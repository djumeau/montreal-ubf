<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\StudySeries;

class StudySeriesController extends Controller
{
    // @desc Show the study-series page
    // @route GET /study-series
    public function index(): View
    {
        $study_series = StudySeries::all();
        return view('study-series', compact('study_series'));
    }
}
