<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BibleController extends Controller
{
    public function index()
    {
        $current_book = "John";
        $current_chapter = 1;
        $verses = "1-4";

        return view('resources.bible.index', compact('current_book', 'current_chapter', 'verses'));
    }
}
