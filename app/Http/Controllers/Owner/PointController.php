<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class PointController extends Controller
{
    /**
     * Sistem point dah TUTUP — gantikan dengan model subscription bulanan
     * (had cawangan + kerusi). Redirect ke page subscription supaya pautan
     * lama (kalau ada) tak error.
     * Route: GET /owner/points
     */
    public function index()
    {
        return redirect()->route('owner.subscription.index');
    }
}
