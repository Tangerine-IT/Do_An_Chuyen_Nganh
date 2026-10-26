<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('petshop.name') }} email preview</title>
</head>
<body style="margin:0;background:#f8f5ee;color:#2f2a25;font-family:Arial,sans-serif;padding:32px 16px">
    <main style="max-width:600px;margin:0 auto;background:#fff;padding:32px;border-radius:12px">
        <div style="background:#176b5b;color:#fff;margin:-32px -32px 28px;padding:22px 32px;font-size:22px;font-weight:700">
            {{ config('petshop.name') }}
        </div>
        <h1 style="font-size:24px">{{ __('mail.preview_intro') }}</h1>
        <p>{{ __('mail.preview_booking') }}</p>
        <a href="{{ url('/') }}" style="display:inline-block;background:#e8864a;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:700">
            {{ __('mail.preview_button') }}
        </a>
        <p style="color:#766f68;margin-top:28px">{{ __('mail.preview_footer') }}</p>
    </main>
</body>
</html>
