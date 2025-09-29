<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEscalation ? 'URGENT: Contract Expiry - Management Escalation' : 'Contract Expiry Notification' }}</title>
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
        @if($isEscalation)
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .alert-urgent {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        @else
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        @endif
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
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .actions-list {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .actions-list ol {
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
            @if($isEscalation)
                <h1>🚨 URGENT: Contract Expiry - Management Escalation</h1>
            @else
                <h1>📋 Contract Expiry Notification</h1>
            @endif
        </div>

        <p>Dear @if($isEscalation)Management Team@else{{ $contract->contract_owner }}@endif,</p>

        @if($isEscalation)
            <div class="alert-urgent">
                <strong>⚠️ ESCALATION NOTICE:</strong> This contract expired {{ abs($contract->days_until_expiry) }} days ago and requires immediate management attention.
            </div>

            <p>This is an escalation notification regarding an expired contract that requires immediate attention.</p>
        @else
            <div class="alert-warning">
                @if($daysUntilExpiry <= 7)
                    <strong>🚨 URGENT:</strong> This contract expires in {{ $daysUntilExpiry }} days!
                @else
                    <strong>⚠️ WARNING:</strong> This contract expires in {{ $daysUntilExpiry }} days.
                @endif
            </div>

            <p>Your contract is approaching its expiry date and requires your immediate attention.</p>
        @endif

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
            @if($isEscalation)
            <tr>
                <td>Contract Owner</td>
                <td>{{ $contract->contract_owner }} ({{ $contract->contract_owner_email }})</td>
            </tr>
            @endif
            <tr>
                <td>Expiry Date</td>
                <td><strong>{{ $contract->formatted_expiry_date }}</strong></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ ucfirst($contract->status) }}</td>
            </tr>
            @if($contract->contract_value)
            <tr>
                <td>Contract Value</td>
                <td>£{{ number_format($contract->contract_value, 2) }}</td>
            </tr>
            @endif
            @if($isEscalation)
            <tr>
                <td>Days Past Expiry</td>
                <td><strong style="color: #dc3545;">{{ abs($contract->days_until_expiry) }} days</strong></td>
            </tr>
            @endif
        </table>

        @if($contract->auto_renewal && !$isEscalation)
            <div class="alert-warning">
                <strong>Note:</strong> This contract is set for automatic renewal. Please verify renewal terms are still acceptable.
            </div>
        @endif

        <div class="actions-list">
            <strong>@if($isEscalation)Required Actions:@else Next Steps:@endif</strong>
            <ol>
                @if($isEscalation)
                    <li>Review the expired contract immediately</li>
                    <li>Contact the supplier if renewal is needed</li>
                    <li>Update contract status in the system</li>
                    <li>Process any necessary documentation</li>
                @else
                    <li>Review the contract terms and renewal options</li>
                    <li>Contact the supplier to discuss renewal if needed</li>
                    <li>Update the contract status in the system</li>
                    <li>Ensure all documentation is current</li>
                @endif
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('contracts.show', $contract->id) }}" class="btn">View Contract Details</a>
        </div>

        @if($isEscalation)
            <p><strong>Please treat this matter with high priority.</strong> If no action is taken within the next 7 days, further escalation procedures may be initiated.</p>
        @else
            <p>If you have any questions or need assistance with this contract, please contact the administrative team immediately.</p>
            <p><strong>Important:</strong> Failure to renew or address this contract by the expiry date may result in service disruptions.</p>
        @endif

        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Contract Management System</strong></p>

        <div class="footer">
            <strong>System Information:</strong><br>
            • Notification sent: {{ now()->format('d/m/Y H:i:s') }}<br>
            • Contract Management Dashboard: <a href="{{ route('contracts.index') }}">{{ route('contracts.index') }}</a><br><br>
            
            @if($isEscalation)
                <em>This is an automated escalation notification. The contract owner has been copied on this email.</em>
            @else
                <em>This is an automated reminder. You will receive additional notifications as the expiry date approaches.</em>
            @endif
        </div>
    </div>
</body>
</html>