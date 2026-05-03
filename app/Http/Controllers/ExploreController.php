<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(Request $request): View
    {
        // Fetch all vendors (these are the BiteSpots shown on map + grid)
        $spots = Vendor::all();

        // Plain array for the Blade view (grid tab uses $spot['name'] etc.)
        $bitespots = $spots->map(function ($spot) {
            return [
                'id'        => $spot->id,
                'name'      => $spot->name,
                'category'  => $spot->category ?? '',
                'location'  => $spot->address   ?? '',
                'rating'    => $spot->rating     ?? 0,
                'image_url' => $spot->image_url  ?? null,
                'latitude'  => $spot->latitude   ?? null,
                'longitude' => $spot->longitude  ?? null,
            ];
        });

        // Separate array for Leaflet JS — only spots that have coordinates
        $mapspotsJson = $bitespots
            ->filter(fn($s) => $s['latitude'] !== null && $s['longitude'] !== null)
            ->map(fn($s) => [
                'id'        => $s['id'],
                'name'      => $s['name'],
                'category'  => $s['category'],
                'location'  => $s['location'],
                'rating'    => (float) $s['rating'],
                'image_url' => $s['image_url'],
                'lat'       => (float) $s['latitude'],
                'lng'       => (float) $s['longitude'],
            ])
            ->values()
            ->toArray();

        return view('pages.explore', [
            'bitespots'    => $bitespots,
            'mapspotsJson' => $mapspotsJson,
        ]);
    }
}