<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    /**
     * Display the visitor registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('visitors.register');
    }
    
    /**
     * Store a new visitor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'card_no' => 'nullable|string|max:50',
            'contact_number' => 'required|string|max:20',
            'host_name' => 'required|string|max:255',
            'host_email' => 'required|email|max:255',
        ]);
        
        // Add check-in time
        $validated['check_in_time'] = now();
        
        // Create visitor record
        Visitor::create($validated);
        
        // Clear the form data for this session
        $sessionId = $request->input('session_id');
        DB::table('form_sessions')->where('session_id', $sessionId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Please wait for your host to receive you.'
        ]);
    }
    
    /**
     * Update form data in real-time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFormData(Request $request)
    {
        $sessionId = $request->input('session_id');
        $formData = $request->input('form_data');
        
        // Store or update the form data
        DB::table('form_sessions')->updateOrInsert(
            ['session_id' => $sessionId],
            [
                'form_data' => json_encode($formData),
                'updated_at' => now()
            ]
        );
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Update form data from receptionist side.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFormDataByReceptionist(Request $request)
    {
        $sessionId = $request->input('session_id');
        $formData = $request->input('form_data');
        
        // Add a flag indicating this was updated by receptionist
        foreach ($formData as $key => $field) {
            if (is_array($field) && isset($field['name']) && $field['name'] !== '_token' && $field['name'] !== 'session_id') {
                // Mark this field as receptionist-edited if it has a value
                if (!empty($field['value'])) {
                    $formData[$key]['receptionist_edit'] = true;
                }
            }
        }
        
        // Store or update the form data
        DB::table('form_sessions')->updateOrInsert(
            ['session_id' => $sessionId],
            [
                'form_data' => json_encode($formData),
                'updated_at' => now(),
                'receptionist_edit' => true
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Visitor information updated successfully']);
    }
    
    /**
     * Display the receptionist view.
     *
     * @return \Illuminate\View\View
     */
    public function showReceptionistView()
    {
        $visitors = Visitor::orderBy('check_in_time', 'desc')
                          ->take(10)
                          ->get();
                          
        // Get all active session IDs
        $activeSessions = DB::table('form_sessions')
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return view('visitors.receptionist', compact('visitors', 'activeSessions'));
    }
    
    /**
     * Get the form data for a specific session.
     *
     * @param  string  $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormData($sessionId)
    {
        $sessionData = DB::table('form_sessions')
            ->where('session_id', $sessionId)
            ->first();
            
        if ($sessionData) {
            return response()->json([
                'success' => true,
                'form_data' => json_decode($sessionData->form_data),
                'updated_at' => $sessionData->updated_at,
                'receptionist_edit' => (bool)$sessionData->receptionist_edit
            ]);
        }
        
        return response()->json([
            'success' => false
        ]);
    }
    
    /**
     * Get all active sessions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveSessions()
    {
        $activeSessions = DB::table('form_sessions')
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'activeSessions' => $activeSessions,
            'count' => $activeSessions->count()
        ]);
    }
}
