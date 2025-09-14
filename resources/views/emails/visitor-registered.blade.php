<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Visitor Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #3490dc;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }
        .details {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
        }
        .action-button {
            display: inline-block;
            background: #3490dc;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Visitor Registration</h1>
        </div>
        
        <p>Hello {{ $visitor->host_name }},</p>
        
        <p>You have a new visitor waiting for you at reception.</p>
        
        <div class="details">
            <h2>Visitor Information</h2>
            
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span>{{ $visitor->full_name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span>{{ $visitor->email ?? 'Not provided' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Contact Number:</span>
                <span>{{ $visitor->contact_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-in Time:</span>
                <span>{{ $visitor->check_in_time->format('F j, Y, g:i a') }}</span>
            </div>
        </div>
        
        <p>Please use the QR code attached to this email or click the button below to view the meeting details.</p>
        
        <div style="text-align: center;">
            <a href="{{ $meetingUrl }}" class="action-button">View Meeting Details</a>
        </div>
        
        <div class="footer">
            <p>Thank you for using our visitor management system.</p>
        </div>
    </div>
</body>
</html>