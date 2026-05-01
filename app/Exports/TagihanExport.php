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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagihanExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $status;
    protected $kabupaten;
    protected $kecamatan;
    protected $search;
    protected $periode;

    public function __construct($status = null, $kabupaten = null, $kecamatan = null, $search = null, $periode = null)
    {
        $this->status = $status;
        $this->kabupaten = $kabupaten;
        $this->kecamatan = $kecamatan;
        $this->search = $search;
        $this->periode = $periode;
    }

    public function query()
    {
        return Tagihan::with(['pelanggan', 'paket'])
            ->when($this->search, function ($query, $search) {
                $query->whereHas('pelanggan', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                });
            })
            ->when($this->status, function ($query, $status) {
                $query->where('status_pembayaran', $status);
            })
            ->when($this->periode, function ($query, $periode) {
                $parts = explode('-', $periode);
                if (count($parts) === 2) {
                    $query->whereYear('tanggal_mulai', $parts[0])
                          ->whereMonth('tanggal_mulai', $parts[1]);
                }
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
            $tagihan->pelanggan->alamat_jalan ?? '-',
            $tagihan->pelanggan->rt ?? '-',
            $tagihan->pelanggan->rw ?? '-',
            $tagihan->pelanggan->desa ?? '-',
            $tagihan->pelanggan->kecamatan ?? '-',
            $tagihan->pelanggan->kabupaten ?? '-',
            $tagihan->pelanggan->provinsi ?? '-',
            $tagihan->pelanggan->kode_pos ?? '-',
            $tagihan->paket->nama_paket ?? '-',
            (float) ($tagihan->harga ?? $tagihan->paket->harga ?? 0),
            $tagihan->paket->kecepatan ?? '-',
            $tagihan->tanggal_mulai ?: '-',
            $tagihan->tanggal_berakhir ?: '-',
            ucfirst($tagihan->status_pembayaran ?? '-'),
            $tagihan->bukti_pembayaran ? asset('storage/'.$tagihan->bukti_pembayaran) : '-',
            $tagihan->catatan ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nomor ID', 'Nama Lengkap', 'Alamat Jalan', 'RT', 'RW',
            'Desa', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Kode Pos',
            'Paket', 'Harga', 'Kecepatan',
            'Tanggal Mulai', 'Tanggal Berakhir',
            'Status Pembayaran', 'Bukti Pembayaran', 'Catatan',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Format column L (price) as currency
            'L' => [
                'numberFormat' => [
                    'formatCode' => '"Rp "#,##0_-'
                ]
            ],
            // Make header bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
