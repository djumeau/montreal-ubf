<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BibleController extends Controller
{
    // @desc Show the bible-books page
    // @route GET /bible-studies
    public function index()
    {
        // Fetches the entire array from lang/{locale}/bible.php matching current middleware locale
        $bibleBooks = __('bible');

        // 2. Convert to a collection and group by the 'testament' key ('ot' and 'nt')
        $testaments = collect($bibleBooks)->groupBy('testament');

        // 3. Pass the grouped collection to the view
        return view('bible-books', compact('testaments'));
    }
}
