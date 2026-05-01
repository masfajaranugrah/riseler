@extends('layouts/layoutMaster')

@section('title', 'Status Pelanggan')

@section('content')
<div class="card">
  <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
    <h5 class="mb-0">Status Pelanggan</h5>
    <div class="d-flex align-items-center gap-2">
      <label for="statusSearch" class="mb-0 fw-semibold">Search:</label>
      <input type="text" id="statusSearch" class="form-control" placeholder="Cari nama / WhatsApp / paket">
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>No. WhatsApp</th>
            <th>Paket</th>
            <th>Status</th>
            <th>Login Terakhir</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pelanggan as $index => $item)
            @php
              $isActive = optional($item->loginStatus)->is_active;
              $loggedInAt = optional($item->loginStatus)->logged_in_at;
            @endphp
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $item->nama_lengkap }}</td>
              <td>{{ $item->no_whatsapp }}</td>
              <td>{{ optional($item->paket)->nama_paket ?? '-' }}</td>
              <td>
                @if($isActive)
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>
              <td>{{ $loggedInAt ? $loggedInAt->timezone(config('app.timezone'))->format('d M Y H:i') : '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center p-3">Belum ada data pelanggan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@section('page-script')
<script>
  (function() {
    const input = document.getElementById('statusSearch');
    const table = document.querySelector('table');
    if (!input || !table) return;

    const rows = Array.from(table.querySelectorAll('tbody tr'));

    const filter = () => {
      const term = input.value.toLowerCase();
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    };

    input.addEventListener('input', filter);
  })();
</script>
@endsection
@endsection
