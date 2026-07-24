<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting to eSewa…</title>
    <style>
        body { font-family: Georgia, serif; background: #f7f3ec; color: #2c2118; display: grid; place-items: center; min-height: 100vh; margin: 0; }
        .box { text-align: center; padding: 2rem; }
        .muted { color: #7a6a5a; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Redirecting to eSewa</h1>
        <p class="muted">Paying Rs {{ number_format($amount) }}. Do not close this window.</p>
        <form id="esewa-form" action="{{ $formUrl }}" method="POST">
            @foreach ($fields as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach
            <noscript>
                <button type="submit">Continue to eSewa</button>
            </noscript>
        </form>
    </div>
    <script>document.getElementById('esewa-form').submit();</script>
</body>
</html>
