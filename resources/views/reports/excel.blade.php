<table>
    <thead>
        <tr>
            <th colspan="2">Laporan Keuangan - {{ now()->translatedFormat('F Y') }}</th>
        </tr>
        <tr>
            <th>Metrik</th>
            <th>Nilai</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Omzet</td>
            <td>{{ $financialSummary['total_omzet'] }}</td>
        </tr>
        <tr>
            <td>Laba Kotor</td>
            <td>{{ $financialSummary['gross_profit'] }}</td>
        </tr>
        <tr>
            <td>Total Pengeluaran</td>
            <td>{{ $financialSummary['total_pengeluaran'] }}</td>
        </tr>
        <tr>
            <td>Pendapatan Bersih</td>
            <td>{{ $financialSummary['net_revenue'] }}</td>
        </tr>
        <tr>
            <td>Rata-rata Nilai Transaksi</td>
            <td>{{ $financialSummary['average_ticket'] }}</td>
        </tr>
        <tr>
            <td>Jumlah Transaksi</td>
            <td>{{ $financialSummary['jumlah_transaksi'] }}</td>
        </tr>
    </tbody>
</table>
