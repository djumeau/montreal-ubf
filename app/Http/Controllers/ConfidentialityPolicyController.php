<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfidentialityPolicyController extends Controller
{
    // @desc Show the user confidentiality policy page
    // @route GET /dashboard
    public function index(): View
    {
        return view('pages.confidentiality-policy');
    }
}
