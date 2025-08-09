<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StakeholderController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            // Admin sees all stakeholders
            $stakeholders = Stakeholder::paginate(10);
        } else {
            // Regular user sees only assigned stakeholders
            $stakeholders = Auth::user()->stakeholders()->paginate(10);
        }
        
        return view('stakeholders.index', compact('stakeholders'));
    }

    public function create()
    {
        $users = \App\Models\User::where('role', 'user')->get();
        return view('stakeholders.create', compact('users'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'The stakeholder name is required.',
            'name.max' => 'The stakeholder name cannot exceed 255 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'organization.required' => 'The organization name is required.',
            'organization.max' => 'The organization name cannot exceed 255 characters.',
            'position.max' => 'The position cannot exceed 255 characters.',
            'type.required' => 'Please select a stakeholder type.',
            'type.in' => 'Please select either Internal or External type.'
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stakeholders',
            'phone' => 'nullable|string|max:20',
            'organization' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'required|in:internal,external',
            'notes' => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ], $messages);

        $stakeholder = Stakeholder::create($validated);
        
        // Attach users if selected
        if ($request->has('users')) {
            $stakeholder->users()->attach($request->users);
        }

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder created successfully.');
    }

    public function show(Stakeholder $stakeholder)
    {
        return view('stakeholders.show', compact('stakeholder'));
    }

    public function edit(Stakeholder $stakeholder)
    {
        $users = \App\Models\User::where('role', 'user')->get();
        $assignedUsers = $stakeholder->users->pluck('id')->toArray();
        return view('stakeholders.edit', compact('stakeholder', 'users', 'assignedUsers'));
    }

    public function update(Request $request, Stakeholder $stakeholder)
    {
        $messages = [
            'name.required' => 'The stakeholder name is required.',
            'name.max' => 'The stakeholder name cannot exceed 255 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'organization.required' => 'The organization name is required.',
            'organization.max' => 'The organization name cannot exceed 255 characters.',
            'position.max' => 'The position cannot exceed 255 characters.',
            'type.required' => 'Please select a stakeholder type.',
            'type.in' => 'Please select either Internal or External type.'
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stakeholders,email,' . $stakeholder->id,
            'phone' => 'nullable|string|max:20',
            'organization' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'required|in:internal,external',
            'notes' => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ], $messages);

        $stakeholder->update($validated);
        
        // Sync users
        if ($request->has('users')) {
            $stakeholder->users()->sync($request->users);
        } else {
            $stakeholder->users()->detach();
        }

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder updated successfully.');
    }

    public function destroy(Stakeholder $stakeholder)
    {
        $stakeholder->delete();

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder deleted successfully.');
    }
}
