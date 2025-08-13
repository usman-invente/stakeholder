<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    /**
     * Send a test email to verify mail configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function sendTestEmail()
    {
        try {
            $email = 'usman.traximtech@gmail.com';
            
            Mail::raw('This is a test email from Stakeholder Management System.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email from Stakeholder Management System');
            });
            
            return response()->json([
                'success' => true,
                'message' => "Test email sent successfully to {$email}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
