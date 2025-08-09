<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $stakeholders = \App\Models\Stakeholder::all();
        $assignedStakeholders = [];
        return view('users.create', compact('stakeholders', 'assignedStakeholders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'stakeholders' => 'nullable|array',
            'stakeholders.*' => 'exists:stakeholders,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);
        
        // Attach stakeholders if selected
        if ($request->has('stakeholders')) {
            $user->stakeholders()->attach($request->stakeholders);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $stakeholders = \App\Models\Stakeholder::all();
        $assignedStakeholders = $user->stakeholders->pluck('id')->toArray();
        return view('users.edit', compact('user', 'stakeholders', 'assignedStakeholders'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
            'stakeholders' => 'nullable|array',
            'stakeholders.*' => 'exists:stakeholders,id',
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validated = $request->validate($rules);
        
        // Remove password from validated data if it's not provided
        if (!$request->filled('password')) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }
        
        // Remove stakeholders from validated data before updating user
        unset($validated['stakeholders']);
        
        $user->update($validated);
        
        // Sync stakeholders
        if ($request->has('stakeholders')) {
            $user->stakeholders()->sync($request->stakeholders);
        } else {
            $user->stakeholders()->detach();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
