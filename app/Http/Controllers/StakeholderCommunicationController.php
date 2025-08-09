<?php

namespace App\Http\Controllers;

use App\Models\StakeholderCommunication;
use App\Models\Stakeholder;
use App\Exports\StakeholderCommunicationsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StakeholderCommunicationController extends Controller
{
    public function index(Stakeholder $stakeholder)
    {
        $communications = $stakeholder->communications()->with('user')->latest()->paginate(10);
        return view('stakeholder-communications.index', compact('stakeholder', 'communications'));
    }

    public function create(Stakeholder $stakeholder)
    {
        $users = \App\Models\User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();
        
        return view('stakeholder-communications.create', compact('stakeholder', 'users'));
    }

    public function store(Request $request, Stakeholder $stakeholder)
    {
        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'meeting_type' => 'required|in:in-person,video,phone,email',
            'location' => 'nullable|string|max:255',
            'attendees' => 'required|string',
            'discussion_points' => 'required|string',
            'action_items' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:meeting_date',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id'
        ]);

        // Convert time to UTC for storage
        if ($validated['meeting_time']) {
            $meetingTime = \Carbon\Carbon::parse($validated['meeting_time'], 'Africa/Dar_es_Salaam');
            $validated['meeting_time'] = $meetingTime->setTimezone('UTC')->format('H:i:s');
        }

        if ($validated['meeting_date']) {
            $meetingDate = \Carbon\Carbon::parse($validated['meeting_date'], 'Africa/Dar_es_Salaam');
            $validated['meeting_date'] = $meetingDate->setTimezone('UTC')->format('Y-m-d');
        }

        if (!empty($validated['follow_up_date'])) {
            $followUpDate = \Carbon\Carbon::parse($validated['follow_up_date'], 'Africa/Dar_es_Salaam');
            $validated['follow_up_date'] = $followUpDate->setTimezone('UTC')->format('Y-m-d');
        }

        $users = $validated['users'];
        unset($validated['users']);

        $communication = $stakeholder->communications()->create([
            ...$validated,
            'user_id' => auth()->id()
        ]);

        $communication->users()->attach($users);

        return redirect()->route('stakeholder-communications.index', $stakeholder)
            ->with('success', 'Communication record added successfully.');
    }

    public function show(Stakeholder $stakeholder, StakeholderCommunication $communication)
    {
        return view('stakeholder-communications.show', compact('stakeholder', 'communication'));
    }

    public function edit(Stakeholder $stakeholder, StakeholderCommunication $communication)
    {
        $users = \App\Models\User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();
    
        return view('stakeholder-communications.edit', compact('stakeholder', 'communication', 'users'));
    }

    public function update(Request $request, Stakeholder $stakeholder, StakeholderCommunication $communication)
    {
        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'meeting_type' => 'required|in:in-person,video,phone,email',
            'location' => 'nullable|string|max:255',
            'attendees' => 'required|string',
            'discussion_points' => 'required|string',
            'action_items' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:meeting_date',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id'
        ]);

        // Convert time to UTC for storage
        if ($validated['meeting_time']) {
            $meetingTime = \Carbon\Carbon::parse($validated['meeting_time'], 'Africa/Dar_es_Salaam');
            $validated['meeting_time'] = $meetingTime->setTimezone('UTC')->format('H:i:s');
        }

        if ($validated['meeting_date']) {
            $meetingDate = \Carbon\Carbon::parse($validated['meeting_date'], 'Africa/Dar_es_Salaam');
            $validated['meeting_date'] = $meetingDate->setTimezone('UTC')->format('Y-m-d');
        }

        if (!empty($validated['follow_up_date'])) {
            $followUpDate = \Carbon\Carbon::parse($validated['follow_up_date'], 'Africa/Dar_es_Salaam');
            $validated['follow_up_date'] = $followUpDate->setTimezone('UTC')->format('Y-m-d');
        }

        $users = $validated['users'];
        unset($validated['users']);

        $communication->update($validated);
        $communication->users()->sync($users);

        return redirect()->route('stakeholder-communications.index', $stakeholder)
            ->with('success', 'Communication record updated successfully.');
    }

    public function destroy(Stakeholder $stakeholder, StakeholderCommunication $communication)
    {
        $communication->delete();
        return redirect()->route('stakeholder-communications.index', $stakeholder)
            ->with('success', 'Communication record deleted successfully.');
    }

    public function report(Request $request)
    {
        $query = StakeholderCommunication::with(['stakeholder', 'users']);

        // If no date range is selected, default to showing the last 12 months
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('meeting_date', '>=', now()->subMonths(12));
        } else {
            if ($request->filled('start_date')) {
                $query->whereDate('meeting_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('meeting_date', '<=', $request->end_date);
            }
        }

        $communications = $query->orderBy('meeting_date', 'desc') // Show latest months first
                               ->orderBy('meeting_time', 'asc')
                               ->paginate(15)
                               ->withQueryString();

        return view('stakeholder-communications.report', compact('communications'));
    }

    public function export(Request $request)
    {
        $export = new StakeholderCommunicationsExport();
        $export->setDateRange($request->start_date, $request->end_date);
        
        $filename = 'stakeholder-communications-' . now()->format('Y-m-d');
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filename = 'stakeholder-communications-' . $request->start_date . '-to-' . $request->end_date;
        }
        
        return Excel::download($export, $filename . '.xlsx');
    }
}
