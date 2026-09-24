<?php

namespace App\Http\Controllers;

use App\Models\BibleStudy;
use Illuminate\View\View;

class HomeController extends Controller
{
    // @desc Show home page (index)
    // @route GET /
    public function index(): View
    {
        // Study featured under the hero; its question sheets are linked in the current language
        $featuredStudy = BibleStudy::find(__('home/study.studyId'));

        return view('pages.index', [
            'questionSheets' => $featuredStudy?->localizedAttachments('question_sheet')->keyBy('extension') ?? collect(),
        ]);
    }
}
