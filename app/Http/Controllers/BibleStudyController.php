<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;

use App\Models\BibleStudy;

class BibleStudyController extends Controller
{
    // @desc Show all bible studies
    // @route GET /bible-studies
    public function index(): View
    {
        $biblestudies = BibleStudy::all();

        return view('pages.bible-studies.index')->with('biblestudies', $biblestudies);
    }

    // @desc Show bible study id
    // @route GET /bible-studies/{id}
    public function show(int $id):View
    {
        $biblestudy = BibleStudy::findOrFail($id);

        return view('pages.bible-studies.show')->with('biblestudy', $biblestudy);
    }

    // @desc Create bible study lesson
    // @route GET /bible-studies/create
    public function create(): View
    {
        return view('pages.bible-studies.create');
    }

    // @desc Save bible study lesson
    // @route POST /bible-studies/store
    public function store(Request $request)
    {
        $validatedData = $request->validate([



        ]);

        // $biblestudy = new BibleStudy();
        // $biblestudy->title = json_encode($validatedData['title']);
        // $biblestudy->content = json_encode($validatedData['content']);
        // $biblestudy->save();

        // return redirect()->route('bible-studies.show', ['id' => $biblestudy->id]);
    }

}
