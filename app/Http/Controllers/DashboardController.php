<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Stakeholder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get stakeholders without recent communications
        $threshold = Setting::getValue('communication_alert_threshold', 30);
        $thresholdDate = now()->subDays($threshold);

        $stakeholdersNeedingCommunication = Stakeholder::whereDoesntHave('communications', function ($query) use ($thresholdDate) {
            $query->where('meeting_date', '>=', $thresholdDate);
        })->get();

        return view('dashboard', compact('stakeholdersNeedingCommunication', 'threshold'));
    }
}
