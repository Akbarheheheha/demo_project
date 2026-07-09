<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        p { margin: 0 0 16px; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; }
        th { background: #f1f5f9; text-align: left; }
        td:last-child { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan</h1>
    <p>Periode: {{ isset($startDate) && isset($endDate) ? \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') : now()->translatedFormat('F Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Metrik</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Omzet</td>
                <td>Rp {{ number_format($financialSummary['total_omzet'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Laba Kotor</td>
                <td>Rp {{ number_format($financialSummary['gross_profit'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td>Rp {{ number_format($financialSummary['total_pengeluaran'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pendapatan Bersih</td>
                <td>Rp {{ number_format($financialSummary['net_revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Rata-rata Nilai Transaksi</td>
                <td>Rp {{ number_format($financialSummary['average_ticket'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
