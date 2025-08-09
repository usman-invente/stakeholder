<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToUserStakeholders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If the user is admin, allow access to all stakeholders
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        // If there's a stakeholder parameter in the route
        if ($request->route('stakeholder')) {
            $stakeholder = $request->route('stakeholder');
            
            // Check if the user is assigned to this stakeholder
            $isAssigned = $stakeholder->users()->where('user_id', Auth::id())->exists();
            
            if (!$isAssigned) {
                return redirect()->route('stakeholders.index')
                    ->with('error', 'You do not have permission to access this stakeholder.');
            }
        }

        // If there's a communication parameter in the route
        if ($request->route('communication')) {
            $communication = $request->route('communication');
            
            // Check if the user is assigned to this communication
            $isAssigned = $communication->users()->where('user_id', Auth::id())->exists();
            
            if (!$isAssigned) {
                return redirect()->route('stakeholders.index')
                    ->with('error', 'You do not have permission to access this communication.');
            }
        }

        return $next($request);
    }
}
