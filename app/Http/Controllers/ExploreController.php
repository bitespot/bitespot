<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ExploreController extends Controller
{
    public function index(): View
    {
        return view('welcome');
    }
}
