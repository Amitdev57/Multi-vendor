<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'items.vendor'])
            ->latest()
            ->get();

        return view('dashboard', compact('orders'));
        }
    }
