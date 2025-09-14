<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Notifications\VisitorRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

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
        
        // Add check-in time and meeting ID
        $validated['check_in_time'] = now();
        $validated['meeting_id'] = Str::uuid();
        
        // Create visitor record
        $visitor = Visitor::create($validated);
        
        // Generate QR code
        $qrCodePath = $this->generateQrCode($visitor);
        
        // Send email notification with QR code to host
        if ($visitor->host_email) {
            try {
                // Using Mail facade directly for immediate sending without queue
                \Illuminate\Support\Facades\Mail::to($visitor->host_email)
                    ->send(new \App\Mail\VisitorRegistered($visitor, $qrCodePath));
                
                // Mark email as sent
                $visitor->update(['email_sent' => true]);
                
                // Log success
                \Illuminate\Support\Facades\Log::info('Email sent to host: ' . $visitor->host_email);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send visitor notification: ' . $e->getMessage());
                // Continue anyway - don't let email issues stop the registration
            }
        }
        
        // Clear the form data for this session
        $sessionId = $request->input('session_id');
        DB::table('form_sessions')->where('session_id', $sessionId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Please wait for your host to receive you.'
        ]);
    }
    
    /**
     * Generate QR code for a visitor.
     *
     * @param  \App\Models\Visitor  $visitor
     * @return string
     */
    private function generateQrCode(Visitor $visitor)
    {
        $meetingUrl = url('/meetings/' . $visitor->meeting_id);
        $fileName = 'qrcode_' . $visitor->meeting_id . '.png';
        
        // Try to use storage/app/public directory first (if symbolic link exists)
        $storagePath = public_path('storage/qrcodes/' . $fileName);
        $directPath = public_path('qrcodes/' . $fileName);
        
        // Determine which path to use
        $path = file_exists(public_path('storage')) ? $storagePath : $directPath;
        $directory = file_exists(public_path('storage')) ? public_path('storage/qrcodes') : public_path('qrcodes');
        
        // Ensure directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate QR code using Endroid QR Code
        $qrCode = new QrCode(
            $meetingUrl,
            encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10
        );
            
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Save QR code to file
        file_put_contents($path, $result->getString());
            
        return $path;
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
            ->where('updated_at', '>=', Carbon::now()->subMinutes(30))
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
            ->where('updated_at', '>=', Carbon::now()->subMinutes(30))
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'activeSessions' => $activeSessions,
            'count' => $activeSessions->count()
        ]);
    }
    
    /**
     * Export visitors to Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'visitors-' . date('Y-m-d') . '.xlsx';
        
        // Add date range to filename if provided
        if ($startDate && $endDate) {
            $filename = 'visitors-' . $startDate . '-to-' . $endDate . '.xlsx';
        } elseif ($endDate) {
            $filename = 'visitors-until-' . $endDate . '.xlsx';
        }
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VisitorsExport($startDate, $endDate), 
            $filename
        );
    }
}
