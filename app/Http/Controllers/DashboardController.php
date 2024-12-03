<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $transaksi_count = Transaksi::count();
        return view('dashboard', compact('transaksi_count') );
    }
}
