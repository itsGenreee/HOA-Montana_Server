<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $title ?? 'HOA Montaña' }}</title>
    <style type="text/css">
        /* Base Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: rgb(255, 251, 255);
            color: rgb(32, 26, 24);
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            padding: 20px;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .header {
            background: rgb(153, 70, 28);
            padding: 25px 30px;
            text-align: center;
        }

        .header a {
            color: rgb(255, 255, 255);
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
        }

        .body {
            padding: 35px 30px;
            width: 100%;
        }

        .footer {
            background: rgb(245, 222, 213);
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid rgb(216, 194, 186);
        }

        .footer p {
            margin: 0 0 10px 0;
            color: rgb(83, 68, 62);
            font-size: 12px;
            line-height: 1.4;
        }

        /* Button Styles */
        .button {
            display: inline-block;
            background: rgb(153, 70, 28);
            color: rgb(255, 255, 255) !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            mso-padding-alt: 0;
        }

        /* OTP/Special Elements */
        .highlight {
            background: rgb(255, 219, 205);
            color: rgb(54, 15, 0);
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            word-break: break-all;
        }

        .subcopy {
            border-top: 1px solid rgb(216, 194, 186);
            padding-top: 15px;
            margin-top: 15px;
            color: rgb(83, 68, 62);
            font-size: 12px;
        }

        /* Mobile Styles */
        @media only screen and (max-width: 620px) {
            .wrapper {
                padding: 10px !important;
            }

            .content {
                width: 100% !important;
                border-radius: 0 !important;
            }

            .header {
                padding: 20px 15px !important;
            }

            .header a {
                font-size: 20px !important;
            }

            .body {
                padding: 25px 20px !important;
            }

            .footer {
                padding: 15px 20px !important;
            }

            .highlight {
                padding: 15px !important;
                font-size: 20px !important;
                margin: 15px 0 !important;
            }
        }

        @media only screen and (max-width: 480px) {
            .header a {
                font-size: 18px !important;
            }

            .body {
                padding: 20px 15px !important;
            }

            .highlight {
                font-size: 18px !important;
                padding: 12px !important;
            }
        }

        /* Outlook-specific fixes */
        .outer-table {
            width: 100% !important;
        }
    </style>
    <!--[if mso]>
    <style type="text/css">
        .body-table {
            width: 600px !important;
        }
        .content {
            width: 600px !important;
        }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="outer-table" width="100%" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 20px;">
                <div class="wrapper" style="max-width: 600px; margin: 0 auto;">
                    <!--[if (gte mso 9)|(IE)]>
                    <table role="presentation" align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                    <tr>
                    <td align="center" valign="top" width="600">
                    <![endif]-->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="content" style="max-width: 600px; width: 100%;">
                        <!-- Header -->
                        <tr>
                            <td class="header" style="padding: 25px 30px; text-align: center;">
                                <a href="{{ config('app.url') }}" style="color: rgb(255, 255, 255); text-decoration: none; font-size: 24px; font-weight: bold;">
                                    HOA Montaña
                                </a>
                            </td>
                        </tr>

                        <!-- Email Content -->
                        <tr>
                            <td class="body" style="padding: 35px 30px;">
                                {{ $slot }}
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td class="footer" style="padding: 20px 30px; text-align: center;">
                                <p style="margin: 0 0 10px 0; color: rgb(83, 68, 62); font-size: 12px;">
                                    &copy; {{ date('Y') }} HOA Montaña. All rights reserved.
                                </p>
                                <p style="margin: 0; color: rgb(83, 68, 62); font-size: 12px;">
                                    Community Facility Reservations
                                </p>
                            </td>
                        </tr>
                    </table>
                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                    </tr>
                    </table>
                    <![endif]-->
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
