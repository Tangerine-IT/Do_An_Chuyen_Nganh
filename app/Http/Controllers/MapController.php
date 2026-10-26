<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(Request $request): View
    {
        $locations = Location::query()
            ->active()
            ->when($request->filled('type') && $request->string('type') !== 'all', fn ($query) => $query->where('type', $request->string('type')))
            ->orderBy('name')
            ->get();

        return view('map.index', compact('locations'));
    }
}
