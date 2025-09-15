<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEscalation ? 'URGENT: Visitor Card Not Returned' : 'Reminder: Visitor Card Not Returned' }}</title>
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
            background: {{ $isEscalation ? '#dc3545' : '#ffc107' }};
            color: {{ $isEscalation ? 'white' : 'black' }};
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
        .urgent {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $isEscalation ? 'URGENT: Visitor Card Not Returned' : 'Reminder: Visitor Card Not Returned' }}</h1>
        </div>
        
        <p>Hello,</p>
        
        @if($isEscalation)
            <p class="urgent">This is an urgent notification regarding an unreturned visitor card.</p>
            <p>After multiple follow-up attempts, visitor {{ $visitor->full_name }} has not returned their access card.</p>
            <p>This requires immediate attention as it is a security concern.</p>
            <p>Number of follow-up attempts: {{ $visitor->follow_up_count }}</p>
        @else
            <p>This is a notification about an unreturned visitor card.</p>
            <p>Visitor {{ $visitor->full_name }} has checked out but has not returned their access card.</p>
            <p>Please follow up with the visitor to retrieve the card.</p>
        @endif
        
        <div class="details">
            <h2>Visitor Information</h2>
            
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span>{{ $visitor->full_name }}</span>
            </div>
            
            @if($visitor->email)
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span>{{ $visitor->email }}</span>
            </div>
            @endif
            
            @if($visitor->contact_number)
            <div class="detail-row">
                <span class="detail-label">Contact Number:</span>
                <span>{{ $visitor->contact_number }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Card Number:</span>
                <span>{{ $visitor->card_no ?: 'Not recorded' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-in Time:</span>
                <span>{{ $visitor->check_in_time->format('F j, Y, g:i a') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Check-out Time:</span>
                <span>{{ $visitor->check_out_time ? $visitor->check_out_time->format('F j, Y, g:i a') : 'Not checked out' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Host:</span>
                <span>{{ $visitor->host_name }}</span>
            </div>
        </div>
        
        <!-- <div style="text-align: center;">
            <a href="{{ $viewUrl }}" class="action-button">View Visitor Details</a>
        </div> -->
        
        <div class="footer">
            <p>Thank you for your attention to this matter.</p>
        </div>
    </div>
</body>
</html>