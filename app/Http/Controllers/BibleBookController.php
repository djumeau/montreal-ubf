<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\BibleBook;

class BibleBookController extends Controller
{
    // @desc Show the bible-books page
    // @route GET /bible-books
    public function index(): View
    {
        // Fetches the entire array from lang/{locale}/bible.php matching current middleware locale
        $ot = BibleBook::where('testament', 'ot')->get();
        $nt = BibleBook::where('testament', 'nt')->get();

        // 3. Pass the grouped collection to the view
        return view('bible-books', compact('ot', 'nt'));
    }
}
