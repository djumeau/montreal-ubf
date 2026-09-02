<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\BibleBook;

class BibleBookController extends Controller
{
    // @desc Show the bible-books page
    // @route GET /bible-books
    public function index()
    {
        // Fetches the entire array from lang/{locale}/bible.php matching current middleware locale
        $bible_books = BibleBook::all();

        // 3. Pass the grouped collection to the view
        return view('bible-books', compact('bible_books'));
    }
}
