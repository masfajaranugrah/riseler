@extends('layouts/layoutMaster')

@section('title', 'Kas Registrasi')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('page-style')
<style>
:root {
  --primary-color: #18181b;
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
body { background: #f5f5f9; }
.wrapper-card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  background: white;
  padding: 1.5rem 2rem;
  animation: fadeIn 0.4s ease-out;
}
.btn {
  border-radius: 6px !important;
  padding: 0.5rem 1rem !important;
  font-weight: 500 !important;
  font-size: 0.875rem !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 0.5rem !important;
}
.btn-primary, .btn.btn-primary {
  background: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}
.btn-primary:hover { background: #27272a !important; border-color: #27272a !important; }
.btn-add {
  padding: 10px 24px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 12px rgba(24, 24, 27, 0.25) !important;
}
.btn-add:hover { transform: translateY(-2px) !important; box-shadow: 0 6px 16px rgba(24, 24, 27, 0.35) !important; }
.btn-secondary, .btn.btn-secondary {
  background: transparent !important;
  color: #71717a !important;
  border: 1px solid transparent !important;
}
.btn-secondary:hover { background: #f4f4f5 !important; color: #18181b !important; border: 1px solid var(--gray-border) !important; }
.btn-danger, .btn.btn-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: 1px solid #dc2626 !important;
}
.btn-icon {
  width: 32px; height: 32px;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.btn-outline-secondary {
  background: transparent !important;
  border: 1px solid var(--gray-border) !important;
  color: #18181b !important;
}
.btn-outline-secondary:hover { background: #f4f4f5 !important; }

/* ======= TABLE ======= */
.table-wrapper {
  border: 1px solid var(--gray-border);
  border-radius: 12px;
  overflow: hidden;
}
.table-kas {
  margin-bottom: 0;
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
}
.table-kas thead th {
  background: #f8fafc;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.72rem;
  letter-spacing: 0.5px;
  color: #18181b;
  padding: 0.9rem 1rem;
  border-bottom: 1px solid #e4e4e7;
  white-space: nowrap;
}
.table-kas tbody tr { border-bottom: 1px solid #f0f0f0; transition: var(--transition); }
.table-kas tbody tr:last-child { border-bottom: none; }
.table-kas tbody tr:hover { background: #fafafa; }
.table-kas tbody td { padding: 0.9rem 1rem; vertical-align: middle; font-size: 0.875rem; color: #18181b; }
.table-kas tfoot td {
  padding: 0.9rem 1rem;
  font-weight: 700;
  border-top: 1px solid #e4e4e7;
  background: #f8fafc;
}
.col-no { width: 60px; text-align: center; }
.col-keterangan { min-width: 200px; }
.col-amount { width: 160px; text-align: right; }
.col-saldo { width: 180px; text-align: right; font-weight: 600; }
.col-actions { width: 100px; text-align: center; }
.amount-pemasukan { color: #16a34a; font-weight: 600; }
.amount-pengeluaran { color: #dc2626; font-weight: 600; }
.saldo-positive { color: #16a34a; }
.saldo-negative { color: #dc2626; }

/* ======= SUMMARY CARDS ======= */
.summary-card {
  background: #fff;
  border: 1px solid var(--gray-border);
  border-radius: 12px;
  padding: 1.25rem 1.5rem;
  transition: var(--transition);
}
.summary-card:hover { border-color: #18181b; box-shadow: 0 4px 12px rgba(24,24,27,0.05); transform: translateY(-2px); }
.summary-label { color: #71717a; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.summary-value { color: #18181b; font-size: 1.35rem; font-weight: 700; margin-top: 0.25rem; }
.summary-icon {
  width: 44px; height: 44px;
  border-radius: 10px;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 1.25rem;
}

/* ======= EMPTY STATE ======= */
.empty-state { text-align: center; padding: 4rem 2rem; }
.empty-state-icon { font-size: 4rem; color: #e4e4e7; margin-bottom: 1rem; }
.empty-state h5 { color: #18181b; font-weight: 600; margin-bottom: 0.5rem; }
.empty-state p { color: #71717a; margin-bottom: 1.5rem; }

/* ======= MODAL ======= */
.modal-backdrop {
  background-color: rgba(255,255,255,0.3) !important;
  backdrop-filter: blur(10px) !important;
  -webkit-backdrop-filter: blur(10px) !important;
  opacity: 1 !important;
}
.modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { background: #18181b !important; border-radius: 16px 16px 0 0; padding: 1.5rem 2rem; border: none; }
.modal-title { font-weight: 600; font-size: 1.1rem; color: #fafafa !important; }
.modal-header .btn-close { filter: invert(1); opacity: 1; }
.modal-body { padding: 1.75rem 2rem; }
.modal-footer { padding: 1.25rem 2rem; border-top: 1px solid var(--gray-border); background: #ffffff; border-radius: 0 0 16px 16px; margin-top: 1rem; }

/* ======= FORM CONTROLS ======= */
.form-input-group {
  border: 1px solid var(--gray-border);
  border-radius: 8px;
  padding: 0.6rem 1rem;
  display: flex; align-items: center; gap: 0.75rem;
  transition: all 0.2s;
  background: #fff;
}
.form-input-group:focus-within { border-color: #18181b; box-shadow: 0 0 0 2px rgba(24,24,27,0.08); }
.form-input-group .form-icon { color: #71717a; font-size: 1rem; min-width: 16px; }
.form-input-group input,
.form-input-group select,
.form-input-group textarea {
  border: none; outline: none; flex: 1; font-size: 0.875rem; color: #18181b; background: transparent;
}
.form-input-group textarea { resize: vertical; min-height: 60px; }
.form-input-group input:read-only { background: transparent; }
.form-label { font-weight: 600; font-size: 0.8rem; color: #18181b; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.3px; }
.jenis-toggle { display: flex; gap: 0.5rem; }
.jenis-btn { padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.875rem; border: 2px solid var(--gray-border); background: #fff; color: #71717a; transition: all 0.2s; }
.jenis-btn.active-masuk { background: #dcfce7; border-color: #16a34a; color: #16a34a; }
.jenis-btn.active-keluar { background: #fee2e2; border-color: #dc2626; color: #dc2626; }

/* ======= FILTER BAR ======= */
.filter-bar { display: flex; gap: 0.75rem; align-items: stretch; flex-wrap: wrap; }
.filter-select { padding: 0.5rem 1rem; border: 1px solid var(--gray-border); border-radius: 8px; font-size: 0.875rem; color: #18181b; background: #f8fafc; cursor: pointer; transition: all 0.2s; height: 38px; }
.filter-select:focus, .filter-select:hover { outline: none; border-color: #18181b; background: #fff; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
<script>
const STORE_URL  = "{{ route('kas-registrasi.store') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

// ======= TOGGLE JENIS =======
function setJenis(jenis) {
    document.getElementById('inputJenis').value = jenis;
    document.getElementById('btnPemasukan').className = 'jenis-btn' + (jenis === 'pemasukan' ? ' active-masuk' : '');
    document.getElementById('btnPengeluaran').className = 'jenis-btn' + (jenis === 'pengeluaran' ? ' active-keluar' : '');
    
    const editJenis = document.getElementById('editJenis');
    if (editJenis) {
        editJenis.value = jenis;
        document.getElementById('editBtnPemasukan').className = 'jenis-btn' + (jenis === 'pemasukan' ? ' active-masuk' : '');
        document.getElementById('editBtnPengeluaran').className = 'jenis-btn' + (jenis === 'pengeluaran' ? ' active-keluar' : '');
    }
}
function setEditJenis(jenis) {
    document.getElementById('editJenis').value = jenis;
    document.getElementById('editBtnPemasukan').className = 'jenis-btn' + (jenis === 'pemasukan' ? ' active-masuk' : '');
    document.getElementById('editBtnPengeluaran').className = 'jenis-btn' + (jenis === 'pengeluaran' ? ' active-keluar' : '');
}

// ======= ADD MODAL & INIT =======
document.addEventListener('DOMContentLoaded', function() {
    
    // Init Flatpickr
    flatpickr('.date-picker', {
      dateFormat: "Y-m-d",
      defaultDate: "today",
      altInput: true,
      altFormat: "d F Y",
    });

    const editPicker = flatpickr('#editTanggal', {
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "d F Y",
    });

    window.openEdit = function(id) {
        fetch(`/dashboard/admin/kas-registrasi/${id}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('editId').value = data.id;
            document.getElementById('editKeterangan').value = data.keterangan;
            document.getElementById('editJumlah').value = data.jumlah;
            editPicker.setDate(data.tanggal);
            document.getElementById('editCatatan').value = data.catatan || '';
            setEditJenis(data.jenis);
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        });
    }

    // Add form submit
    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        const keterangan = document.getElementById('inputKeterangan').value.trim();
        const jenis = document.getElementById('inputJenis').value;
        const jumlah = document.getElementById('inputJumlah').value;
        const tanggal = document.getElementById('inputTanggal').value;
        if (!keterangan || !jenis || !jumlah || !tanggal) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Semua field wajib diisi!', confirmButtonColor: '#18181b' });
            return;
        }
        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ keterangan, jenis, jumlah, tanggal, catatan: document.getElementById('inputCatatan').value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Reload without toaster
                window.location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan.', confirmButtonColor: '#18181b' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi bermasalah.', confirmButtonColor: '#18181b' }))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="ri-save-line me-1"></i>Simpan'; });
    });

    // Edit form submit
    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const keterangan = document.getElementById('editKeterangan').value.trim();
        const jenis = document.getElementById('editJenis').value;
        const jumlah = document.getElementById('editJumlah').value;
        const tanggal = document.getElementById('editTanggal').value;
        const btn = document.getElementById('btnUpdate');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        fetch(`/dashboard/admin/kas-registrasi/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ keterangan, jenis, jumlah, tanggal, catatan: document.getElementById('editCatatan').value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan.', confirmButtonColor: '#18181b' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi bermasalah.', confirmButtonColor: '#18181b' }))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="ri-save-line me-1"></i>Simpan'; });
    });
});

// ======= DELETE =======
function deleteItem(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: false,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-outline-secondary'
        },
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/dashboard/admin/kas-registrasi/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    });
}
</script>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

  <div class="wrapper-card">
    {{-- ======= HEADER ======= --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
      <div>
        <h4 class="fw-bold mb-1" style="color:#18181b;"><i class="ri-wallet-3-line me-2" style="color:#18181b;"></i>Kas Registrasi</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">Kelola pemasukan dan pengeluaran kas registrasi</p>
      </div>
      <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="ri-add-line"></i> Add Regist
      </button>
    </div>

    {{-- ======= FILTER ======= --}}
    <form method="GET" action="{{ route('kas-registrasi.index') }}" class="mb-4">
      <div class="filter-bar">
        <select name="filter_month" class="filter-select">
          @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>
              {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
            </option>
          @endforeach
        </select>
        <select name="filter_year" class="filter-select">
          @foreach(range(date('Y')-2, date('Y')+1) as $y)
            <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary" style="height: 38px;"><i class="ri-filter-line me-1"></i>Filter</button>
        <div class="d-flex align-items-center"><span class="text-muted ms-2 fw-medium" style="font-size:0.875rem; letter-spacing:0.5px;">{{ strtoupper($monthLabel) }}</span></div>
      </div>
    </form>

    {{-- ======= SUMMARY CARDS ======= --}}
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="summary-card d-flex align-items-center gap-3">
          <div class="summary-icon" style="background:#dcfce7;">
            <i class="ri-arrow-down-circle-line" style="color:#16a34a;"></i>
          </div>
          <div>
            <div class="summary-label">Total Pemasukan</div>
            <div class="summary-value" style="color:#16a34a;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="summary-card d-flex align-items-center gap-3">
          <div class="summary-icon" style="background:#fee2e2;">
            <i class="ri-arrow-up-circle-line" style="color:#dc2626;"></i>
          </div>
          <div>
            <div class="summary-label">Total Pengeluaran</div>
            <div class="summary-value" style="color:#dc2626;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="summary-card d-flex align-items-center gap-3">
          <div class="summary-icon" style="background:#dbeafe;">
            <i class="ri-safe-line" style="color:#1d4ed8;"></i>
          </div>
          <div>
            <div class="summary-label">Saldo Akhir</div>
            <div class="summary-value" style="color:{{ $saldoAkhir >= 0 ? '#16a34a' : '#dc2626' }};">
              Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ======= TABLE CARD ======= --}}
    <div class="table-wrapper">
      <div class="table-responsive">
        <table class="table-kas">
          <thead>
            <tr>
              <th class="col-no">NO</th>
              <th class="col-keterangan">KETERANGAN</th>
              <th class="col-amount" style="text-align:right;">PEMASUKAN</th>
              <th class="col-amount" style="text-align:right;">PENGELUARAN</th>
              <th class="col-saldo" style="text-align:right;">SALDO</th>
              <th class="col-actions">AKSI</th>
            </tr>
          </thead>
          <tbody>
            @forelse($paginatedItems as $i => $item)
            <tr>
              <td class="col-no text-center text-muted" style="font-size:0.8rem;">{{ $paginatedItems->firstItem() + $i }}</td>
              <td class="col-keterangan">
                <div class="fw-bold" style="color:#18181b; font-size:0.9rem;">{{ $item->keterangan }}</div>
                @if($item->catatan)
                  <div class="text-muted mt-1" style="font-size:0.8rem;">{{ $item->catatan }}</div>
                @endif
                <div class="text-muted" style="font-size:0.75rem;margin-top:4px;">
                  <i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                </div>
              </td>
              <td class="col-amount">
                @if($item->pemasukan > 0)
                  <span class="amount-pemasukan">Rp {{ number_format($item->pemasukan, 0, ',', '.') }}</span>
                @else
                  <span class="text-muted" style="opacity:0.4;">-</span>
                @endif
              </td>
              <td class="col-amount">
                @if($item->pengeluaran > 0)
                  <span class="amount-pengeluaran">Rp {{ number_format($item->pengeluaran, 0, ',', '.') }}</span>
                @else
                  <span class="text-muted" style="opacity:0.4;">-</span>
                @endif
              </td>
              <td class="col-saldo">
                <span class="{{ $item->saldo >= 0 ? 'saldo-positive' : 'saldo-negative' }}" style="font-size:0.95rem;">
                  Rp {{ number_format($item->saldo, 0, ',', '.') }}
                </span>
              </td>
              <td class="col-actions text-center">
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn btn-icon btn-outline-secondary" title="Edit" onclick="openEdit({{ $item->id }})">
                    <i class="ri-edit-line" style="font-size:0.95rem;"></i>
                  </button>
                  <button class="btn btn-icon btn-danger" title="Hapus" onclick="deleteItem({{ $item->id }})">
                    <i class="ri-delete-bin-line" style="font-size:0.95rem;"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" style="border-bottom:none;">
                <div class="empty-state">
                  <div class="empty-state-icon"><i class="ri-wallet-3-line"></i></div>
                  <h5>Belum Ada Data</h5>
                  <p>Belum ada transaksi kas registrasi untuk periode ini.</p>
                  <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="ri-add-line me-1"></i>Tambah Transaksi
                  </button>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
          @if($paginatedItems->count() > 0)
          <tfoot>
            <tr>
              <td colspan="2" class="text-end fw-bold" style="font-size:0.85rem;text-transform:uppercase;">Jumlah Bulan Ini</td>
              <td class="col-amount" style="text-align:right;color:#16a34a;font-weight:700;">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
              </td>
              <td class="col-amount" style="text-align:right;color:#dc2626;font-weight:700;">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
              </td>
              <td colspan="2"></td>
            </tr>
            <tr>
              <td colspan="4" class="text-end" style="font-weight:800;font-size:1rem;letter-spacing:0.5px;color:#18181b;">SALDO AKHIR</td>
              <td class="col-saldo" style="font-size:1.1rem;font-weight:800;color:{{ $saldoAkhir >= 0 ? '#16a34a' : '#dc2626' }};">
                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
              </td>
              <td></td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
    
    {{-- Pagination Links --}}
    <div class="mt-4 d-flex justify-content-end border-top pt-3" style="border-color: var(--gray-border) !important;">
      {{ $paginatedItems->links('vendor.pagination.custom-always') }}
    </div>
  </div>

</div>

{{-- ======= MODAL ADD ======= --}}
<div class="modal fade" id="modalAdd" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ri-add-circle-line me-2"></i>Tambah Transaksi Kas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAdd">
        <div class="modal-body">

          {{-- Jenis --}}
          <div class="mb-4">
            <label class="form-label">Jenis Transaksi *</label>
            <div class="jenis-toggle">
              <button type="button" id="btnPemasukan" class="jenis-btn active-masuk" onclick="setJenis('pemasukan')">
                <i class="ri-arrow-down-circle-line me-1"></i>Pemasukan
              </button>
              <button type="button" id="btnPengeluaran" class="jenis-btn" onclick="setJenis('pengeluaran')">
                <i class="ri-arrow-up-circle-line me-1"></i>Pengeluaran
              </button>
            </div>
            <input type="hidden" id="inputJenis" name="jenis" value="pemasukan">
          </div>

          {{-- Keterangan --}}
          <div class="mb-3">
            <label class="form-label">Keterangan *</label>
            <div class="form-input-group">
              <i class="ri-file-text-line form-icon"></i>
              <input type="text" id="inputKeterangan" name="keterangan" placeholder="Contoh: Registrasi Budi" required>
            </div>
          </div>

          {{-- Jumlah --}}
          <div class="mb-3">
            <label class="form-label">Jumlah (Rp) *</label>
            <div class="form-input-group">
              <i class="ri-money-dollar-circle-line form-icon"></i>
              <input type="number" id="inputJumlah" name="jumlah" placeholder="Contoh: 100000" min="0" required>
            </div>
          </div>

          {{-- Tanggal --}}
          <div class="mb-3">
            <label class="form-label">Tanggal *</label>
            <div class="form-input-group">
              <i class="ri-calendar-line form-icon"></i>
              <input type="text" id="inputTanggal" class="date-picker" name="tanggal" placeholder="Pilih Tanggal" required>
            </div>
          </div>

          {{-- Catatan --}}
          <div class="mb-2">
            <label class="form-label">Catatan (Opsional)</label>
            <div class="form-input-group" style="align-items:flex-start;padding-top:0.75rem;">
              <i class="ri-sticky-note-line form-icon" style="margin-top:2px;"></i>
              <textarea id="inputCatatan" name="catatan" placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" id="btnSimpan" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ======= MODAL EDIT ======= --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ri-edit-line me-2"></i>Edit Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEdit">
        <input type="hidden" id="editId">
        <div class="modal-body">

          {{-- Jenis --}}
          <div class="mb-4">
            <label class="form-label">Jenis Transaksi *</label>
            <div class="jenis-toggle">
              <button type="button" id="editBtnPemasukan" class="jenis-btn" onclick="setEditJenis('pemasukan')">
                <i class="ri-arrow-down-circle-line me-1"></i>Pemasukan
              </button>
              <button type="button" id="editBtnPengeluaran" class="jenis-btn" onclick="setEditJenis('pengeluaran')">
                <i class="ri-arrow-up-circle-line me-1"></i>Pengeluaran
              </button>
            </div>
            <input type="hidden" id="editJenis" name="jenis" value="pemasukan">
          </div>

          {{-- Keterangan --}}
          <div class="mb-3">
            <label class="form-label">Keterangan *</label>
            <div class="form-input-group">
              <i class="ri-file-text-line form-icon"></i>
              <input type="text" id="editKeterangan" name="keterangan" required>
            </div>
          </div>

          {{-- Jumlah --}}
          <div class="mb-3">
            <label class="form-label">Jumlah (Rp) *</label>
            <div class="form-input-group">
              <i class="ri-money-dollar-circle-line form-icon"></i>
              <input type="number" id="editJumlah" name="jumlah" min="0" required>
            </div>
          </div>

          {{-- Tanggal --}}
          <div class="mb-3">
            <label class="form-label">Tanggal *</label>
            <div class="form-input-group">
              <i class="ri-calendar-line form-icon"></i>
              <input type="text" id="editTanggal" name="tanggal" placeholder="Pilih Tanggal" required>
            </div>
          </div>

          {{-- Catatan --}}
          <div class="mb-2">
            <label class="form-label">Catatan (Opsional)</label>
            <div class="form-input-group" style="align-items:flex-start;padding-top:0.75rem;">
              <i class="ri-sticky-note-line form-icon" style="margin-top:2px;"></i>
              <textarea id="editCatatan" name="catatan" placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" id="btnUpdate" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
