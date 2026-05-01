<?php

namespace App\Exports;

use App\Models\Tagihan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KwitansiExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue, WithStyles
{
    use Exportable;

    protected $status;
    protected $kabupaten;
    protected $kecamatan;

    public function __construct($status = null, $kabupaten = null, $kecamatan = null)
    {
        $this->status = $status;
        $this->kabupaten = $kabupaten;
        $this->kecamatan = $kecamatan;
    }

    public function query()
    {
        return Tagihan::with(['pelanggan', 'paket'])
            ->when($this->status, function ($query, $status) {
                $query->where('status_pembayaran', $status);
            })
            ->when($this->kabupaten, function ($query, $kabupaten) {
                $query->whereHas('pelanggan', function ($q) use ($kabupaten) {
                    $q->where('kabupaten', $kabupaten);
                });
            })
            ->when($this->kecamatan, function ($query, $kecamatan) {
                $query->whereHas('pelanggan', function ($q) use ($kecamatan) {
                    $q->where('kecamatan', $kecamatan);
                });
            })
            ->orderBy('tanggal_mulai', 'desc');
    }

    public function map($tagihan): array
    {
        return [
            $tagihan->pelanggan->nomer_id ?? '-',
            $tagihan->pelanggan->nama_lengkap ?? '-',
            $tagihan->paket->nama_paket ?? '-',
            (float) ($tagihan->harga ?? $tagihan->paket->harga ?? 0),
            $tagihan->tanggal_mulai ?: '-',
            $tagihan->tanggal_berakhir ?: '-',
            ucfirst($tagihan->status_pembayaran ?? '-'),
            $tagihan->catatan ?? '-',
            $tagihan->kwitansi ? asset('storage/'.$tagihan->kwitansi) : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nomor ID', 'Nama Lengkap', 'Paket', 'Harga',
            'Tanggal Mulai', 'Tanggal Berakhir',
            'Status Pembayaran', 'Catatan', 'Kwitansi',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Format column D (price) as currency
            'D' => [
                'numberFormat' => [
                    'formatCode' => '"Rp "#,##0_-'
                ]
            ],
            // Make header bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
