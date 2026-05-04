<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the bidder dashboard.
     */
    public function index(): View
    {
        return view('bidder.dashboard');
    }
}
