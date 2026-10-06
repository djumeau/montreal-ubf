<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\ManageInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    // @desc Show the manage inquiries page: messages sent through the contact page, newest first, with their follow-up
    // @route GET /manage-inquiries
    public function index(Request $request): View
    {
        // Messages hold personal information: refused below the management roles, not only hidden in the page
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $user = $request->user();

        // Every message in one list, newest first, 5 per page, with its follow-up (read, answered, by whom, note)
        $inquiries = Inquiry::with('management.answeredBy')
            ->latest()
            ->paginate(5)
            ->withQueryString()
            ->fragment('inquiries');

        return view('pages.dashboards.manage-inquiries', compact('user', 'inquiries'));
    }

    // @desc Mark a message as read, when it is opened in the Inquiry modal (sent in the background, answers JSON)
    // @route POST /manage-inquiries/{inquiry}/read
    public function markRead(Request $request, Inquiry $inquiry): JsonResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Its follow-up is created the first time the message is handled; an earlier read date is kept
        $management = ManageInquiry::firstOrNew(['inquiry_id' => $inquiry->id]);
        $management->read_at ??= now();
        $management->save();

        return response()->json(['readAt' => self::dateTime($management->read_at)]);
    }

    // @desc Record the answer to a message (Answer section of the Inquiry modal): who answered, when, and the note
    // @route PUT /manage-inquiries/{inquiry}/answer
    public function answer(Request $request, Inquiry $inquiry): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        // The first answer sets when and by whom (the signed-in Elder or Administrator); saving again only changes the note
        $management = ManageInquiry::firstOrNew(['inquiry_id' => $inquiry->id]);
        $management->read_at ??= now();
        $management->answered_at ??= now();
        $management->answered_by ??= $request->user()->id;
        $management->note = $validated['note'] ?? null;
        $management->save();

        return back()->with('status', __('dashboard/manage-inquiries/index.answer_saved'));
    }

    // @desc Undo the answer to a message (clicking its "Answered" badge): back to read; the note is kept
    // @route PUT /manage-inquiries/{inquiry}/unanswer
    public function unanswer(Request $request, Inquiry $inquiry): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $inquiry->management?->update(['answered_at' => null, 'answered_by' => null]);

        return back()->with('status', __('dashboard/manage-inquiries/index.answer_undone'));
    }

    /**
     * Date and time in the current language, as on the page: "Thursday, October 1st, 2026, 4:40 PM" / "Jeudi 1 octobre 2026, 16 h 40".
     */
    private static function dateTime($date): string
    {
        return ucfirst($date->isoFormat(__('bible-study-schedule/index.list_day_format')))
            . ', ' . $date->isoFormat(__('bible-study-schedule/index.time_format'));
    }

    // @desc Delete the messages ticked on the page (Delete Selected), with their follow-ups
    // @route DELETE /manage-inquiries
    public function destroySelected(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validate([
            'inquiries' => ['required', 'array'],
            'inquiries.*' => ['integer', 'exists:inquiries,id'],
        ]);

        // One by one, as for a single delete, so each follow-up goes with its message
        $deleted = 0;
        foreach (Inquiry::whereIn('id', $validated['inquiries'])->get() as $inquiry) {
            $inquiry->delete();
            $deleted++;
        }

        return back()->with('status', trans_choice('dashboard/manage-inquiries/index.inquiries_deleted', $deleted, ['count' => $deleted]));
    }

    // @desc Delete a contact form message, with its follow-up (manage_inquiries row, removed by the foreign key)
    // @route DELETE /manage-inquiries/{inquiry}
    public function destroy(Request $request, Inquiry $inquiry): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $inquiry->delete();

        return back()->with('status', __('dashboard/manage-inquiries/index.inquiry_deleted', ['name' => $inquiry->name]));
    }
}
