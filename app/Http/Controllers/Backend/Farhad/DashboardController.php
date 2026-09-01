<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Models\User;
use App\Models\ContactMessage;
use App\Models\Catalogue;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalMessages = ContactMessage::count();
        $totalCatalogues = Catalogue::count();
        
        // Orders & Revenue metrics
        $totalOrders = Purchase::count();
        $netRevenue = Purchase::whereIn('payment_status', ['paid', 'partially_refunded'])->sum('amount') 
            - Purchase::sum('refunded_amount');
        $pendingRefunds = Purchase::where('refund_request_status', 'pending')->count();

        // Calculate Monthly Net Revenue for ApexCharts (Database-agnostic)
        $salesThisYear = Purchase::whereYear('created_at', date('Y'))
            ->whereIn('payment_status', ['paid', 'partially_refunded'])
            ->get();

        $monthlySales = array_fill(1, 12, 0);
        foreach ($salesThisYear as $purchase) {
            $month = $purchase->created_at->month;
            $netAmount = floatval($purchase->amount) - floatval($purchase->refunded_amount);
            $monthlySales[$month] += $netAmount;
        }
        $chartData = array_values($monthlySales);

        // Fetch Recent Orders (Purchases)
        $recentOrders = Purchase::with(['user', 'catalogue', 'membershipPackage'])->latest()->limit(5)->get();

        // Fetch Recent Support/Contact Messages
        $recentMessages = ContactMessage::latest()->limit(5)->get();

        return view('backend.layouts.dashboard.index', compact(
            'totalUsers',
            'totalMessages',
            'totalCatalogues',
            'totalOrders',
            'netRevenue',
            'pendingRefunds',
            'chartData',
            'recentOrders',
            'recentMessages'
        ));
    }
}


