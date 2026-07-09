<table>
    <thead>
        <tr>
            <th colspan="2">Laporan Keuangan - {{ isset($startDate) && isset($endDate) ? \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') : now()->translatedFormat('F Y') }}</th>
        </tr>
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
        <tr>
            <td>Jumlah Transaksi</td>
            <td>{{ $financialSummary['jumlah_transaksi'] }}</td>
        </tr>
    </tbody>
</table>
