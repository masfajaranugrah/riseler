<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center; font-weight: bold; font-size: 14px;">Ringkasan BBP - Rugi Laba</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center; font-style: italic;">Periode: {{ $periodeLabel }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">Pendapatan Kotor</td>
            <td>Rp {{ number_format($pendapatanKotor, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pendapatan Bersih</td>
            <td>Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pemasukan</td>
            <td>Rp {{ number_format($pemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pengeluaran</td>
            <td>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; text-decoration: underline;">Ringkasan Administrasi</td>
        </tr>
        <tr>
            <td>Omset Seharusnya (Total biaya langganan pelanggan aktif)</td>
            <td>Rp {{ number_format($omsetSeharusnya, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Omset Realisasi (periode berjalan)</td>
            <td>Rp {{ number_format($omsetRealisasi, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Potongan PPN</td>
            <td>Rp {{ number_format($potonganPpn, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Piutang</td>
            <td style="{{ $piutang > 0 ? 'color: #ff9900;' : '' }}">Rp {{ number_format($piutang, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Hutang (periode berjalan)</td>
            <td style="color: #ff0000;">Rp {{ number_format($totalHutang ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Rugi/Laba</td>
            <td style="font-weight: bold; {{ $rugiLaba >= 0 ? 'color: #00cc00;' : 'color: #ff0000;' }}">Rp {{ number_format($rugiLaba, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; text-decoration: underline;">Catatan Rumus</td>
        </tr>
        <tr>
            <td colspan="2">1. Pendapatan Kotor = Pemasukan + Potongan PPN</td>
        </tr>
        <tr>
            <td colspan="2">2. Pendapatan Bersih = Pemasukan</td>
        </tr>
        <tr>
            <td colspan="2">3. Rugi/Laba = Pendapatan Bersih - Pengeluaran</td>
        </tr>
        <tr>
            <td colspan="2">4. Piutang = Omset Seharusnya - Omset Realisasi</td>
        </tr>
    </tbody>
</table>
