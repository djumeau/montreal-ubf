<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\BibleBook;
use App\Models\BibleStudy;
use App\Models\StudySeries;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // @desc Show the user dashboard page
    // @route GET /dashboard
    public function index(): View
    {
        $user = Auth::user();
        return view('pages.dashboards.profile', compact('user'));
    }

    // @desc Show the manage users page
    // @route GET /manage-users
    public function manageUsers(): View
    {
        $user = Auth::user();
        $users = User::orderBy('name')->paginate(10);
        $adminCount = User::where('role', Role::ADMIN)->count();
        return view('pages.dashboards.manage-users', compact('user', 'users', 'adminCount'));
    }

    // @desc Show the manage series page
    // @route GET /manage-series
    public function manageSeries(): View
    {
        $user = Auth::user();
        $seriesList = StudySeries::withCount('bibleStudies')
            ->with('book')
            ->orderBy('id')
            ->paginate(10);
        $books = BibleBook::orderBy('id')->get(); // Canonical order, for the Related Book select
        return view('pages.dashboards.manage-series', compact('user', 'seriesList', 'books'));
    }

    // @desc Show the manage studies page
    // @route GET /manage-studies
    public function manageStudies(Request $request): View
    {
        $user = Auth::user();

        // Optional ?series={id} filter (e.g. from the Studies button on Manage Series); ignored if the series doesn't exist
        $currentSeries = StudySeries::find($request->integer('series')) ?: null;

        // Optional ?book={id} filter; ignored if the book doesn't exist
        $currentBook = BibleBook::find($request->integer('book')) ?: null;

        // Optional ?q= search; every word must match a title, the passage or the book name (e.g. "Jean 3")
        $search = trim($request->string('q'));

        $studies = BibleStudy::with([
                'series',
                'book',
                'attachments' => fn ($query) => $query->orderBy('filename')->orderBy('extension'), // For the Attachments modal
            ])
            ->withCount('attachments')
            ->filter($currentSeries, $currentBook, $search)
            ->orderBy('id')
            ->paginate(5)
            ->withQueryString(); // Keep ?series= on the pagination links

        // Study counts show next to each name in the filter dropdowns, e.g. "The Gospel of John (7)"
        $seriesList = StudySeries::withCount('bibleStudies')->orderBy('id')->get(); // For the series filter and the dialog's Series select

        $books = BibleBook::withCount('bibleStudies')->orderBy('id')->get(); // Canonical order, for the Book select

        return view('pages.dashboards.manage-studies', compact('user', 'studies', 'currentSeries', 'currentBook', 'search', 'seriesList', 'books'));
    }

}
