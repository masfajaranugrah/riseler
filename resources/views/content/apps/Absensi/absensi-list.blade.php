@extends('layouts/layoutMaster')

@section('title', 'Data Absensi')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const table = $('.table-absensi').DataTable({
        responsive: true,
        searching: true,
        ordering: true,
        paging: true
    });

    // === Detail Modal ===
    $(document).on('click', '.btn-detail', function() {
        const data = $(this).data('item');

        let a = JSON.parse(decodeURIComponent(data));

        let html = `
            <p><strong>Nama:</strong> ${a.user?.name ?? '-'}</p>
            <p><strong>Tanggal:</strong> ${a.date}</p>

            <hr>

            <p><strong>Jam Masuk:</strong> ${a.time_in ?? '-'}</p>
            <p><strong>Foto Masuk:</strong> ${a.photo_in ?? '-'}</p>
            <p><strong>Lokasi Masuk:</strong> ${a.lat_in}, ${a.lng_in}</p>

            <hr>

            <p><strong>Jam Pulang:</strong> ${a.time_out ?? '-'}</p>
            <p><strong>Foto Pulang:</strong> ${a.photo_out ?? '-'}</p>
            <p><strong>Lokasi Pulang:</strong> ${a.lat_out}, ${a.lng_out}</p>

            <hr>

            <p><strong>Lembur Masuk:</strong> ${a.lembur_in ?? '-'}</p>
            <p><strong>Lembur Pulang:</strong> ${a.lembur_out ?? '-'}</p>
            <p><strong>Foto Lembur Masuk:</strong> ${a.photo_lembur_in ?? '-'}</p>
            <p><strong>Foto Lembur Pulang:</strong> ${a.photo_lembur_out ?? '-'}</p>

            <hr>

            <p><strong>Total Jam Kerja:</strong> ${a.total_hours}</p>
            <p><strong>Total Lembur:</strong> ${a.overtime_hours}</p>
            <p><strong>Catatan:</strong> ${a.note ?? '-'}</p>
        `;

        $('#modalDetailBody').html(html);
        $('#modalDetail').modal('show');
    });

});
</script>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Data Absensi</h5>
    </div>

    <div class="card-datatable table-responsive">
        <table class="table table-absensi table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Lembur Masuk</th>
                    <th>Lembur Pulang</th>

                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absensi as $i => $a)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $a->user->name }}</td>
                    <td>{{ $a->date }}</td>
                    <td>{{ $a->time_in ?? '-' }}</td>
                    <td>{{ $a->time_out ?? '-' }}</td>
                    <td>{{ $a->lembur_in ?? '-' }}</td>
                    <td>{{ $a->lembur_out ?? '-' }}</td>


                    <td>
                        <button
                            class="btn btn-sm btn-info btn-detail"
                            data-item="{{ urlencode(json_encode($a)) }}">
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetailBody"></div>
        </div>
    </div>
</div>
@endsection
