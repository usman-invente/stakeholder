<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contract Alteration Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f8f9fa;
        }
        .details-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .details-table td:first-child {
            font-weight: bold;
            width: 40%;
            background-color: #e9ecef;
        }
        .changes-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff3cd;
        }
        .changes-table th {
            background-color: #ffc107;
            color: #856404;
            padding: 12px 15px;
            font-weight: bold;
            border-bottom: 2px solid #ffb700;
        }
        .changes-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #ffeaa7;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 20px 0;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        .notes-list {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .notes-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Contract Alteration Notification</h1>
        </div>

        <p>Dear {{ $contract->contract_owner }},</p>

        <div class="alert-info">
            <strong>📢 Notice:</strong> This notification is to inform you that changes have been made to one of your contracts.
        </div>

        <table class="details-table">
            <tr>
                <td>Contract ID</td>
                <td><strong>{{ $contract->contract_id }}</strong></td>
            </tr>
            <tr>
                <td>Contract Title</td>
                <td>{{ $contract->contract_title }}</td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td>{{ $contract->supplier_name }}</td>
            </tr>
            <tr>
                <td>Department</td>
                <td>{{ $contract->department->name }}</td>
            </tr>
            <tr>
                <td>Current Status</td>
                <td>{{ ucfirst($contract->status) }}</td>
            </tr>
            <tr>
                <td>Updated By</td>
                <td><strong>{{ $updatedBy ?? 'System Administrator' }}</strong></td>
            </tr>
            <tr>
                <td>Date Modified</td>
                <td><strong>{{ now()->format('d/m/Y H:i:s') }}</strong></td>
            </tr>
        </table>

        <h3>📋 Changes Made:</h3>
        @if(isset($changes) && is_array($changes) && count($changes) > 0)
            <table class="changes-table">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Previous Value</th>
                        <th>New Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($changes as $field => $change)
                        <tr>
                            <td><strong>{{ ucwords(str_replace('_', ' ', $field)) }}</strong></td>
                            <td>{{ $change['old'] ?? 'Not set' }}</td>
                            <td><strong>{{ $change['new'] ?? 'Not set' }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert-info">
                Multiple fields have been updated. Please review the contract details for complete information.
            </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('contracts.show', $contract->id) }}" class="btn">View Updated Contract</a>
        </div>

        <div class="notes-list">
            <strong>📋 Important Notes:</strong>
            <ul>
                <li>Please review these changes carefully</li>
                <li>Verify that all modifications are accurate and approved</li>
                <li>Contact the administrative team if you have any concerns</li>
                <li>Ensure all relevant stakeholders are informed of these changes</li>
            </ul>
        </div>

        @if($contract->status === 'active' && $contract->days_until_expiry <= 30)
            <div class="alert-warning">
                <strong>⚠️ Additional Notice:</strong> This contract expires in {{ $contract->days_until_expiry }} days. Please consider renewal if applicable.
            </div>
        @endif

        <div class="alert-info">
            <strong>⚠️ Important:</strong> If you did not authorize these changes or believe this notification was sent in error, please contact the contract administration team immediately.
        </div>

        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Contract Management System</strong></p>

        <div class="footer">
            <strong>Contract Management Information:</strong><br>
            • Contract Dashboard: <a href="{{ route('contracts.index') }}">{{ route('contracts.index') }}</a><br>
            • Department: {{ $contract->department->name }}<br>
            @if($contract->contract_value)
                • Contract Value: £{{ number_format($contract->contract_value, 2) }}<br>
            @endif
            • Expiry Date: {{ $contract->formatted_expiry_date }}<br><br>
            
            <em>This is an automated notification sent when contract details are modified. All contract alterations are logged and tracked for audit purposes.</em>
        </div>
    </div>
</body>
</html>