<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BibleStudy;
use Illuminate\View\View as IlluminateView;

class BibleStudyController extends Controller
{
    public function index()
    {
        $biblestudies = BibleStudy::all();

        return view('pages.bible-study.index')->with('biblestudies', $biblestudies);
    }

    public function show(int $id):IlluminateView
    {
        $biblestudy = BibleStudy::findOrFail($id);

        return view('pages.bible-study.show')->with('biblestudy', $biblestudy);
    }
}
