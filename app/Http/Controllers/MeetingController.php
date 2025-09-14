<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        
        // Generate QR code SVG for inline display
        $qrCode = null;
        try {
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->errorCorrection('H')
                ->encoding('UTF-8')
                ->generate(url('/meetings/' . $meetingId)));
        } catch (\Exception $e) {
            // Just continue without QR code if it fails
        }
        
        return view('meetings.details', compact('visitor', 'qrCode'));
    }
}
