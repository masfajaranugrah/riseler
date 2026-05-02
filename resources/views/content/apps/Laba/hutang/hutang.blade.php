@extends('layouts/layoutMaster')

@section('title', 'Data Hutang')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Data Hutang</h4>
            <p class="text-muted mb-0">Kelola data hutang administrasi</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('hutang.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama barang/catatan">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </form>
            <a href="{{ route('hutang.create') }}" class="btn btn-primary">Tambah Hutang</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Catatan</th>
                    <th>Tanggal & Jam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hutangs as $item)
                    <tr>
                        <td>{{ ($hutangs->firstItem() ?? 1) + $loop->index }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $item->catatan ?: '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data hutang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 d-flex justify-content-between align-items-center">
        <div class="text-muted small">Menampilkan <strong>{{ $hutangs->firstItem() ?? 0 }}</strong> - <strong>{{ $hutangs->lastItem() ?? 0 }}</strong> dari <strong>{{ $hutangs->total() }}</strong> data</div>
        <div>{{ $hutangs->appends(request()->query())->onEachSide(1)->links('pagination::custom-always') }}</div>
    </div>
</div>
@endsection
