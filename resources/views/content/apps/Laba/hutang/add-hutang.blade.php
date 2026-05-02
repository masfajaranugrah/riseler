@extends('layouts/layoutMaster')

@section('title', 'Tambah Hutang')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jumlahInput = document.getElementById('jumlah');

    flatpickr('#tanggal', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        defaultDate: new Date(),
        time_24hr: true
    });

    jumlahInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value === '') value = '0';
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });

    jumlahInput.form.addEventListener('submit', function() {
        jumlahInput.value = jumlahInput.value.replace(/\./g, '');
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card" style="border-radius:12px; border:none; box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-header" style="background:#18181b; border-radius:12px 12px 0 0;">
            <h5 class="mb-0" style="color:#fafafa; font-weight:600;">Tambah Hutang</h5>
        </div>
        <div class="card-body" style="padding:1.5rem;">
            <form action="{{ route('hutang.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" required>
                    @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="jumlah" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" required>
                    </div>
                    @error('jumlah')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea id="catatan" name="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal & Jam</label>
                    <input type="text" id="tanggal" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('hutang.index') }}" class="btn btn-label-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Hutang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
