<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Catalogue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /**
     * Display the public development catalogue.
     */
    public function index()
    {
        // Eager load active features for active catalogue offerings
        $items = Catalogue::with('features')
            ->where('status', 1)
            ->latest()
            ->get();

        return view('frontend.catalogue', compact('items'));
    }
}
