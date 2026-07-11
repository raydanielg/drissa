<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 24px;">{{ config('app.name') }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">Password Reset Notification</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="margin: 0 0 20px 0;">Hello <strong>{{ $user->name }}</strong>,</p>
            
            <p style="margin: 0 0 20px 0;">Your password has been reset by an administrator. Below are your new login credentials:</p>
            
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #6b7280;">Email:</p>
                <p style="margin: 0 0 20px 0; font-size: 16px; font-weight: bold; color: #1f2937;">{{ $user->email }}</p>
                
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #6b7280;">New Password:</p>
                <p style="margin: 0; font-size: 20px; font-weight: bold; color: #10b981; letter-spacing: 2px;">{{ $newPassword }}</p>
            </div>

            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">
                <strong>Security Notice:</strong> For your security, please change this password immediately after logging in.
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}" style="display: inline-block; background: #10b981; color: white; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold;">Login Now</a>
            </div>

            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                If you did not request this change, please contact your administrator immediately.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
