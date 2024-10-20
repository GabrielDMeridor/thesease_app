<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .email-header {
            text-align: center;
            background-color: #007bff;
            color: #ffffff;
            padding: 20px 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
        }
        .email-body h1 {
            color: #333;
        }
        .email-body p {
            font-size: 16px;
            color: #555;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 20px;
        }
        .reset-button {
            display: inline-block;
            padding: 15px 25px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 16px;
        }
        .reset-button:hover {
            background-color: #0056b3;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                padding: 10px;
            }
            .email-header {
                font-size: 20px;
            }
            .email-body {
                padding: 10px;
            }
            .reset-button {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            Password Reset
        </div>
        <div class="email-body">
            <h1>Password Reset Request</h1>
            <p>You have requested to reset your password. To reset your password, click the button below:</p>
            <p style="text-align: center;">
                <a href="{{ $resetLink }}" class="reset-button">Reset Password</a>
            </p>
            <p>If you did not request a password reset, please ignore this email.</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </div>
    </div>
</body>
</html>
