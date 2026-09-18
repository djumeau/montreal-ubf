<?php

namespace App\Http\Controllers;

use App\Enums\InquiryType;
use App\Enums\Role;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContactController extends Controller
{

    // @desc Show the contact page
    // @route GET /contact
    public function index(): View
    {
        session(['contact_form_rendered_at' => now()->timestamp]);

        return view('pages.contact', ['inquiryOptions' => $this->inquiryOptions()]);
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

        Inquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user' => Auth::check(),
            'inquiry' => $validated['inquiring_about'],
            'message' => $validated['message'],
        ]);

        return back()->with('status', __('contact.status_received'));
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
