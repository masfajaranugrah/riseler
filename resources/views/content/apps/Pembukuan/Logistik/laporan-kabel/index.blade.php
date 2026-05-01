@extends('layouts/layoutMaster')

@section('title', 'Laporan Kabel')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('page-style')
<style>
  .lk-filter-input {
    height: 42px;
  }
  .lk-main-card {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(17, 24, 39, 0.06);
    overflow: hidden;
    background: #fff;
  }
  .lk-main-header {
    padding: 24px;
    border-bottom: 1px solid #eceff3;
  }
  .lk-main-body {
    padding: 20px;
    background: #fff;
  }
  .lk-subcard {
    border: 1px solid #eceff3;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(17, 24, 39, 0.04);
  }
  .lk-pagination-wrap {
    border-top: 1px solid #e9ecef;
    padding-top: 14px;
    margin-top: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .lk-pagination-info {
    color: #6b7280;
    font-size: 1rem;
  }
  .lk-pagination-info strong {
    color: #111827;
  }
  .lk-pagination-wrap .pagination {
    margin-bottom: 0;
    gap: 8px;
  }
  .lk-pagination-wrap .pagination .page-link {
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
    border-radius: 999px !important;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d1d5db;
    color: #111827;
    font-weight: 600;
  }
  .lk-pagination-wrap .pagination .page-item.active .page-link {
    background: #111827;
    border-color: #111827;
    color: #fff;
  }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="lk-main-card">
    <div class="lk-main-header">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div>
          <h4 class="mb-1">Laporan Kabel</h4>
          <div class="text-muted">Semua data laporan tarikan kabel pelanggan.</div>
        </div>
        <div class="d-flex gap-2">
          <a
            href="{{ route('logistik.laporan-kabel.export.pdf', request()->only(['date', 'wilayah', 'search'])) }}"
            class="btn btn-outline-secondary"
          >
            <i class="ri-file-pdf-line me-1"></i> Export PDF
          </a>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLaporanModal">
            <i class="ri-add-line me-1"></i> Add Laporan
          </button>
        </div>
      </div>
    </div>

    <div class="lk-main-body">
      <div class="row g-3 mb-3">
        <div class="col-12 col-lg-7">
          <div class="card h-100 lk-subcard">
            <div class="card-body">
          <form method="GET" action="{{ route('logistik.laporan-kabel.index') }}" class="row g-2 align-items-end">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <div class="col-12 col-md-5">
              <label class="form-label">Tanggal</label>
              <input type="date" class="form-control lk-filter-input" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-12 col-md-5">
              <label class="form-label">Wilayah</label>
              <select name="wilayah" class="form-select lk-filter-input">
                <option value="">Semua Wilayah</option>
                <option value="Klaten" {{ request('wilayah') === 'Klaten' ? 'selected' : '' }}>Klaten</option>
                <option value="Gunung Kidul" {{ request('wilayah') === 'Gunung Kidul' ? 'selected' : '' }}>Gunung Kidul</option>
              </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
              <button type="submit" class="btn btn-primary w-100 lk-filter-input">Filter</button>
            </div>
          </form>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-5">
          <div class="card h-100 lk-subcard">
            <div class="card-body">
          <form method="GET" action="{{ route('logistik.laporan-kabel.index') }}" class="row g-2 align-items-end">
            <input type="hidden" name="date" value="{{ request('date') }}">
            <input type="hidden" name="wilayah" value="{{ request('wilayah') }}">
            <div class="col-12 col-md-8">
              <label class="form-label">Search</label>
              <input type="search" class="form-control lk-filter-input" name="search" value="{{ request('search') }}" placeholder="Cari nama, alamat, wilayah, jenis kabel...">
            </div>
            <div class="col-6 col-md-2 d-grid">
              <button type="submit" class="btn btn-primary lk-filter-input">Cari</button>
            </div>
            <div class="col-6 col-md-2 d-grid">
              <a href="{{ route('logistik.laporan-kabel.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center lk-filter-input">Reset</a>
            </div>
          </form>
            </div>
          </div>
        </div>
      </div>

      @if($errors->any())
        <div class="alert alert-danger">
          <div class="fw-semibold mb-1">Gagal menyimpan laporan:</div>
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card lk-subcard">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pelanggan</th>
            <th>Nama Teknisi</th>
            <th>Wilayah</th>
            <th>Alamat</th>
            <th>Tarikan (Meter)</th>
            <th>Jenis Kabel</th>
            <th>Sisa Kabel</th>
            <th>Keterangan</th>
            <th>Tanggal Input</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($laporanKabel as $item)
            <tr>
              <td>{{ $laporanKabel->firstItem() + $loop->index }}</td>
              <td>{{ $item->nama_pelanggan }}</td>
              <td>{{ optional($item->employee)->full_name ?: '-' }}</td>
              <td>{{ $item->wilayah ?: '-' }}</td>
              <td>{{ $item->alamat }}</td>
              <td>{{ rtrim(rtrim(number_format((float) $item->tarikan_meter, 2, '.', ''), '0'), '.') }} M</td>
              <td><span class="badge bg-label-primary text-uppercase">{{ $item->jenis_kabel }}</span></td>
              <td>{{ rtrim(rtrim(number_format((float) $item->sisi_core, 2, '.', ''), '0'), '.') }} M</td>
              <td>{{ $item->keterangan ?: '-' }}</td>
              <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
              <td class="text-end">
                @php
                  $editPayload = e(json_encode([
                    'id' => $item->id,
                    'nama_pelanggan' => $item->nama_pelanggan,
                    'wilayah' => $item->wilayah,
                    'employee_id' => $item->employee_id,
                    'alamat' => $item->alamat,
                    'tarikan_meter' => $item->tarikan_meter,
                    'jenis_kabel' => $item->jenis_kabel,
                    'sisa_kabel' => $item->sisi_core,
                    'keterangan' => $item->keterangan,
                  ]));
                @endphp
                <div class="d-inline-flex gap-1">
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary btn-edit-laporan"
                    data-item="{{ $editPayload }}"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger btn-delete-laporan"
                    data-name="{{ $item->nama_pelanggan }}"
                    data-url="{{ route('logistik.laporan-kabel.destroy', $item) }}"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center text-muted py-4">Belum ada laporan kabel.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body pt-0">
      <div class="lk-pagination-wrap">
        <div class="lk-pagination-info">
          Menampilkan
          <strong>{{ $laporanKabel->firstItem() ?? 0 }}</strong> -
          <strong>{{ $laporanKabel->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $laporanKabel->total() }}</strong> data
        </div>
        <div>
          @if($laporanKabel->hasPages())
            {{ $laporanKabel->links('pagination::bootstrap-5') }}
          @else
            <nav aria-label="Pagination">
              <ul class="pagination mb-0">
                <li class="page-item disabled"><span class="page-link" aria-hidden="true">&lsaquo;</span></li>
                <li class="page-item active"><span class="page-link">1</span></li>
                <li class="page-item disabled"><span class="page-link" aria-hidden="true">&rsaquo;</span></li>
              </ul>
            </nav>
          @endif
        </div>
      </div>
    </div>
  </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addLaporanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Laporan Kabel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('logistik.laporan-kabel.store') }}">
        @csrf
        <input type="hidden" name="form_mode" value="add">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Pelanggan</label>
              <input type="text" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror" value="{{ old('nama_pelanggan') }}" required>
              @error('nama_pelanggan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Wilayah</label>
              <select name="wilayah" class="form-select @error('wilayah') is-invalid @enderror" required>
                <option value="">Pilih wilayah</option>
                <option value="Klaten" {{ old('wilayah') === 'Klaten' ? 'selected' : '' }}>Klaten</option>
                <option value="Gunung Kidul" {{ old('wilayah') === 'Gunung Kidul' ? 'selected' : '' }}>Gunung Kidul</option>
              </select>
              @error('wilayah')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Teknisi</label>
              <select
                name="employee_id"
                id="employee_id"
                class="form-select @error('employee_id') is-invalid @enderror"
                data-placeholder="Pilih teknisi / karyawan"
                required
              >
                <option value=""></option>
                @foreach($employees as $employee)
                  <option value="{{ $employee->id }}" {{ old('employee_id') === $employee->id ? 'selected' : '' }}>
                    {{ $employee->full_name }}
                  </option>
                @endforeach
              </select>
              @error('employee_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
              @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Tarikan (Meter)</label>
              <input type="number" step="0.01" min="0" name="tarikan_meter" class="form-control @error('tarikan_meter') is-invalid @enderror" value="{{ old('tarikan_meter') }}" required>
              @error('tarikan_meter')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Jenis Kabel</label>
              <select name="jenis_kabel" class="form-select @error('jenis_kabel') is-invalid @enderror" required>
                <option value="">Pilih jenis kabel</option>
                <option value="1c" {{ old('jenis_kabel') === '1c' ? 'selected' : '' }}>1c</option>
                <option value="4c" {{ old('jenis_kabel') === '4c' ? 'selected' : '' }}>4c</option>
                <option value="12c" {{ old('jenis_kabel') === '12c' ? 'selected' : '' }}>12c</option>
              </select>
              @error('jenis_kabel')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Sisa Kabel</label>
              <input type="number" min="0" step="0.01" name="sisa_kabel" class="form-control @error('sisa_kabel') is-invalid @enderror" value="{{ old('sisa_kabel') }}" required>
              @error('sisa_kabel')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Keterangan (Opsional)</label>
              <textarea name="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Contoh: jalur lewat sisi timur">{{ old('keterangan') }}</textarea>
              @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Laporan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editLaporanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Laporan Kabel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="editLaporanForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_mode" value="edit">
        <input type="hidden" name="edit_id" id="edit_id" value="{{ old('edit_id') }}">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Pelanggan</label>
              <input type="text" name="nama_pelanggan" id="edit_nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror" value="{{ old('nama_pelanggan') }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Wilayah</label>
              <select name="wilayah" id="edit_wilayah" class="form-select @error('wilayah') is-invalid @enderror" required>
                <option value="">Pilih wilayah</option>
                <option value="Klaten" {{ old('wilayah') === 'Klaten' ? 'selected' : '' }}>Klaten</option>
                <option value="Gunung Kidul" {{ old('wilayah') === 'Gunung Kidul' ? 'selected' : '' }}>Gunung Kidul</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Teknisi</label>
              <select
                name="employee_id"
                id="edit_employee_id"
                class="form-select @error('employee_id') is-invalid @enderror"
                data-placeholder="Pilih teknisi / karyawan"
                required
              >
                <option value=""></option>
                @foreach($employees as $employee)
                  <option value="{{ $employee->id }}" {{ old('employee_id') === $employee->id ? 'selected' : '' }}>
                    {{ $employee->full_name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" id="edit_alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label">Tarikan (Meter)</label>
              <input type="number" step="0.01" min="0" name="tarikan_meter" id="edit_tarikan_meter" class="form-control @error('tarikan_meter') is-invalid @enderror" value="{{ old('tarikan_meter') }}" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Jenis Kabel</label>
              <select name="jenis_kabel" id="edit_jenis_kabel" class="form-select @error('jenis_kabel') is-invalid @enderror" required>
                <option value="">Pilih jenis kabel</option>
                <option value="1c" {{ old('jenis_kabel') === '1c' ? 'selected' : '' }}>1c</option>
                <option value="4c" {{ old('jenis_kabel') === '4c' ? 'selected' : '' }}>4c</option>
                <option value="12c" {{ old('jenis_kabel') === '12c' ? 'selected' : '' }}>12c</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Sisa Kabel</label>
              <input type="number" min="0" step="0.01" name="sisa_kabel" id="edit_sisa_kabel" class="form-control @error('sisa_kabel') is-invalid @enderror" value="{{ old('sisa_kabel') }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Keterangan (Opsional)</label>
              <textarea name="keterangan" id="edit_keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Contoh: jalur lewat sisi timur">{{ old('keterangan') }}</textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteLaporanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yakin mau hapus laporan untuk <strong id="deleteLaporanName">-</strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <form method="POST" id="deleteLaporanForm">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const addModalEl = document.getElementById('addLaporanModal');
    const editModalEl = document.getElementById('editLaporanModal');
    const deleteModalEl = document.getElementById('deleteLaporanModal');
    const editFormEl = document.getElementById('editLaporanForm');
    const deleteFormEl = document.getElementById('deleteLaporanForm');
    const deleteNameEl = document.getElementById('deleteLaporanName');
    const updateUrlTemplate = @json(route('logistik.laporan-kabel.update', ['laporanKabel' => '__ID__']));
    const $employeeSelect = $('#employee_id');
    const $editEmployeeSelect = $('#edit_employee_id');

    if ($employeeSelect.length) {
      $employeeSelect.select2({
        placeholder: $employeeSelect.data('placeholder') || 'Pilih teknisi / karyawan',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#addLaporanModal')
      });
    }

    if ($editEmployeeSelect.length) {
      $editEmployeeSelect.select2({
        placeholder: $editEmployeeSelect.data('placeholder') || 'Pilih teknisi / karyawan',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#editLaporanModal')
      });
    }

    const applyEditData = (item) => {
      if (!item || !editFormEl) return;

      const action = updateUrlTemplate.replace('__ID__', item.id);
      editFormEl.setAttribute('action', action);

      document.getElementById('edit_id').value = item.id ?? '';
      document.getElementById('edit_nama_pelanggan').value = item.nama_pelanggan ?? '';
      document.getElementById('edit_wilayah').value = item.wilayah ?? '';
      document.getElementById('edit_alamat').value = item.alamat ?? '';
      document.getElementById('edit_tarikan_meter').value = item.tarikan_meter ?? '';
      document.getElementById('edit_jenis_kabel').value = item.jenis_kabel ?? '';
      document.getElementById('edit_sisa_kabel').value = item.sisa_kabel ?? '';
      document.getElementById('edit_keterangan').value = item.keterangan ?? '';

      if ($editEmployeeSelect.length) {
        $editEmployeeSelect.val(item.employee_id ?? '').trigger('change');
      }
    };

    document.querySelectorAll('.btn-edit-laporan').forEach((btn) => {
      btn.addEventListener('click', function () {
        const raw = this.getAttribute('data-item') || '{}';
        let item = {};
        try {
          item = JSON.parse(raw);
        } catch (e) {
          item = {};
        }

        applyEditData(item);

        if (editModalEl && window.bootstrap) {
          const modal = new bootstrap.Modal(editModalEl);
          modal.show();
        }
      });
    });

    document.querySelectorAll('.btn-delete-laporan').forEach((btn) => {
      btn.addEventListener('click', function () {
        const url = this.getAttribute('data-url') || '';
        const name = this.getAttribute('data-name') || '-';

        if (deleteFormEl) {
          deleteFormEl.setAttribute('action', url);
        }
        if (deleteNameEl) {
          deleteNameEl.textContent = name;
        }

        if (deleteModalEl && window.bootstrap) {
          const modal = new bootstrap.Modal(deleteModalEl);
          modal.show();
        }
      });
    });

    @if($errors->any())
      const formMode = @json(old('form_mode'));
      if (formMode === 'edit') {
        const oldEdit = {
          id: @json(old('edit_id')),
          nama_pelanggan: @json(old('nama_pelanggan')),
          wilayah: @json(old('wilayah')),
          employee_id: @json(old('employee_id')),
          alamat: @json(old('alamat')),
          tarikan_meter: @json(old('tarikan_meter')),
          jenis_kabel: @json(old('jenis_kabel')),
          sisa_kabel: @json(old('sisa_kabel')),
          keterangan: @json(old('keterangan')),
        };

        applyEditData(oldEdit);

        if (editModalEl && window.bootstrap) {
          const modal = new bootstrap.Modal(editModalEl);
          modal.show();
        }
      } else if (addModalEl && window.bootstrap) {
        const modal = new bootstrap.Modal(addModalEl);
        modal.show();
      }
    @endif
  });
</script>
@endsection
