<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1;url={{ route('pos.fullscreen') }}">
    <title>Membuka Kasir POS - SmartBiz</title>
    <style>
        :root {
            color-scheme: dark;
        }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #090d16;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }
        .container {
            text-align: center;
            max-width: 380px;
            padding: 2rem;
        }
        /* Glowy Ring Spinner */
        .spinner {
            position: relative;
            width: 60px;
            height: 60px;
            margin: 0 auto 2rem;
            border-radius: 50%;
            border: 3px solid rgba(99, 102, 241, 0.1);
            border-top-color: #6366f1;
            border-right-color: #a855f7;
            animation: spin 1s infinite linear;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h1 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin: 0 0 0.5rem 0;
            background: linear-gradient(to right, #a5b4fc, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p {
            font-size: 0.875rem;
            color: #94a3b8;
            margin: 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Menyiapkan Kasir POS</h1>
        <p>Membuka mode layar penuh. Harap tunggu sebentar...</p>
    </div>

    <script>
        // Redirect immediately
        window.location.replace(@json(route('pos.fullscreen')));
    </script>
</body>
</html>
