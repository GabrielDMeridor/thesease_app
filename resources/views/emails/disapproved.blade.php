
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
            <td align="center">
                <table style="background-color: #5E6797; margin:0 auto; max-width: 670px; width: 100%;" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <h1 class="styled-header" style="text-align:center;">
                                <span class="thes" style="text-align:center;">Thes</span><span style="text-align:center;" class="ease">Ease</span>
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
                                    <td style="padding:0 35px;">
                                        <h1 style="color:#1e1e2d; font-weight:500; margin:0; font-size:32px; text-align:center;">Hello, {{ $user->name }}</h1>
                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0; text-align:center;">
                                            We regret to inform you that your account has been disapproved.
                                        </p>

                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0; text-align:center;">
                                            <strong>Reason for Disapproval:</strong> {{ $reason }}
                                        </p>

                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0; text-align:center;">
                                            If you think this was a mistake, please contact our support team.
                                        </p>
                                        <br>
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
                        <td style="text-align:center;">
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