<!DOCTYPE html>
<html>
<head>
	<title>Slip Gaji Karyawan</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			font-size: 11px;
			color: #000;
            padding: 0;
            margin: 0;
		}
        .container {
            width: 100%;
            padding: 20px;
        }
		.header {
			display: table;
            width: 100%;
			margin-bottom: 5px;
		}
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-logo {
            font-size: 24px;
            font-weight: 900;
            color: #1e3a8a; /* Dark blue like JMK */
            margin-bottom: 0;
        }
        .header-sublogo {
            font-size: 10px;
            font-weight: bold;
            color: #555;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .header-info-table {
            width: 100%;
            font-size: 11px;
        }
        .header-info-table td {
            padding: 1px 0;
        }
        .header-info-label {
            width: 80px;
            font-weight: normal;
        }
        
        .title-bar {
            background-color: #555;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .section-header {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .subsection-header {
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 5px;
            padding-left: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .col-num { width: 20px; }
        .col-item { width: 250px; }
        .col-detail { width: auto; text-align: left; }
        .col-amount { width: 100px; text-align: right; }
        .col-total { width: 100px; text-align: right; font-weight: bold; }

        .total-highlight {
            background-color: #ffff00;
            font-weight: bold;
            padding: 5px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .notes {
            font-size: 9px;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        .signature-table {
            width: 100%;
            margin-top: 20px;
            text-align: center;
        }
        .signature-names {
            margin-top: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        /* Helper for layout */
        .w-100 { width: 100%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .pl-2 { padding-left: 15px; }
        .pl-3 { padding-left: 30px; }
	</style>
</head>
<body>
    @php
        use Carbon\Carbon;
        
        // Perhitungan subtotal (Simple groupings based on available fields)
        // Group A: Penghasilan Tetap
        $totalTetap = 
            $gaji->gaji_pokok + 
            $gaji->tunj_jabatan + 
            $gaji->tunj_fungsional + 
            $gaji->tunj_kehadiran + 
            $gaji->makan + 
            $gaji->transport;
        
        // Add Dynamic Allowances to Tetap
        if(is_array($gaji->tunj_dynamic) || is_object($gaji->tunj_dynamic)) {
            foreach($gaji->tunj_dynamic as $val) {
                $totalTetap += (float)$val;
            }
        }

        // Group B: Lembur
        $totalLembur = $gaji->lembur;
        
        // Total Penerimaan
        $totalPenerimaan = $totalTetap + $totalLembur;

        // Group Pengeluaran
        $totalPotongan = 
            $gaji->pot_sosial + 
            $gaji->pot_denda + 
            $gaji->pot_koperasi + 
            $gaji->pot_pajak + 
            $gaji->pot_lain;

        // Date formatting
        Carbon::setLocale('id');
        $periode = Carbon::parse($gaji->created_at)->isoFormat('MMMM Y');
        $printDate = Carbon::now()->isoFormat('D MMMM Y');
    @endphp

    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <img src="{{ public_path('assets/img/jmk-logo.png') }}" style="height: 60px; width: auto;" alt="JMK Logo">
            </div>
            <div class="header-right">
                <table class="header-info-table" align="right">
                    <tr>
                        <td class="header-info-label">Bulan</td>
                        <td>: {{ strtoupper($periode) }}</td>
                    </tr>
                    <tr>
                        <td class="header-info-label">Karyawan</td>
                        <td>: {{ $gaji->employee->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="header-info-label">Status</td>
                        <td>: {{ $gaji->employee->jabatan ?? 'KARYAWAN' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TITLE BAR -->
        <div class="title-bar">
            FORM SLIP GAJI KARYAWAN
        </div>

        <!-- I. PENERIMAAN -->
        <div class="section-header">I. PENERIMAAN</div>
        
        <!-- A. Penghasilan Tetap -->
        <div class="subsection-header">A. Penghasilan Tetap</div>
        <table class="data-table">
            <tr>
                <td class="col-num">1.</td>
                <td class="col-item">Gaji Pokok</td>
                <td class="col-detail"></td>
                <td class="col-amount">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                <td class="col-total"></td>
            </tr>
            @if($gaji->tunj_jabatan > 0)
            <tr>
                <td class="col-num">2.</td>
                <td class="col-item">Tunjangan Jabatan</td>
                <td class="col-detail"></td>
                <td class="col-amount">Rp {{ number_format($gaji->tunj_jabatan, 0, ',', '.') }}</td>
                <td class="col-total"></td>
            </tr>
            @endif
            @if($gaji->tunj_fungsional > 0)
            <tr>
                <td class="col-num">3.</td>
                <td class="col-item">Tunjangan Fungsional</td>
                <td class="col-detail"></td>
                <td class="col-amount">Rp {{ number_format($gaji->tunj_fungsional, 0, ',', '.') }}</td>
                <td class="col-total"></td>
            </tr>
            @endif
            @if($gaji->tunj_kehadiran > 0)
            <tr>
                <td class="col-num">4.</td>
                <td class="col-item">Tunjangan Kehadiran</td>
                <td class="col-detail"></td>
                <td class="col-amount">Rp {{ number_format($gaji->tunj_kehadiran, 0, ',', '.') }}</td>
                <td class="col-total"></td>
            </tr>
            @endif
            @if($gaji->makan > 0)
            <tr>
                <td class="col-num">5.</td>
                <td class="col-item">Tunjangan Makan</td>
                <td class="col-detail"></td>
                <td class="col-amount">Rp {{ number_format($gaji->makan, 0, ',', '.') }}</td>
                <td class="col-total"></td>
            </tr>
            @endif
            @if($gaji->transport > 0)
            <tr>
                <td class="col-num">6.</td>
                <td class="col-item">Tunjangan Transportasi</td>
                <td class="col-detail"></td>
                <td class="col-amount">Rp {{ number_format($gaji->transport, 0, ',', '.') }}</td>
                <td class="col-total"></td>
            </tr>
            @endif
            
            {{-- Dynamic Allowances --}}
            @php $idx = 7; @endphp
            @foreach($gaji->tunj_dynamic as $key => $val)
                @if($val > 0)
                <tr>
                    <td class="col-num">{{ $idx++ }}.</td>
                    <td class="col-item">{{ $gaji->tunj_keterangan[$key] ?? 'Tunjangan Lain' }}</td>
                    <td class="col-detail"></td>
                    <td class="col-amount">Rp {{ number_format($val, 0, ',', '.') }}</td>
                    <td class="col-total"></td>
                </tr>
                @endif
            @endforeach

            {{-- Total Tetap --}}
            <tr>
                <td colspan="4" style="padding-top: 5px;"><strong>Total Penghasilan Tetap</strong></td>
                <td class="col-total" style="padding-top: 5px;">Rp {{ number_format($totalTetap, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- B. Penghasilan Lembur -->
        <div class="subsection-header" style="margin-top: 15px;">B. Penghasilan Lembur</div>
        <table class="data-table">
            <tr>
                <td class="col-num">1.</td>
                <td class="col-item">Lembur Kantor/Lapangan</td>
                <td class="col-detail"></td> {{-- Could put hours here if data existed --}}
                <td class="col-amount">{{ $gaji->lembur > 0 ? 'Rp '.number_format($gaji->lembur, 0, ',', '.') : '-' }}</td>
                <td class="col-total"></td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top: 5px;"><strong>Total Penghasilan Lembur</strong></td>
                <td class="col-total" style="padding-top: 5px;">Rp {{ number_format($totalLembur, 0, ',', '.') }}</td>
            </tr>
            
            {{-- GRAND TOTAL PENERIMAAN --}}
            <tr>
                <td colspan="5" style="padding-top: 10px;"></td>
            </tr>
            <tr>
                <td colspan="4"><strong>TOTAL PENERIMAAN</strong></td>
                <td class="col-total">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- II. PENGELUARAN -->
        <div class="section-header" style="margin-top: 20px;">II. PENGELUARAN</div>

        <div class="subsection-header">A. Potongan</div>
        <table class="data-table">
            <tr>
                <td class="col-num">1.</td>
                <td class="col-item">Potongan Sosial/BPJS</td>
                <td class="col-detail"></td>
                <td class="col-amount">{{ $gaji->pot_sosial > 0 ? 'Rp '.number_format($gaji->pot_sosial, 0, ',', '.') : '-' }}</td>
                <td class="col-total"></td>
            </tr>
             <tr>
                <td class="col-num">2.</td>
                <td class="col-item">Pajak (PPh 21)</td>
                <td class="col-detail"></td>
                <td class="col-amount">{{ $gaji->pot_pajak > 0 ? 'Rp '.number_format($gaji->pot_pajak, 0, ',', '.') : '-' }}</td>
                <td class="col-total"></td>
            </tr>
        </table>

        <div class="subsection-header" style="margin-top: 10px;">B. Lain-lain</div>
        <table class="data-table">
            <tr>
                <td class="col-num">1.</td>
                <td class="col-item">Koperasi/Pinjaman</td>
                <td class="col-detail"></td>
                <td class="col-amount">{{ $gaji->pot_koperasi > 0 ? 'Rp '.number_format($gaji->pot_koperasi, 0, ',', '.') : '-' }}</td>
                <td class="col-total"></td>
            </tr>
             <tr>
                <td class="col-num">2.</td>
                <td class="col-item">Denda/Lainnya</td>
                <td class="col-detail"></td>
                <td class="col-amount">
                    @php $lain = $gaji->pot_denda + $gaji->pot_lain; @endphp
                    {{ $lain > 0 ? 'Rp '.number_format($lain, 0, ',', '.') : '-' }}
                </td>
                <td class="col-total"></td>
            </tr>
            
             {{-- TOTAL PENGELUARAN --}}
             <tr>
                <td colspan="4" style="padding-top: 10px;"><strong>TOTAL PENGELUARAN</strong></td>
                <td class="col-total" style="padding-top: 10px;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- GRAND TOTAL -->
        <div style="margin-top: 20px; font-size: 13px;">
            <table class="data-table">
                <tr class="total-highlight">
                    <td colspan="4">TOTAL GAJI DITERIMA KARYAWAN</td>
                    <td class="text-right">Rp {{ number_format($gaji->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- FOOTER & NOTES -->
        <div class="footer">
            <div class="notes">
                * Gaji dihitung berdasarkan masa kerja efektif.<br>
                * Terdapat kelebihan/kekurangan pembayaran gaji akan diperhitungkan pada periode selanjutnya.<br>
                * Bila ada kesalahan penggajian harap menghubungi Finance maksimal 3x24 jam setelah slip diterima.
            </div>

            <div class="text-right" style="margin-bottom: 20px; padding-right: 50px; font-weight: bold;">
                Klaten, {{ $printDate }}
            </div>

            <table class="signature-table">
                <tr>
                    <td width="33%" style="vertical-align: bottom;">
                        {{-- Finance Column --}}
                         <div class="signature-names">FINANCE</div>
                    </td>
                    <td width="33%" style="vertical-align: bottom;">
                        {{-- Director Column with Image --}}
                        <div style="margin-bottom: 5px;">
                            <img src="{{ public_path('assets/img/signature-director.png') }}" style="height: 70px; width: auto;" alt="Director Sign">
                        </div>
                        <div class="signature-names">ROHMAT S.N</div>
                    </td>
                    <td width="33%" style="vertical-align: bottom;">
                        {{-- Employee Column with Image --}}
                        <div style="margin-bottom: 5px;">
                            <img src="{{ public_path('assets/img/signature-sh.png') }}" style="height: 60px; width: auto;" alt="Employee Sign">
                        </div>
                        <div class="signature-names"> EKA MULYANA</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>
</html>
