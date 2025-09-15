<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weekly Visitor Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .stat-box {
            background: white;
            border-radius: 5px;
            padding: 15px;
            margin: 0 10px 10px 0;
            flex: 1 1 calc(50% - 10px);
            min-width: 200px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-title {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #3490dc;
        }
        .warning-value {
            color: #e3342f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .host-stats {
            margin-top: 20px;
        }
        .host-stats h3 {
            margin-bottom: 10px;
        }
        .host-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .host-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weekly Visitor Summary Report</h1>
            <h3>{{ $statistics['start_date'] }} to {{ $statistics['end_date'] }}</h3>
        </div>
        
        <p>Hello Management Team,</p>
        
        <p>Please find below a summary of visitors for the past week.</p>
        
        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-title">Total Visitors</div>
                <div class="stat-value">{{ $statistics['total_visitors'] }}</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-title">Unique Hosts</div>
                <div class="stat-value">{{ $statistics['unique_hosts'] }}</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-title">Unreturned Cards</div>
                <div class="stat-value {{ $statistics['unreturned_cards'] > 0 ? 'warning-value' : '' }}">
                    {{ $statistics['unreturned_cards'] }}
                </div>
            </div>
        </div>
        
        @if(count($statistics['visitors_by_host']) > 0)
        <div class="host-stats">
            <h3>Top Hosts</h3>
            @foreach($statistics['visitors_by_host'] as $host => $count)
            <div class="host-item">
                <span class="host-name">{{ $host }}</span>
                <span class="host-count">{{ $count }} visitors</span>
            </div>
            @endforeach
        </div>
        @endif
        
        <h2>Visitor Details</h2>
        
        @if($visitors->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Visitor Name</th>
                    <th>Host</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Card Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitors as $visitor)
                <tr>
                    <td>{{ $visitor->full_name }}</td>
                    <td>{{ $visitor->host_name }}</td>
                    <td>{{ $visitor->check_in_time->format('M d, Y g:i A') }}</td>
                    <td>
                        @if($visitor->check_out_time)
                            {{ $visitor->check_out_time->format('M d, Y g:i A') }}
                        @else
                            Not checked out
                        @endif
                    </td>
                    <td>
                        @if($visitor->check_out_time && !$visitor->card_returned)
                            <span style="color: #e3342f;">Not Returned</span>
                        @elseif($visitor->check_out_time)
                            <span style="color: #38c172;">Returned</span>
                        @else
                            <span style="color: #3490dc;">In use</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>No visitors recorded during this period.</p>
        @endif
        
        <div class="footer">
            <p>This is an automated weekly report. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} DCG - Visitor Management System</p>
        </div>
    </div>
</body>
</html>