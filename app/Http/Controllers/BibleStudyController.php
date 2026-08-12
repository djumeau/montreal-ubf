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
        // $validatedData = $request->validate([
        //     'title' => 'required|string',
        //     'content' => 'required|string',
        // ]);

        // $biblestudy = new BibleStudy();
        // $biblestudy->title = json_encode($validatedData['title']);
        // $biblestudy->content = json_encode($validatedData['content']);
        // $biblestudy->save();

        // return redirect()->route('bible-studies.show', ['id' => $biblestudy->id]);
    }

}
