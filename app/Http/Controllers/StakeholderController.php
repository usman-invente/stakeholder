<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use Illuminate\Http\Request;

class StakeholderController extends Controller
{
    public function index()
    {
        $stakeholders = Stakeholder::paginate(10);
        return view('stakeholders.index', compact('stakeholders'));
    }

    public function create()
    {
        return view('stakeholders.create');
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
            'notes' => 'nullable|string'
        ], $messages);

        Stakeholder::create($validated);

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder created successfully.');
    }

    public function show(Stakeholder $stakeholder)
    {
        return view('stakeholders.show', compact('stakeholder'));
    }

    public function edit(Stakeholder $stakeholder)
    {
        return view('stakeholders.edit', compact('stakeholder'));
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
            'notes' => 'nullable|string'
        ], $messages);

        $stakeholder->update($validated);

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
