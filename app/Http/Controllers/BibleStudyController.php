<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View as IlluminateView;

use App\Models\BibleStudy;

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

    public function create():IlluminateView
    {
        return view('pages.bible-study.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([

            'study_series_id' => 'nullable|string',

            'book_id' => 'required|integer',

            'title.en' => 'required|string|max:255',
            'title.fr' => 'required|string|max:255',

            'image_links.en' => 'nullable|string',
            'image_links.fr' => 'nullable|string',

            'bible_passage.en' => 'required|string|max:255',
            'bible_passage.fr' => 'required|string|max:255',

            'passage_links.en' => 'nullable|string|max:255',
            'passage_links.fr' => 'nullable|string|max:255',

            'question_sheet.en' => 'nullable|string|max:255',
            'question_sheet.fr' => 'nullable|string|max:255',

            'lecture.en' => 'nullable|string|max:255',
            'lecture.fr' => 'nullable|string|max:255',

        ]);

        // $biblestudy = new BibleStudy();
        // $biblestudy->title = json_encode($validatedData['title']);
        // $biblestudy->content = json_encode($validatedData['content']);
        // $biblestudy->save();

        // return redirect()->route('bible-studies.show', ['id' => $biblestudy->id]);
    }

}
