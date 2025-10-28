<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Password Reset Request - HOA Montaña</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9fafb; padding: 32px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #99461c; padding: 32px 24px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: bold; margin: 0;">HOA Montaña</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 32px;">
                            <h2 style="color: #111827; font-size: 24px; font-weight: bold; margin: 0 0 16px 0; text-align: left;">Password Reset Request</h2>

                            <p style="color: #6b7280; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;">
                                You are receiving this email because we received a password reset request for your account.
                            </p>

                            <h3 style="color: #111827; font-size: 18px; font-weight: 600; margin: 0 0 16px 0; text-align: left;">Your Reset Code</h3>

                            <!-- OTP Code -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 0 32px 0;">
                                <tr>
                                    <td align="center">
                                        <div style="background-color: #ffdbcd; border: 2px dashed #99461c; border-radius: 8px; padding: 32px 24px; text-align: center;">
                                            <div style="color: #360f00; font-size: 32px; font-weight: bold; letter-spacing: 8px; font-family: 'Courier New', monospace; line-height: 1;">
                                                {{ $token }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div style="color: #6b7280; font-size: 16px; line-height: 1.6;">
                                <p style="font-weight: 600; margin: 0 0 16px 0;">
                                    This reset code will expire in 5 minutes.
                                </p>

                                <p style="margin: 0 0 16px 0;">
                                    Please use this code in the HOA Montaña mobile app to reset your password.
                                </p>

                                <p style="font-size: 14px; color: #9ca3af; margin: 32px 0 0 0;">
                                    If you did not request a password reset, please ignore this email.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f5ded5; border-top: 1px solid #e0c9bf; padding: 24px 32px; text-align: center;">
                            <p style="color: #6b7280; font-size: 14px; margin: 0 0 4px 0;">
                                &copy; {{ date('Y') }} HOA Montaña. All rights reserved.
                            </p>
                            <p style="color: #6b7280; font-size: 14px; margin: 0;">
                                Community Facility Reservations
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
