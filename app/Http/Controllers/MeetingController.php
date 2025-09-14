<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

class MeetingController extends Controller
{
    /**
     * Display meeting details based on meeting ID.
     *
     * @param  string  $meetingId
     * @return \Illuminate\View\View
     */
    public function show($meetingId)
    {
        $visitor = Visitor::where('meeting_id', $meetingId)->firstOrFail();
        
        // Generate QR code for inline display
        $qrCode = null;
        try {
            // Generate QR code using Endroid QR Code
            $qrCode = new QrCode(
                url('/meetings/' . $meetingId),
                encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 200,
                margin: 10
            );
                
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Get QR code as base64 encoded string
            $qrCode = base64_encode($result->getString());
        } catch (\Exception $e) {
            // Just continue without QR code if it fails
        }
        
        return view('meetings.details', compact('visitor', 'qrCode'));
    }
}
