<?php

namespace App\Http\Controllers;

use App\Models\StakeholderCommunication;
use App\Models\Stakeholder;
use Illuminate\Http\Request;

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
        return view('stakeholder-communications.edit', compact('stakeholder', 'communication'));
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
            'follow_up_date' => 'nullable|date|after:meeting_date'
        ]);

        $communication->update($validated);

        return redirect()->route('stakeholder-communications.index', $stakeholder)
            ->with('success', 'Communication record updated successfully.');
    }

    public function destroy(Stakeholder $stakeholder, StakeholderCommunication $communication)
    {
        $communication->delete();
        return redirect()->route('stakeholder-communications.index', $stakeholder)
            ->with('success', 'Communication record deleted successfully.');
    }
}
