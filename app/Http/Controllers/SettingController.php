<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'communication_alert_threshold' => 'required|integer|min:1|max:365'
        ]);

        Setting::where('key', 'communication_alert_threshold')
            ->update(['value' => $request->communication_alert_threshold]);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    }
}
