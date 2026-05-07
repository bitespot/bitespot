<?php

namespace App\Http\Controllers;

use App\Models\BiteSpot;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch the latest spots for the main feed
        $posts = BiteSpot::with(['user', 'likes', 'saves'])->latest()->get();

        return view('dashboard', compact('posts'));
    }

    public function saved()
    {
        // Fetch ONLY the spots the currently logged-in user has saved
        $posts = auth()->user()->savedBiteSpots()->with(['user', 'likes', 'saves'])->latest()->get();
        
        return view('pages.saved', compact('posts'));
    }
}