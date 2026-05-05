<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Vendor;
use Illuminate\View\View;

class EstablishmentController extends Controller
{
    public function show(Vendor $vendor): View
    {
        if ($vendor->status !== 'approved') {
            abort(404);
        }

        $vendor->load('category');

        $isBookmarked = false;
        if (auth()->check()) {
            $isBookmarked = Bookmark::where('user_id', auth()->id())
                ->where('vendor_id', $vendor->id)
                ->exists();
        }

        return view('pages.place', compact('vendor', 'isBookmarked'));
    }
}
