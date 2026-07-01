<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1;url={{ route('pos.fullscreen') }}">
    <title>Membuka Kasir POS - SmartBiz</title>
    @vite(['resources/css/app.css', 'resources/css/pos.css', 'resources/js/app.js'])
    <style>
        :root {
            color-scheme: dark;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #020617;
            color: #fff;
        }

        .shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .panel {
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.06);
            padding: 24px;
            text-align: center;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.45);
        }

        .badge {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #4f46e5, #ec4899);
            font-weight: 900;
        }

        h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        p {
            margin: 8px 0 0;
            font-size: 14px;
            line-height: 1.6;
            color: #cbd5e1;
        }

        a {
            display: inline-flex;
            margin-top: 20px;
            border-radius: 12px;
            padding: 10px 16px;
            background: #fff;
            color: #020617;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="panel">
            <div class="badge">S</div>
            <h1>Membuka halaman kasir penuh</h1>
            <p>POS dibuka di mode layar penuh agar tidak tercampur dengan dashboard admin.</p>
            <a href="{{ route('pos.fullscreen') }}">
                Buka Kasir Sekarang
            </a>
        </section>
    </main>

    <script>
        window.location.replace(@json(route('pos.fullscreen')));
    </script>
</body>
</html>
