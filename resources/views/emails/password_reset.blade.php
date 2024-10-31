<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Added for responsiveness -->
    <style type="text/css">
        body {
            background-color: #5E6797; /* Set body color */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            font-family: 'Open Sans', sans-serif; /* Set font family for body */

        }

        .styled-header {
            font-size: 48px; /* Adjust font size as needed */
        }

        .thes {
            color: white; /* Color for "Thes" */
        }

        .ease {
            color: yellow; /* Color for "Ease" */
        }

        .reset-button {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            color: white; /* Text color */
            background-color: #CA6D38; /* Button color */
            text-decoration: none; /* Remove underline */
            transition: background-color 0.3s ease, transform 0.2s ease; /* Transition for hover effects */
        }

        .reset-button:hover {
            background-color: #b15b2a; /* Darker shade for hover */
        }

        @media (max-width: 600px) {
            .styled-header {
                font-size: 36px; /* Smaller font size for mobile */
            }

            .reset-button {
                padding: 10px 15px; /* Smaller padding for button on mobile */
                font-size: 14px; /* Smaller font size for button */
            }

            /* Additional styles for smaller screens */
            table {
                width: 100%; /* Make tables full width on smaller screens */
                margin: 0; /* Remove margin */
            }

            td {
                padding: 10px; /* Add padding to table cells for better spacing */
            }
        }
    </style>
</head>

<body>
    <!--100% body table-->
    <table cellspacing="0" border="0" cellpadding="0" width="100%">
        <tr>
            <td>
                <table style="background-color: #5E6797; margin:0 auto; max-width: 670px; width: 100%;" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <h1 class="styled-header">
                                <span class="thes">Thes</span><span class="ease">Ease</span>
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="background:#fff; border-radius:3px; text-align:center; -webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06); -moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06); box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h1 style="color:#1e1e2d; font-weight:500; margin:0; font-size:32px;">You have
                                            requested to reset your password</h1>
                                        <span
                                            style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0;">
                                            We cannot simply send you your old password. A unique link to reset your
                                            password has been generated for you. To reset your password, click the
                                            following link and change your password.
                                        </p>
                                        <p style="text-align: center;">
                                            <a href="{{ $resetLink }}" class="reset-button" style="color: white;">Reset Password</a>
                                        </p>
                                        <p style="color:#455056; font-size:15px; line-height:24px; margin:0;">If you did not request a password reset, please ignore this email.</p>
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
                            <p style="font-size:14px; color:white; line-height:18px; margin:0 0 0;">&copy;{{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!--/100% body table-->
</body>

</html>
