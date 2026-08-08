{{-- resources/views/vendor/mail/html/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Email styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #00A3E0;
        }
        .header img {
            max-height: 50px;
        }
        .content {
            padding: 30px 0;
        }
        .footer {
            padding: 20px 0;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .btn {
            display: inline-block;
            background-color: #00A3E0;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #0088bb;
        }
        .order-number {
            font-weight: 700;
            color: #0F3D5E;
        }
        .total {
            font-weight: 700;
            color: #0F3D5E;
            font-size: 18px;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #f3f4f6;
        }
        .no-image {
            width: 50px;
            height: 50px;
            background-color: #f3f4f6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 12px;
        }
        .info-grid {
            width: 100%;
        }
        .info-grid td {
            padding: 6px 10px;
            border: none;
        }
        .info-grid .label {
            font-weight: 600;
            color: #4b5563;
            width: 120px;
        }
        .info-grid .value {
            color: #1f2937;
        }
        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 10px;
            }
            table {
                font-size: 13px;
            }
            td, th {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('images/copower-logo-email.png') }}" alt="{{ config('app.name') }}">
        </div>

        <div class="content">
            {{ $slot }}
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="font-size: 11px; color: #9ca3af;">
                This email was sent to {{ $user->email ?? 'customer' }}. 
                If you did not request this quote, please ignore this email.
            </p>
        </div>
    </div>
</body>
</html>