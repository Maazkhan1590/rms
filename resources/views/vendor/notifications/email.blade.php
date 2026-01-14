<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .email-header {
            background: linear-gradient(135deg, #003d82 0%, #0056b3 50%, #4d8bff 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .email-header img {
            max-width: 80px;
            margin-bottom: 15px;
        }

        .email-header h1 {
            font-size: 24px;
            margin: 10px 0;
            font-weight: 600;
        }

        .email-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .email-body {
            padding: 40px 30px;
        }

        .email-body h2 {
            color: #0056b3;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .email-body p {
            margin-bottom: 15px;
            color: #555555;
        }

        .email-body strong {
            color: #333333;
        }

        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #0056b3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
        }

        .button:hover {
            background-color: #003d82;
        }

        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #0056b3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .email-footer p {
            font-size: 13px;
            color: #6c757d;
            margin: 5px 0;
        }

        .email-footer a {
            color: #0056b3;
            text-decoration: none;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #6c757d;
            text-decoration: none;
            font-size: 12px;
        }

        ul {
            margin: 15px 0;
            padding-left: 20px;
        }

        ul li {
            margin: 8px 0;
            color: #555555;
        }

        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 25px 0;
        }

        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 25px 15px;
            }

            .email-header {
                padding: 20px 15px;
            }

            .button {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <h1>Research Management System</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            {{-- Greeting --}}
            @if (! empty($greeting))
                <h2>{{ $greeting }}</h2>
            @else
                @if ($level === 'error')
                    <h2>Whoops!</h2>
                @else
                    <h2>Hello!</h2>
                @endif
            @endif

            {{-- Intro Lines --}}
            @foreach ($introLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            {{-- Action Button --}}
            @isset($actionText)
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
                </div>
            @endisset

            {{-- Outro Lines --}}
            @foreach ($outroLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            {{-- Salutation --}}
            @if (! empty($salutation))
                <div class="divider"></div>
                <p>{!! nl2br($salutation) !!}</p>
            @else
                <div class="divider"></div>
                <p>Best regards,<br>
                Research Management Team</p>
            @endif

            {{-- Subcopy --}}
            @isset($actionText)
                <div class="info-box">
                    <p style="font-size: 12px; margin: 0;">
                        If you're having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser:
                    </p>
                    <p style="word-break: break-all; font-size: 11px; color: #6c757d; margin-top: 8px;">
                        {{ $actionUrl }}
                    </p>
                </div>
            @endisset
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>Research Management System</strong></p>
            
            <div class="social-links">
                <a href="#">Website</a> |
                <a href="mailto:support@rms.com">Support</a> |
                <a href="#">Privacy Policy</a>
            </div>

            <p style="margin-top: 15px;">
                © {{ date('Y') }} Research Management System. All rights reserved.
            </p>

            <p style="font-size: 11px; color: #999; margin-top: 10px;">
                This email was sent to {{ $notifiable->email ?? 'you' }}. 
                If you did not expect this email, please contact support.
            </p>
        </div>
    </div>
</body>
</html>
