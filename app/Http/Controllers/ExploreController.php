<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExploreController extends Controller
{
	public function index()
	{
		// TODO: Replace with real data from the database
		$bitespots = [];
		return view('pages.explore', compact('bitespots'));
	}
}
