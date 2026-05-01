@extends('layouts/layoutMaster')

@section('title', 'History Ticket')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])

<style>
  .ticket-card {
    border-left: 4px solid #696cff;
    transition: all 0.3s ease;
  }
  
  .ticket-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .ticket-card.deleted {
    border-left-color: #dc3545;
    background-color: #fff5f5;
  }
  
  .status-badge {
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
    margin-right: 8px;
  }
  
  .status-pending {
    background: rgba(255, 159, 67, 0.12);
    color: #ff9f43;
  }
  
  .status-assigned {
    background: rgba(0, 207, 232, 0.12);
    color: #00cfe8;
  }
  
  .status-progress {
    background: rgba(105, 108, 255, 0.12);
    color: #696cff;
  }
  
  .status-finished {
    background: rgba(40, 199, 111, 0.12);
    color: #28c76f;
  }
  
  .status-closed {
    background: #dc3545 !important;
    color: white !important;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  }
  
  .timeline-item {
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  
  .timeline-item:hover {
    background-color: #f8f9fa;
  }
  
  .deleted-badge {
    background: #dc3545;
    color: white;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 8px;
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1"><i class="ri-history-line me-2"></i>History Ticket</h5>
      <p class="mb-0 text-muted small">Riwayat perubahan status tiket pelanggan</p>
    </div>
    <span class="badge bg-label-primary">{{ $tickets->count() }} Tiket</span>
  </div>

  <div class="card-body">
    @forelse($tickets as $ticket)
        @php
          $isDeleted = is_null($ticket->pelanggan);
          $lastStatus = $ticket->statusLogs->last()?->status;
          $isClosed = $lastStatus === 'approved';
        @endphp
        
        <div class="card ticket-card mb-3 {{ $isDeleted ? 'deleted' : '' }}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h5 class="mb-1">
                  <i class="ri-ticket-2-line me-2"></i>
                  @if($isDeleted)
                    <span class="text-danger">Pelanggan Tidak Ditemukan</span>
                    <span class="deleted-badge">DELETED</span>
                  @else
                    {{ $ticket->pelanggan->nama_lengkap }}
                    <small class="text-muted ms-2">({{ $ticket->pelanggan->nomer_id }})</small>
                  @endif
                </h5>
                
                @if(!$isDeleted)
                  <p class="text-muted mb-0 small">
                    <i class="ri-phone-line me-1"></i>{{ $ticket->pelanggan->no_whatsapp ?? '-' }}
                  </p>
                @endif
              </div>
              
              @if($isClosed)
                <span class="status-closed">
                  <i class="ri-lock-line me-1"></i>CLOSED
                </span>
              @endif
            </div>
            
            <div class="border-top pt-3">
              <h6 class="text-muted mb-2 small">
                <i class="ri-time-line me-1"></i>Timeline Status
              </h6>
              
              @if($ticket->statusLogs->isEmpty())
                <p class="text-muted mb-0">Belum ada riwayat status</p>
              @else
                <ul class="list-unstyled mb-0">
                  @foreach($ticket->statusLogs as $log)
                    <li class="timeline-item mb-2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          @php
                            $statusClass = match(strtolower($log->status)) {
                              'pending' => 'status-pending',
                              'assigned' => 'status-assigned',
                              'progress' => 'status-progress',
                              'finished' => 'status-finished',
                              'approved' => 'status-closed',
                              default => 'status-pending'
                            };
                            
                            $statusIcon = match(strtolower($log->status)) {
                              'pending' => 'ri-time-line',
                              'assigned' => 'ri-user-add-line',
                              'progress' => 'ri-loader-4-line',
                              'finished' => 'ri-check-line',
                              'approved' => 'ri-checkbox-circle-line',
                              default => 'ri-record-circle-line'
                            };
                            
                            $statusText = match(strtolower($log->status)) {
                              'approved' => 'CLOSED',
                              default => ucfirst($log->status)
                            };
                          @endphp
                          
                          <span class="status-badge {{ $statusClass }}">
                            <i class="{{ $statusIcon }} me-1"></i>{{ $statusText }}
                          </span>
                        </div>
                        
                        <small class="text-muted">
                          <i class="ri-calendar-line me-1"></i>{{ $log->created_at->format('d M Y H:i') }}
                          <span class="mx-1">-></span>
                          <i class="ri-user-line me-1"></i>{{ $log->user?->name ?? 'Unknown' }}
                        </small>
                      </div>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>
    @empty
        <div class="text-center py-5">
          <i class="ri-inbox-line" style="font-size: 4rem; opacity: 0.3;"></i>
          <p class="text-muted mt-3 mb-0">Belum ada history ticket</p>
        </div>
    @endforelse
  </div>
</div>
@endsection
