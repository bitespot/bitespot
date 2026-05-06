<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(Request $request): View
    {
        $spots = Vendor::with('category')->where('status', 'approved')->get();

        $bitespots = $spots->map(fn($spot) => [
            'id'            => $spot->id,
            'slug'          => $spot->slug ?? (string) $spot->id,
            'name'          => $spot->business_name,
            'category'      => $spot->category?->name ?? '',
            'category_slug' => $spot->category?->slug ?? '',
            'city'          => $spot->city ?? '',
            'price_tier'    => $spot->price_tier_label ?? '',
            'rating'        => $spot->avg_rating !== null ? (float) $spot->avg_rating : null,
            'image_url'     => $spot->primary_photo,
            'lat'           => $spot->lat !== null ? (float) $spot->lat : null,
            'lng'           => $spot->lng !== null ? (float) $spot->lng : null,
        ]);

        // All vendors as JSON for JS (sidebar list + grid view)
        $allVendorsJson = $bitespots->values()->toArray();

        // Map-only subset: vendors that have coordinates
        $mapspotsJson = $bitespots
            ->filter(fn($s) => $s['lat'] !== null && $s['lng'] !== null)
            ->values()
            ->toArray();

        return view('pages.explore', [
            'bitespots'     => $bitespots,
            'allVendorsJson'=> $allVendorsJson,
            'mapspotsJson'  => $mapspotsJson,
        ]);
    }
}