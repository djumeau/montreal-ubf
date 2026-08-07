<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BibleStudy;

class BibleStudyController extends Controller
{
    public function index()
    {
        $biblestudies = BibleStudy::all();
        return view('pages.bible-study')->with('biblestudies', $biblestudies);
    }
}
