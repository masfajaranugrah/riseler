@extends('layouts/layoutMaster')

@section('title', 'Laporan Kabel')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('page-style')
<style>
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
  .lk-filter-actions .btn {
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding-top: 0;
    padding-bottom: 0;
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
        <div class="d-flex gap-2 flex-wrap">
          {{-- Export Excel (streaming, tidak timeout) --}}
          <button
            type="button"
            id="btnExportExcel"
            class="btn btn-outline-secondary"
            data-export-url="{{ route('logistik.laporan-kabel.export.excel', request()->only(['date', 'month', 'year', 'wilayah', 'search'])) }}"
          >
            <i class="ri-file-excel-line me-1"></i> Export Excel
          </button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLaporanModal">
            <i class="ri-add-line me-1"></i> Add Laporan
          </button>
        </div>
      </div>
    </div>

    <div class="lk-main-body">
      @if(session('error'))
        <div class="alert alert-danger mb-3">
          {{ session('error') }}
        </div>
      @endif
      <div class="row g-3 mb-3">
        <div class="col-12">
          <div class="card lk-subcard">
            <div class="card-body">
              <form method="GET" action="{{ route('logistik.laporan-kabel.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-2">
                  <label class="form-label" style="font-weight: 500;">Tanggal</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ri-calendar-event-line"></i></span>
                    <input type="text" class="form-control lk-flatpickr-date bg-white" name="date" value="{{ request('date') }}" placeholder="Pilih Tanggal">
                  </div>
                </div>
                <div class="col-12 col-md-2">
                  <label class="form-label" style="font-weight: 500;">Bulan</label>
                  <select name="month" class="form-select">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                      <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12 col-md-2">
                  <label class="form-label" style="font-weight: 500;">Tahun</label>
                  <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), 2020) as $y)
                      <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                        {{ $y }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12 col-md-2">
                  <label class="form-label" style="font-weight: 500;">Wilayah</label>
                  <select name="wilayah" class="form-select">
                    <option value="">Semua Wilayah</option>
                    <option value="Klaten" {{ request('wilayah') === 'Klaten' ? 'selected' : '' }}>Klaten</option>
                    <option value="Gunung Kidul" {{ request('wilayah') === 'Gunung Kidul' ? 'selected' : '' }}>Gunung Kidul</option>
                    <option value="Boyolali" {{ request('wilayah') === 'Boyolali' ? 'selected' : '' }}>Boyolali</option>
                  </select>
                </div>
                <div class="col-12 col-md-2">
                  <label class="form-label" style="font-weight: 500;">Pencarian</label>
                  <input type="search" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama, alamat...">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 lk-filter-actions">
                  <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                  <a href="{{ route('logistik.laporan-kabel.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-3" title="Reset">
                    <i class="ri-refresh-line"></i>
                  </a>
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
            <th>Detail</th>
            <th>Nama Pelanggan</th>
            <th>Nama Teknisi</th>
            <th>Wilayah</th>
            <th>Alamat</th>
            <th>Tarikan (Meter)</th>
            <th>Jenis Kabel</th>
            <th>Sisa Kabel</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($laporanKabel as $item)
            <tr>
              <td>{{ $laporanKabel->firstItem() + $loop->index }}</td>
              <td class="text-end">
                @php
                  $editPayload = json_encode([
                    'id' => $item->id,
                    'nama_pelanggan' => $item->nama_pelanggan,
                    'employee_name' => optional($item->employee)->full_name,
                    'wilayah' => $item->wilayah,
                    'employee_id' => $item->employee_id,
                    'alamat' => $item->alamat,
                    'tarikan_meter' => $item->tarikan_meter,
                    'jenis_kabel' => $item->jenis_kabel,
                    'sisa_kabel' => $item->sisi_core,
                    'keterangan' => $item->keterangan,
                    'tanggal_input' => optional($item->created_at)->format('d M Y H:i'),
                  ]);
                @endphp
                <button
                  type="button"
                  class="btn btn-sm btn-icon text-secondary border-0 btn-detail-laporan me-1"
                  data-detail="{{ $editPayload }}"
                  title="Detail"
                >
                  <i class="ri-eye-line fs-5"></i>
                </button>
              </td>
              <td>{{ $item->nama_pelanggan }}</td>
              <td>{{ optional($item->employee)->full_name ?: '' }}</td>
              <td>{{ $item->wilayah ?: '-' }}</td>
              <td>{{ $item->alamat }}</td>
              <td>{{ rtrim(rtrim(number_format((float) $item->tarikan_meter, 2, '.', ''), '0'), '.') }} M</td>
              <td><span class="badge bg-label-primary text-uppercase">{{ $item->jenis_kabel }}</span></td>
              <td>{{ rtrim(rtrim(number_format((float) $item->sisi_core, 2, '.', ''), '0'), '.') }} M</td>
              <td class="text-end">
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
              <td colspan="10" class="text-center text-muted py-4">Belum ada laporan kabel.</td>
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
                <option value="Boyolali" {{ old('wilayah') === 'Boyolali' ? 'selected' : '' }}>Boyolali</option>
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
                <option value="Boyolali" {{ old('wilayah') === 'Boyolali' ? 'selected' : '' }}>Boyolali</option>
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

<div class="modal fade" id="detailLaporanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content overflow-hidden">
      <div class="modal-header bg-dark p-3">
        <h5 class="modal-title text-white fs-6">
          <i class="ri-information-line me-2"></i> Detail Laporan Kabel
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light p-4">
        
        <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; border: 1px solid #eee !important;">
          <div class="card-body text-center p-4">
            <div class="avatar avatar-xl d-inline-block mb-3" style="width: 80px; height: 80px;">
              <span class="avatar-initial rounded-circle bg-dark text-white shadow-sm" id="detailAvatarInitial" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 0 20px rgba(17,24,39,0.15) !important;">T</span>
            </div>
            <h4 class="mb-2 text-dark fw-bold" id="detailHeaderNamaPelanggan">TIMAH</h4>
            <span class="badge bg-danger rounded-pill px-3 py-2 fw-medium" id="detailHeaderWilayah">
              <i class="ri-map-pin-line me-1"></i> Gunung Kidul
            </span>
          </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid #111827 !important;">
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
              <i class="ri-user-line fs-5 me-2 text-dark"></i>
              <h6 class="mb-0 fw-bold text-dark" style="letter-spacing: 0.5px;">INFORMASI DASAR</h6>
            </div>
            <hr class="border-dark opacity-100 mt-0 mb-3" style="border-top-width: 2px;">
            
            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-layout-grid-line me-2 text-muted"></i> Nama Lengkap
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailNamaPelangganRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-user-star-line me-2 text-muted"></i> Nama Teknisi
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailNamaTeknisiRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                 <i class="ri-map-pin-2-line me-2 text-muted"></i> Wilayah
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailWilayahRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                 <i class="ri-map-pin-line me-2 text-muted"></i> Alamat
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailAlamatRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-route-line me-2 text-muted"></i> Tarikan Meter
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailTarikanRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-price-tag-3-line me-2 text-muted"></i> Jenis Kabel
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailJenisKabelRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-dashboard-3-line me-2 text-muted"></i> Sisa Kabel
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailSisaKabelRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center mb-3">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-file-text-line me-2 text-muted"></i> Keterangan
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailKeteranganRow">-</div>
            </div>

            <div class="separator-border mb-3" style="border-bottom: 1px solid #f1f1f1;"></div>

            <div class="row align-items-center">
              <div class="col-5 text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ri-calendar-line me-2 text-muted"></i> Tanggal Input
              </div>
              <div class="col-7 fw-semibold text-dark" id="detailTanggalInputRow">-</div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {

    // Configure Flatpickr
    flatpickr('.lk-flatpickr-date', {
      dateFormat: 'Y-m-d',
      allowInput: true
    });

    const addModalEl = document.getElementById('addLaporanModal');
    const editModalEl = document.getElementById('editLaporanModal');
    const detailModalEl = document.getElementById('detailLaporanModal');
    const editFormEl = document.getElementById('editLaporanForm');
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
        const employeeValue = (item.employee_id ?? '').toString();
        $editEmployeeSelect.val(employeeValue).trigger('change.select2');
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

        if (window.Swal) {
          Swal.fire({
            title: 'Hapus laporan?',
            text: 'Yakin mau hapus laporan untuk ' + name + '?',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            customClass: {
              confirmButton: 'btn btn-danger me-2',
              cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
          }).then((result) => {
            if (!result.isConfirmed) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = @json(csrf_token());

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
          });
        }
      });
    });

    document.querySelectorAll('.btn-detail-laporan').forEach((btn) => {
      btn.addEventListener('click', function () {
        const raw = this.getAttribute('data-detail') || '{}';
        let item = {};
        try {
          item = JSON.parse(raw);
        } catch (e) {
          item = {};
        }

        document.getElementById('detailHeaderNamaPelanggan').textContent = item.nama_pelanggan || '-';
        
        // Setup Avatar Initial
        const initial = item.nama_pelanggan ? item.nama_pelanggan.charAt(0).toUpperCase() : '?';
        document.getElementById('detailAvatarInitial').textContent = initial;

        // Setup Wilayah Badge
        document.getElementById('detailHeaderWilayah').innerHTML = '<i class="ri-map-pin-line me-1"></i> ' + (item.wilayah || 'Tidak diketahui');

        document.getElementById('detailNamaPelangganRow').textContent = item.nama_pelanggan || '-';
        document.getElementById('detailNamaTeknisiRow').textContent = item.employee_name || '-';
        document.getElementById('detailWilayahRow').textContent = item.wilayah || '-';
        document.getElementById('detailAlamatRow').textContent = item.alamat || '-';
        document.getElementById('detailTarikanRow').textContent = item.tarikan_meter ? item.tarikan_meter + ' M' : '-';
        document.getElementById('detailJenisKabelRow').textContent = item.jenis_kabel ? item.jenis_kabel.toUpperCase() : '-';
        document.getElementById('detailSisaKabelRow').textContent = item.sisa_kabel ? item.sisa_kabel + ' M' : '-';
        document.getElementById('detailKeteranganRow').textContent = item.keterangan || '-';
        document.getElementById('detailTanggalInputRow').textContent = item.tanggal_input || '-';

        if (detailModalEl && window.bootstrap) {
          const modal = new bootstrap.Modal(detailModalEl);
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

    // ---- EXCEL ----
    const btnExcel = document.getElementById('btnExportExcel');
    if (btnExcel) {
      btnExcel.addEventListener('click', function () {
        const url = this.getAttribute('data-export-url');
        if (!url) return;

        // Gunakan window.location agar jika terjadi error, tampil di layar.
        // Jika sukses, browser akan memunculkan "Save As" dan halaman tetap aman.
        window.location.href = url;
      });
    }

  });
</script>
@endsection
