<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stakeholder Communication Alert</title>
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
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2d3748;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
            color: #718096;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Stakeholder Communication Alert</h1>
        
        <p>There are {{ $count }} stakeholders without communication in the last {{ $threshold }} days.</p>
        
        <p>The following stakeholders require attention:</p>
        
        {!! $tableHtml !!}
        
        <div class="footer">
            <p>Thanks,<br>
            {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
