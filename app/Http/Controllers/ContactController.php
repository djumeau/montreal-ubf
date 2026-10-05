<?php

namespace App\Http\Controllers;

use App\Enums\EventCategory;
use App\Enums\InquiryType;
use App\Enums\Role;
use App\Models\Event;
use App\Models\Inquiry;
use App\Notifications\InquiryReceived;
use App\Notifications\InquirySubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{

    // @desc Show the contact page
    // @route GET /contact
    public function index(Request $request): View
    {
        session(['contact_form_rendered_at' => now()->timestamp]);

        return view('pages.contact', [
            'inquiryOptions' => $this->inquiryOptions(),
            'prefill' => $this->prefill($request),
        ]);
    }

    /**
     * Subject and message filled in when arriving from the Bible Study Schedule (/contact?study={event id}):
     * in person asks about the location, online asks to subscribe and attend that study, a Sunday worship service
     * asks for more information under the "Worship Service" subject. Empty for any other visit,
     * and for a study the viewer may not see.
     */
    private function prefill(Request $request): array
    {
        $categories = array_map(fn (EventCategory $category) => $category->value, EventCategory::WITH_BIBLE_STUDY);

        $study = $request->filled('study')
            ? Event::whereIn('category', $categories)->visibleTo($request->user())->find($request->integer('study'))
            : null;

        return match ($study?->category) {
            EventCategory::SUNDAY_SERVICE => [
                'inquiring_about' => InquiryType::WORSHIP->value,
                'message' => __('contact.prefill_service') . $this->studyDetails($study, false),
            ],
            EventCategory::GBS_IN_PERSON => [
                'inquiring_about' => InquiryType::GROUP_STUDY->value,
                'message' => __('contact.prefill_in_person') . $this->studyDetails($study),
            ],
            EventCategory::GBS_ONLINE => [
                'inquiring_about' => InquiryType::SUBSCRIBE->value,
                'message' => __('contact.prefill_online', ['title' => $study->current_title ?: $study->category->label()]) . $this->studyDetails($study, false),
            ],
            default => ['inquiring_about' => '', 'message' => ''],
        };
    }

    // @desc Handle contact form submission
    // @route POST /contact
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: real visitors never see or fill this field, so anything in it means a bot.
        // Bots that fail either check are told "it worked" anyway so they don't learn to adapt.
        $renderedAt = $request->session()->pull('contact_form_rendered_at');

        if ($request->filled('website') || !$renderedAt || now()->timestamp - $renderedAt < 3) {
            return back()->with('status', __('contact.status_received'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50'],
            'inquiring_about' => ['required', 'string', 'in:' . implode(',', array_keys($this->inquiryOptions()))],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user' => Auth::check(),
            'inquiry' => $validated['inquiring_about'],
            'message' => $validated['message'],
        ]);

        // Two emails: the full message to the church, and a confirmation (without the message) to the visitor.
        // The message is already saved for the dashboard, so a mail failure is logged rather than shown to the visitor.
        $emails = [
            fn () => Notification::route('mail', config('mail.from.address'))->notify(new InquirySubmitted($inquiry)),
            fn () => Notification::route('mail', [$inquiry->email => $inquiry->name])->notify(new InquiryReceived($inquiry)),
        ];

        foreach ($emails as $send) {
            try {
                $send();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', __('contact.status_received'));
    }

    /**
     * Lines added under the pre-filled message: the study's title (unless the message already names it),
     * when it takes place and, when it has a location, where.
     */
    private function studyDetails(Event $study, bool $withTitle = true): string
    {
        $lines = array_filter([
            $withTitle && $study->current_title ? __('contact.prefill_study', ['title' => $study->current_title]) : null,
            __('contact.prefill_when', ['when' => $study->schedule_when]),
            $study->schedule_where ? __('contact.prefill_where', ['where' => $study->schedule_where]) : null,
        ]);

        return "\n\n" . implode("\n", $lines);
    }

    /**
     * Options for the "Inquiring About" select, gated by role for authenticated users.
     */
    private function inquiryOptions(): array
    {
        $options = [];

        foreach (InquiryType::cases() as $type) {
            if ($type === InquiryType::PASTORAL && !$this->canRequestPastoralInquiry()) {
                continue;
            }

            $options[$type->value] = $type->label();
        }

        return $options;
    }

    private function canRequestPastoralInquiry(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user !== null && $user->role !== Role::GUEST;
    }

}
