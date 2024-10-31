<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            background-color: #5E6797;
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            text-align: center;
        }

        .styled-header {
            font-size: 48px;
        }

        .thes {
            color: white;
        }

        .ease {
            color: yellow;
        }

        .next-steps {
            margin: 20px 0;
        }

        .footer {
            font-size: 14px;
            color: white;
            line-height: 18px;
            margin: 0;
        }

        @media (max-width: 600px) {
            .styled-header {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>
    <table cellspacing="0" border="0" cellpadding="0" width="100%">
        <tr>
            <td style="text-align:center;">
                <table style="background-color: #5E6797; margin:0 auto; max-width: 670px; width: 100%;" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <h1 class="styled-header"  style="text-align:center;">
                                <span class="thes" style="text-align:center;">Thes</span><span class="ease" style="text-align:center;">Ease</span>
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:3px; text-align:center; -webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06); -moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06); box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 35px; text-align:center;">
                                        <h1 style="color:#1e1e2d; font-weight:500; margin:0; font-size:32px; text-align:center;">Hello, {{ $user->name }}</h1>
                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0; text-align:center;">
                                            Congratulations! Your account has been verified successfully.
                                        </p>
                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0; text-align:center;">
                                            You can now access the full features of our system. We're excited to have you on board!
                                        </p>
                                        <div class="next-steps">
                                            <h2 style="color:#1e1e2d; font-weight:500; margin:0; font-size:24px; text-align:center;">Next Steps:</h2>
                                            <ul style="list-style-type: none; padding: 0; color:#455056; font-size:15px; line-height:24px; text-align:left; display: inline-block; text-align:center;">
                                                <li><strong>Log in to your account:</strong> Access your dashboard to explore all features.</li>
                                            </ul>
                                        </div>
                                        <p style="text-align:center;">Thanks,<br>{{ config('app.name') }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <p class="footer">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>