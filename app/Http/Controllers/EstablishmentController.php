<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class EstablishmentController extends Controller
{
    public function show(mixed $vendor): View
    {
        unset($vendor);

        return view('welcome');
    }
}
