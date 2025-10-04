<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Stakeholder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role;
        
        // For receptionist, show a different dashboard
        if ($userRole === 'receptionist') {
            // Get recent visitors for receptionist dashboard with pagination
            $visitors = \App\Models\Visitor::orderBy('check_in_time', 'desc')
                ->paginate(20);
                
            return view('dashboard.receptionist', compact('visitors'));
        }

        // redirect contract creators to contracts page
        if ($userRole === 'contract_creator') {
            return redirect()->route('contracts.index');
        }
        
        $isAdmin = $userRole === 'admin';
        $threshold = Setting::getValue('communication_alert_threshold', 30);
        $thresholdDate = now()->subDays($threshold);
        
        // Get stakeholders without recent communications
        $stakeholdersNeedingCommunication = Stakeholder::whereDoesntHave('communications', function ($query) use ($thresholdDate) {
            $query->where('meeting_date', '>=', $thresholdDate);
        })->get();
        
        if ($isAdmin) {
            // Stats for admin
            $totalUsers = User::count();
            $todayUsers = User::whereDate('created_at', today())->count();
            $recentUsers = User::latest()->take(5)->get();
            $showUserStats = true;
            $showNotifications = true;
        } else {
            // Stats for regular users
            $totalUsers = 0;
            $todayUsers = 0;
            $recentUsers = collect();
            $showUserStats = false;
            $showNotifications = true; // Show notifications for all users
        }

        return view('dashboard', compact(
            'stakeholdersNeedingCommunication', 
            'threshold',
            'totalUsers',
            'todayUsers',
            'recentUsers',
            'showUserStats',
            'showNotifications',
            'isAdmin'
        ));
    }
}
