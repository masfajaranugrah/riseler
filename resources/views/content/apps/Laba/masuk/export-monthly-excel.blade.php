<table border="1">
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold; font-size: 14pt;">
                REKAP LABA MASUK - {{ mb_strtoupper($monthLabel) }}
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #d1d5db;">Tanggal</th>
            <th style="font-weight: bold; background-color: #d1d5db;">No Pelanggan</th>
            <th style="font-weight: bold; background-color: #d1d5db;">Nama Pelanggan</th>
            <th style="font-weight: bold; background-color: #d1d5db;">Paket</th>
            <th style="font-weight: bold; background-color: #d1d5db;">Status</th>
            <th style="font-weight: bold; background-color: #d1d5db;">Jenis Tagihan</th>
            <th style="font-weight: bold; background-color: #d1d5db;">Metode Pembayaran</th>
            <th style="font-weight: bold; background-color: #d1d5db;">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($incomes as $income)
            <tr>
                <td>{{ \Carbon\Carbon::parse($income->tanggal_pembayaran)->format('d-m-Y H:i') }}</td>
                <td>{{ $income->no_pelanggan }}</td>
                <td>{{ $income->nama_pelanggan }}</td>
                <td>{{ $income->nama_paket }}</td>
                <td style="text-transform: uppercase;">{{ $income->status_pembayaran }}</td>
                <td>{{ $exporter->formatJenisTagihan($income) }}</td>
                <td>{{ $income->type_pembayaran }}</td>
                <td data-format="#,##0">{{ $income->jumlah }}</td>
            </tr>
        @endforeach
        
        <tr>
            <td colspan="8"></td>
        </tr>
        
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold; background-color: #e5e7eb; font-size: 12pt;">
                RINGKASAN BERDASARKAN METODE PEMBAYARAN
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #f3f4f6;">Metode Pembayaran</th>
            <th colspan="2" style="font-weight: bold; background-color: #f3f4f6;">Total (Rp)</th>
        </tr>
        
        @php
            $grandTotal = 0;
        @endphp
        
        @foreach($bankTotals as $bank)
            @php
                $grandTotal += $bank->total;
            @endphp
            <tr>
                <td colspan="6">{{ $bank->nama_bank }}</td>
                <td colspan="2" data-format="#,##0">{{ $bank->total }}</td>
            </tr>
        @endforeach
        
        <tr>
            <td colspan="6" style="font-weight: bold;">GRAND TOTAL</td>
            <td colspan="2" style="font-weight: bold;" data-format="#,##0">{{ $grandTotal }}</td>
        </tr>
    </tbody>
</table>
