<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin:0; padding:0; font-family: 'Nunito', 'Segoe UI', Arial, sans-serif; background: #f3f4f6; color: #1f2937;">

<table width="100%" cellpadding="0" cellspacing="0" style="background: #f3f4f6; min-height: 100vh;">
    <tr>
        <td align="center" style="padding: 40px 16px;">

<table width="560" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); max-width: 560px;">

    {{-- Header --}}
    <tr>
        <td style="background: linear-gradient(135deg, #024938 0%, #013028 100%); padding: 36px 40px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.02em;">
                {{ $clinicName }}
            </h1>
            <p style="margin: 6px 0 0; color: rgba(255,255,255,0.7); font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase;">
                Password Reset Request
            </p>
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding: 36px 40px;">
            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #374151;">
                Hello <strong>{{ $userName }}</strong>,
            </p>
            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #374151;">
                We received a request to reset your password for your {{ $clinicName }} account. Click the button below to choose a new password.
            </p>

            {{-- Button --}}
            <div style="text-align: center; margin: 28px 0;">
                <a href="{{ $url }}" style="display: inline-block; background: linear-gradient(135deg, #f9ac00 0%, #d49700 100%); color: #1f2937; font-weight: 700; font-size: 15px; text-decoration: none; padding: 14px 40px; border-radius: 10px; box-shadow: 0 4px 14px rgba(249,172,0,0.3);">
                    Reset My Password
                </a>
            </div>

            <p style="margin: 0 0 12px; font-size: 13px; line-height: 1.6; color: #6b7280;">
                If the button doesn't work, copy and paste this link into your browser:
            </p>
            <p style="margin: 0 0 24px; font-size: 12px; line-height: 1.5; color: #9ca3af; word-break: break-all;">
                <a href="{{ $url }}" style="color: #024938; text-decoration: underline;">{{ $url }}</a>
            </p>

            {{-- Expiry Notice --}}
            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; margin: 0 0 20px;">
                <p style="margin: 0; font-size: 13px; color: #92400e; line-height: 1.5;">
                    <strong>&#9888; Important:</strong> This reset link will expire in <strong>{{ $expiry }} minutes</strong>. Please use it promptly.
                </p>
            </div>

            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #374151;">
                If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 6px; font-size: 12px; color: #9ca3af; text-align: center;">
                This is an automated message from <strong>{{ $clinicName }}</strong>. Please do not reply.
            </p>
            <p style="margin: 0; font-size: 11px; color: #d1d5db; text-align: center;">
                &copy; {{ date('Y') }} {{ $clinicName }}. All rights reserved.
            </p>
        </td>
    </tr>
</table>

<table width="560" cellpadding="0" cellspacing="0" style="max-width: 560px; margin-top: 16px;">
    <tr>
        <td style="text-align: center;">
            <p style="margin: 0; font-size: 11px; color: #9ca3af;">
                For your security, never share your password or reset link with anyone.
            </p>
        </td>
    </tr>
</table>

        </td>
    </tr>
</table>

</body>
</html>
