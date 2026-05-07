<?php

namespace App\Http\Controllers;

class SavedController extends Controller
{
    public function index()
    {
        $bookmarks = auth()->user()->bookmarks()
            ->with(['vendor.category'])
            ->latest()
            ->get();

        return view('pages.saved', compact('bookmarks'));
    }
}
