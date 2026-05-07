<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BiteSpotController extends Controller
{
    public function create(Request $request)
    {
        return view('bitespot.create');
    }
}
