<?php


use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\WebviewKwitansiController;

use App\Http\Controllers\TagihanReadController;

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MobileTagihanController;
use App\Http\Controllers\DirecturMarketingController;
use App\Http\Controllers\LaporanHarianController;
use App\Http\Controllers\KasRegistrasiController;

use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\LoginLogs;
use App\Http\Controllers\LaporanKabelController;
use App\Http\Controllers\KaryawanHomeController;

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\IklanController;
use App\Http\Controllers\OutstandingController;

use App\Http\Controllers\CustomerTagihanController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LaporanController;

use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TagihanKwitansiController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketStatusLog;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomBroadcastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminProfileController;

Route::middleware('web')->group(function () {
    Route::post('/broadcasting/auth', function (Request $request) {
        return \Illuminate\Support\Facades\Broadcast::auth($request);
    });
});



// Main Page Route
Route::get('/', function () {
    return redirect()->route('login');
});
// Maintenance Route
// Route::get('/maintenance', function () {
//     return view('content.apps.maintenance');
// })->name('maintenance');

// // Main Page Route - Auto redirect to maintenance
// Route::get('/', function () {
//     return redirect()->route('maintenance');
// });

// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// auth
Route::get('dashboard/auth/login', [AuthController::class, 'indexLogin'])->name('login')->middleware('guest:customer,web'); // tambahkan semua guard yang ingin dicek
Route::get('/karyawan/auth', [AuthController::class, 'indexLoginKaryawan'])->name('login.karyawan')->middleware('guest:customer,web');
Route::get('/karyawan/login', fn () => redirect()->route('login.karyawan'))->middleware('guest:customer,web');

Route::get('dashboard/auth/register', [AuthController::class, 'indexRegister'])->name('register');
Route::post('dashboard/auth/login', [AuthController::class, 'login'])->name('login.create');
Route::post('/karyawan/auth', [AuthController::class, 'login'])->name('login.karyawan.post');
Route::post('/karyawan/login', [AuthController::class, 'login']);
Route::post('dashboard/auth/register', [AuthController::class, 'register'])->name('register.create');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/customer/logout', [AuthController::class, 'logoutCustomer'])->name('customer.logout');

// auth pelanggan




// Dashboard Welcome
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.welcome');
});



Route::prefix('/pelanggan/jernihnet')->group(function () {

    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [AuthController::class, 'loginMember'])
            ->name('users.member');

        Route::post('/login', [AuthController::class, 'loginMem'])
            ->name('login.member.post');
    });

    // Route yang sudah login customer
    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard/tagihan', [TagihanController::class, 'index'])
            ->name('customer.tagihan.legacy');
    });
});



// laravel example
Route::middleware(['auth', 'role:administrator,admin,verifikasi'])->group(function () {

    Route::get('/dashboard/admin/employees', [EmployeeController::class, 'index'])->name('karyawan.index');
    Route::get('/dashboard/admin/employees/create', [EmployeeController::class, 'create'])->name('karyawan.create');
    Route::get('/dashboard/admin/employees/data', [EmployeeController::class, 'getDataJson'])->name('employees.data');
    Route::get('/dashboard/admin/employees/upload/data', [EmployeeController::class, 'upload']);
    Route::get('/dashboard/admin/employees/image/{id}/{type}', [EmployeeController::class, 'showImage'])->name('karyawan.image');
    Route::post('/dashboard/admin/employees/create', [EmployeeController::class, 'store'])->name('employees.create.post');
    Route::get('/dashboard/admin/employees/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/dashboard/admin/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/dashboard/admin/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    // Route POST untuk proses import
    Route::post('/dashboard/admin/employees/import-excel', [EmployeeController::class, 'importExcel'])->name('karyawan.excel');

    Route::prefix('/dashboard/admin/paket')->group(function () {
        Route::get('/', [PaketController::class, 'index'])->name('paket.index');
        Route::get('/create', [PaketController::class, 'create'])->name('paket.add');
        Route::post('/', [PaketController::class, 'store'])->name('paket.store');
        Route::get('/{id}/edit', [PaketController::class, 'edit'])->name('paket.edit');
        Route::put('/{id}', [PaketController::class, 'update'])->name('paket.update');
        Route::delete('/{id}', [PaketController::class, 'destroy'])->name('paket.destroy');
    });

    Route::prefix('/dashboard/admin/tagihan')->group(function () {
        Route::get('/', [TagihanController::class, 'index'])->name('tagihan.get');
        Route::get('/lunas', [TagihanController::class, 'lunas'])->name('tagihan.lunas');
        Route::get('/proses', [TagihanController::class, 'proses'])->name('tagihan.proses');
        Route::get('/add/tagihan', [TagihanController::class, 'indexAddTagihan'])->name('tagihan.add');
        Route::get('/tagihan/data', [TagihanController::class, 'getData'])->name('tagihan.data');
        Route::put('/{id}/update', [TagihanController::class, 'update'])->name('tagihan.update');
        Route::post('/konfirmasi/{id}', [TagihanController::class, 'konfirmasiBayar']);
        Route::post('/tagihan/store', [TagihanController::class, 'store'])->name('tagihan.store');
        Route::post('/{id}/bayar', [TagihanController::class, 'updateStatus'])->name('tagihan.bayar');
        Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
        Route::delete('/tagihan-lunas/{id}', [TagihanController::class, 'destroyLunas'])->name('tagihan.destroyLunas');
        Route::post('/{id}/bayar', [TagihanController::class, 'konfirmasiBayar'])->name('tagihan.konfirmasi');
        Route::post('/{id}/kembalikan-belum-bayar', [TagihanController::class, 'updateStatusToBelumBayar'])->name('tagihan.kembalikan.belum.bayar');
        Route::post('/{id}/update-paket', [TagihanController::class, 'updatePaket'])->name('tagihan.update.paket');
        // Route::get('/ostanding', [OutstandingController::class, 'index'])->name('tagihan.outstanding');
        Route::get('/pdf', [TagihanController::class, 'lihat']);
        // Broadcast Tagihan - AJAX endpoints
        Route::get('/broadcast/count', [TagihanController::class, 'getBroadcastCount'])->name('tagihan.broadcast.count');
        Route::get('/broadcast/ids', [TagihanController::class, 'getBroadcastIds'])->name('tagihan.broadcast.ids');
        Route::post('/broadcast/store', [TagihanController::class, 'massStore'])->name('tagihan.broadcast.store'); // Point to massStore which supports JSON & Batching
        Route::get('/export-belum-lunas', [TagihanController::class, 'exportBelumLunas'])->name('tagihan.export.belumlunas');


        // Status Baca Tagihan
        // Route::get('/status-baca', [TagihanReadController::class, 'index'])->name('tagihan.read.status');
        // Route::get('/status-baca/data', [TagihanReadController::class, 'getDataJson'])->name('tagihan.read.data');


    });
    Route::prefix('/dashboard/admin/laporan')->group(function () {

        // Laporan
        Route::get('/tagihan', [LaporanController::class, 'tagihan'])->name('laporan.tagihan');
        Route::get('/pembayaran', [LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');

        Route::get('/tagihan/export', [LaporanController::class, 'exportExcel'])->name('laporan.tagihan.export');

        // Laporan Harian
        Route::get('/harian', [LaporanHarianController::class, 'index'])->name('laporan.harian');
        Route::get('/harian/export', [LaporanHarianController::class, 'export'])->name('laporan.harian.export');
    });
    Route::prefix('/dashboard/admin/laporan')->group(function () {
        Route::get('/tagihan/kwitansi', [TagihanKwitansiController::class, 'index'])->name('laporan.kwitansi.index');
        Route::get('/tagihan/kwitansi/export', [TagihanKwitansiController::class, 'exportExcel'])->name('laporan.kwitansi.export');
    });

});

Route::middleware(['auth', 'role:admin,administrator'])->group(function () {

    // pelanggan
    Route::prefix('/dashboard/admin/pelanggan')->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('pelanggan');
        Route::get('/status', [PelangganController::class, 'status'])->name('pelanggan.status.active');
        Route::get('upload/data', [PelangganController::class, 'upload'])->name('pelanggan.data');
        Route::get('/status/data', [PelangganController::class, 'getDataAprove'])->name('pelanggan.status.data');
        Route::get('/create', [PelangganController::class, 'create'])->name('add-pelanggan');
        Route::post('/store', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('/edit/{id}', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('/update/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::get('/get-paket', [PelangganController::class, 'getPaket'])->name('pelanggangetPaket');
        Route::delete('/delete/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.delete');
        // ✅ Route POST untuk proses import
        Route::post('/import-excel', [PelangganController::class, 'importExcel'])->name('pelanggan.excel');

    });

    Route::prefix('/dashboard/admin/users')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('users.index'); // list users
        Route::get('/create', [TeamController::class, 'create'])->name('users.create'); // form add user
        Route::post('/store', [TeamController::class, 'register'])->name('users.store'); // simpan user baru
        Route::get('/edit/{user}', [TeamController::class, 'edit'])->name('users.edit'); // edit user
        Route::put('/update/{user}', [TeamController::class, 'update'])->name('users.update'); // update user
        Route::delete('/delete/{user}', [TeamController::class, 'destroy'])->name('users.destroy'); // hapus user
        Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('users.show.edit');
        Route::put('/{id}', [TeamController::class, 'update'])->name('users.update.id');

    });
});

Route::middleware(['auth', 'role:customer_service'])->group(function () {

    Route::prefix('/dashboard/cs/tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('tickets.indexs');
        Route::get('/tickets/json', [TicketController::class, 'ticketsJson'])->name('tickets.json');
        Route::get('/create', [TicketController::class, 'create'])->name('tickets.creates');
        Route::post('/store', [TicketController::class, 'store'])->name('tickets.stores');

        Route::get('/edit/{ticket}', [TicketController::class, 'edit'])->name('tiket.edit');
        Route::put('/update/{ticket}', [TicketController::class, 'update'])->name('tickets.updates');
        Route::delete('/delete/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroys');

        Route::get('/finished', [TicketController::class, 'finished'])->name('finished');
        Route::get('/approved', [TicketController::class, 'approved'])->name('approved');


    });
});

Route::prefix('/dashboard/admin/history')->group(function () {
    Route::get('/tickets', [TicketStatusLog::class, 'index'])->name('history.index');
    Route::get('/create', [TicketStatusLog::class, 'create'])->name('tickets.create');
    Route::post('/store', [TicketStatusLog::class, 'store'])->name('tickets.store');
    Route::get('/{ticket}', [TicketStatusLog::class, 'show'])->name('tickets.show');
    Route::get('/login-log', [LoginLogs::class, 'LogLogin'])->name('login.log');




});

Route::middleware(['auth', 'role:team,karyawan'])->group(function () {

    Route::prefix('dashboard/karyawan/jobs')->group(function () {
        Route::get('/', [JobsController::class, 'index'])->name('jobs.index');
        Route::get('/create', [JobsController::class, 'create'])->name('jobs.create');
        Route::get('/approved-jobs', [JobsController::class, 'approved'])->name('jobs.approved');
        Route::post('/store', [JobsController::class, 'store'])->name('jobs.store');
        Route::get('/preview-jobs/{ticket}', [JobsController::class, 'show'])->name('jobs.show');
        Route::get('/edit/{ticket}', [JobsController::class, 'edit'])->name('jobs.edit');
        Route::put('/update/{ticket}', [JobsController::class, 'update'])->name('jobs.update');
        Route::delete('/delete/{ticket}', [JobsController::class, 'destroy'])->name('jobs.destroy');
        Route::patch('{id}/auto-update', [JobsController::class, 'autoUpdateStatus'])->name('jobs.autoUpdateStatus');
    });
});

Route::get('/kwitansi/{filename}', function ($filename) {
    $path = storage_path('app/public/kwitansi/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});

Route::prefix('dashboard/admin/incomes')->group(function () {
    Route::get('/', [IncomeController::class, 'index'])->name('income.index');
    Route::get('/potongan-ppn', [IncomeController::class, 'potonganPpn'])->name('income.ppn');
    Route::get('/export', [IncomeController::class, 'export'])->name('income.export');
    Route::get('/export/dedicated', [IncomeController::class, 'exportDedicated'])->name('income.export.dedicated');
    Route::get('/export/monthly', [IncomeController::class, 'exportMonthly'])->name('income.export.monthly');
    Route::get('/add', [IncomeController::class, 'create'])->name('income.create');
    Route::post('/', [IncomeController::class, 'store'])->name('income.store');
    Route::get('{id}', [IncomeController::class, 'edit'])->name('income.edit');
    Route::put('{id}', [IncomeController::class, 'update'])->name('income.update');
    Route::delete('{id}', [IncomeController::class, 'destroy'])->name('income.delete');
});


// Resource route untuk pengeluaran
Route::prefix('dashboard/admin/expenses')->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('keluar.index');       // List semua pengeluaran
    Route::get('/create', [ExpenseController::class, 'create'])->name('keluar.create');  // Form tambah
    Route::post('/store', [ExpenseController::class, 'store'])->name('keluar.store');    // Simpan pengeluaran
    Route::get('/{id}/edit', [ExpenseController::class, 'edit'])->name('keluar.edit');  // Form edit
    Route::put('/{id}', [ExpenseController::class, 'update'])->name('keluar.update');   // Update pengeluaran
    Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('keluar.destroy');  // Hapus
    Route::get('/export/monthly', [ExpenseController::class, 'exportMonthly'])->name('keluar.export.monthly');
    Route::get('/export/date-range', [ExpenseController::class, 'exportDateRange'])->name('keluar.export.daterange');
});

Route::prefix('dashboard/admin/hutangs')->group(function () {
    Route::get('/', [HutangController::class, 'index'])->name('hutang.index');
    Route::get('/create', [HutangController::class, 'create'])->name('hutang.create');
    Route::post('/store', [HutangController::class, 'store'])->name('hutang.store');
});

Route::prefix('dashboard/admin/pembukuan')->group(function () {
    Route::get('/masuk', [LedgerController::class, 'index'])->name('pembukuan.index');
    Route::get('/bbp-ringkasan', [LedgerController::class, 'bbpRingkasan'])->name('pembukuan.bbp.ringkasan');
    Route::get('/masuk/export', [LedgerController::class, 'exportExcelBukuPembantu'])->name('pembukuan.masuk.export');
    Route::get('/keluar', [LedgerController::class, 'keluar'])->name('pembukuan.keluar');
    Route::get('/total', [LedgerController::class, 'total'])->name('pembukuan.total');
    Route::get('/total/export', [LedgerController::class, 'exportExcel'])->name('pembukuan.total.export');

    // Saldo Awal Routes
    Route::get('/saldo-awal', [\App\Http\Controllers\SaldoAwalController::class, 'index'])->name('saldo-awal.index');
    Route::post('/saldo-awal/store', [\App\Http\Controllers\SaldoAwalController::class, 'store'])->name('saldo-awal.store');
    Route::put('/saldo-awal/{id}', [\App\Http\Controllers\SaldoAwalController::class, 'update'])->name('saldo-awal.update');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/home', [KaryawanHomeController::class, 'index'])->name('karyawan.home');
    Route::get('/dashboard/karyawan/home', fn () => redirect()->route('karyawan.home'));

    Route::get('/dashboard/karyawan/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/dashboard/karyawan/absensi/capture', [AbsensiController::class, 'capture'])->name('absensi.capture');
    Route::get('/dashboard/karyawan/data/absensi', [AbsensiController::class, 'getAll'])->name('absensi.indexAll');
    Route::post('/absensi/submit', [AbsensiController::class, 'submit'])->name('absensi.kirim');
    Route::delete('/absensi/{absensi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

    Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
});

Route::prefix('/dashboard/admin/users')->group(function () {
// Route::get('/', [TeamController::class, 'index']);
// Route::get('/create', [TeamController::class, 'create']);
// Route::post('/store', [TeamController::class, 'register']);
// Route::get('/edit/{user}', [TeamController::class, 'edit'])->name('users.get.edit.v2');
// Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('users.edit.v2');
// Route::put('/{id}', [TeamController::class, 'update'])->name('users.edit.data.v2');

});

Route::middleware(['auth:customer', 'customer_status'])->group(function () {
    Route::get('dashboard/customer/tagihan/home', [CustomerTagihanController::class, 'indexHome'])->name('customer.tagihan.v1');
    Route::get('dashboard/customer/tagihan/selesai', [CustomerTagihanController::class, 'selesai'])->name('customer.tagihan.lunas');
    Route::get('dashboard/customer/tagihan', [CustomerTagihanController::class, 'index'])->name('customer.tagihan');
    Route::get('dashboard/customer/tagihan/json', [CustomerTagihanController::class, 'getTagihanJson'])->name('customer.tagihan.json');
    Route::get('dashboard/customer/tagihan/selesai/json', [CustomerTagihanController::class, 'getInvoiceJson'])->name('customer.tagihan.selesai.json');
    Route::put('dashboard/customer/tagihan/{id}', [CustomerTagihanController::class, 'update'])
        ->name('customer.tagihan.update');
    Route::get('/customer/tagihan/{id}', [CustomerTagihanController::class, 'show'])->name('customer.tagihan.show');

    // Chat pelanggan dengan admin
    Route::get('dashboard/customer/chat', [ChatController::class, 'user'])->name('customer.chat');


    // Chat pelanggan dengan Admin untuk Billing (terpisah dari CS)
    Route::get('dashboard/customer/chat-billing', [ChatController::class, 'customerBillingChat'])->name('customer.chat.billing');

    // Profile Customer
    Route::get('dashboard/customer/profile', [CustomerTagihanController::class, 'profile'])->name('customer.profile');

    // Riwayat Pembayaran
    Route::get('dashboard/customer/riwayat', [CustomerTagihanController::class, 'riwayat'])->name('customer.riwayat');

    // FAQ / Bantuan
    Route::get('dashboard/customer/faq', [CustomerTagihanController::class, 'faq'])->name('customer.faq');


});
// Admin Billing Chat API endpoints (for customer and admin)
Route::middleware('auth:web,customer')->prefix('admin-chat')->group(function () {
    Route::get('/messages/{userId?}', [ChatController::class, 'getAdminChatMessages'])->name('admin.chat.messages');
    Route::post('/send', [ChatController::class, 'sendAdminChat'])->name('admin.chat.send');
    Route::put('/messages/{messageId}', [ChatController::class, 'updateAdminChatMessage'])->name('admin.chat.message.update');
    Route::delete('/messages/{messageId}', [ChatController::class, 'deleteAdminChatMessage'])->name('admin.chat.message.delete');
    Route::post('/broadcast', [ChatController::class, 'broadcastAdminChat'])->name('admin.chat.broadcast');
    Route::get('/broadcast/{id}/progress', [ChatController::class, 'getBroadcastProgress'])->name('admin.chat.broadcast.progress');
    Route::post('/mark-read/{userId}', [ChatController::class, 'markReadAdminChat'])->name('admin.chat.markRead');
    Route::get('/unread-count', [ChatController::class, 'getAdminChatUnreadCount'])->name('admin.chat.unreadCount');
    Route::get('/users', [ChatController::class, 'getAdminChatUserList'])->name('admin.chat.users');
});

// Admin Billing Chat Panel (for admin role only)
Route::middleware(['auth', 'role:admin,administrator'])->group(function () {
    Route::get('/dashboard/admin/billing-chat', [ChatController::class, 'adminBilling'])->name('admin.billing.chat');
});


Route::prefix('/pelanggan/jernihnet')->middleware('customer.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginMember'])->name('users.member');
    Route::post('/login', [AuthController::class, 'loginMem'])->name('login.member.post');
});

// Route::post('/save-subscription', [PushSubscriptionController::class, 'store']);

// Route::middleware(['auth:customer'])->get('/customer/tagihan/json', [CustomerTagihanController::class, 'getTagihanJson']);

Route::post('/pelanggan/save-player-id', function (Request $request) {

    $request->validate([
        'nomer_id' => 'required',
        'player_id' => 'required',
    ]);

    $pelanggan = \App\Models\Pelanggan::where('nomer_id', $request->nomer_id)->first();

    if ($pelanggan) {
        $pelanggan->update(['player_id' => $request->player_id]);

        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'not_found'], 404);
});

Route::get('/test-push', function () {

    $playerId = '557a4368-e57a-407b-b479-a33ad32df8a1'; // ganti manual dulu untuk test

    $fields = [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'include_player_ids' => [$playerId],
        'headings' => ['en' => 'Tagihan WIFI'],
        'contents' => ['en' => 'segera dibayar'],
    ];

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
});

// json
Route::get('dashboard/admin/tagihan/data', [TagihanController::class, 'indexGetJson'])->name('tagihan.index');
Route::get('dashboard/admin/tagihan/data/{id}', [TagihanController::class, 'getByIdJson'])->name('tagihan.index.id');

Route::post('/dashboard/admin/tagihan/outstanding', [TagihanController::class, 'outstandingStore'])
    ->name('tagihan.outstandingStore');


Route::post('/dashboard/admin/tagihan/mass-store', [TagihanController::class, 'massStore'])
    ->name('tagihan.massStore');

// news rekeknig

Route::get('/dashboard/admin/rekenings', [RekeningController::class, 'index'])->name('rekenings.index');
Route::get('/dashboard/admin/add/rekenings', [RekeningController::class, 'create'])->name('rekenings.add');
Route::post('/dashboard/admin/rekenings', [RekeningController::class, 'store'])->name('rekenings.create');
Route::get('/dashboard/admin/rekenings/{id}/edit', [RekeningController::class, 'edit'])->name('rekenings.edit');
Route::put('/dashboard/admin/rekenings/{id}', [RekeningController::class, 'update'])->name('rekenings.update');
Route::delete('/dashboard/admin/rekenings/{id}', [RekeningController::class, 'destroy'])->name('rekenings.destroy');

Route::post('/pelanggan/{nomerid}/update-sid', [\App\Http\Controllers\PelangganController::class, 'updateSid'])
    ->middleware('auth:customer');

Route::get('/install', function () {
    return view('content/apps/install');
});

// Route::get('/customer/webview-auth', [WebViewController::class, 'loginWithToken']);

Route::prefix('/dashboard/admin/barangs')->group(function () {
    Route::get('/', [BarangController::class, 'index'])->name('barangs');
    Route::get('/create', [BarangController::class, 'create'])->name('add-barang');
    Route::post('/', [BarangController::class, 'store'])->name('post-barang');
    Route::get('{id}', [BarangController::class, 'show'])->name('get-barang');
    Route::put('{id}', [BarangController::class, 'update'])->name('edit-barang');
    Route::delete('{id}', [BarangController::class, 'destroy'])->name('delete-barang');
});

// Kas Registrasi
Route::middleware(['auth', 'role:administrator,admin'])->prefix('dashboard/admin/kas-registrasi')->group(function () {
    Route::get('/', [KasRegistrasiController::class, 'index'])->name('kas-registrasi.index');
    Route::post('/store', [KasRegistrasiController::class, 'store'])->name('kas-registrasi.store');
    Route::get('/{id}', [KasRegistrasiController::class, 'show'])->name('kas-registrasi.show');
    Route::put('/{id}', [KasRegistrasiController::class, 'update'])->name('kas-registrasi.update');
    Route::delete('/{id}', [KasRegistrasiController::class, 'destroy'])->name('kas-registrasi.destroy');
});


Route::middleware(['auth', 'role:directur'])->prefix('dashboard/directur')->name('directur.')->group(function () {
    Route::get('/pelanggan', [DirecturMarketingController::class, 'index'])->name('pelanggan');
    Route::get('/pelanggan/{id}/edit', [MarketingController::class, 'edit'])->name('pelanggan.edit');
    Route::put('/pelanggan/{id}', [MarketingController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{id}', [MarketingController::class, 'destroy'])->name('pelanggan.delete');
    Route::get('/progres', [DirecturMarketingController::class, 'progres'])->name('progres');
    Route::get('/progres/belum-progres', [DirecturMarketingController::class, 'progresBelum'])->name('progres.belum-progres');
    Route::get('/progres/tarik-kabel', [DirecturMarketingController::class, 'progresTarikKabel'])->name('progres.tarik-kabel');
    Route::get('/progres/aktivasi', [DirecturMarketingController::class, 'progresAktivasi'])->name('progres.aktivasi');
    Route::get('/progres/registrasi', [DirecturMarketingController::class, 'progresRegistrasi'])->name('progres.registrasi');
    Route::get('/approve', [DirecturMarketingController::class, 'approve'])->name('approve');
});



Route::prefix('/dashboard/admin/barang-masuks')->group(function () {
    Route::get('/', [BarangMasukController::class, 'index'])->name('index.barangmasuk');
    Route::get('/create', [BarangMasukController::class, 'create'])->name('create.barangmasuk');
    Route::post('/', [BarangMasukController::class, 'store'])->name('add.barangmasuk');
    Route::get('{id}', [BarangMasukController::class, 'edit'])->name('show.barangmasuk');
    Route::put('{id}', [BarangMasukController::class, 'update'])->name('edit.barangmasuk');
    Route::delete('{id}', [BarangMasukController::class, 'destroy'])->name('delete.barangmasuk');
});
Route::prefix('/dashboard/admin/barang-keluar')->group(function () {
    Route::get('/', [BarangKeluarController::class, 'index'])->name('index.barangkeluar');          // List Barang Keluar
    Route::get('/create', [BarangKeluarController::class, 'create'])->name('add.barangkeluar');     // Form tambah Barang Keluar
    Route::post('/store', [BarangKeluarController::class, 'store'])->name('store.barangkeluar');    // Simpan Barang Keluar
    Route::get('/{id}/edit', [BarangKeluarController::class, 'edit'])->name('edit.barangkeluar');   // Form edit Barang Keluar
    Route::put('/{id}', [BarangKeluarController::class, 'update'])->name('update.barangkeluar');    // Update Barang Keluar
    Route::delete('/{id}', [BarangKeluarController::class, 'destroy'])->name('delete.barangkeluar'); // Hapus Barang Keluar
});


Route::middleware(['auth', 'role:logistic'])->prefix('/dashboard/logistik/laporan-kabel')->name('logistik.laporan-kabel.')->group(function () {
    Route::get('/', [LaporanKabelController::class, 'index'])->name('index');
    Route::post('/', [LaporanKabelController::class, 'store'])->name('store');
    Route::put('/{laporanKabel}', [LaporanKabelController::class, 'update'])->name('update');
    Route::delete('/{laporanKabel}', [LaporanKabelController::class, 'destroy'])->name('destroy');
    Route::get('/export/pdf', [LaporanKabelController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/excel', [LaporanKabelController::class, 'exportExcel'])->name('export.excel');
});


// Chat Routes - Real-time messaging
Route::middleware(['auth', 'role:administrator,admin'])->group(function () {
    Route::get('/dashboard/admin/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::post('/dashboard/admin/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password.update');
});

Route::middleware('auth')->group(function () {
    // Chat Admin - untuk admin melihat daftar user
    Route::get('/dashboard/admin/chat', [ChatController::class, 'admin'])->name('chat.admin');

    // Chat User - untuk user chat dengan admin
    Route::get('/pelanggan/chat', [ChatController::class, 'pelanggan'])->name('chat.pelanggan');
});

// Chat API endpoints - support both web and customer guards
Route::middleware('auth:web,customer')->group(function () {
    Route::get('/chat/messages/{userId?}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::put('/chat/messages/{messageId}', [ChatController::class, 'updateMessage']);
    Route::delete('/chat/messages/{messageId}', [ChatController::class, 'deleteMessage']);
    Route::get('/chat/users', [ChatController::class, 'getUserList'])->name('chat.users');
    Route::post('/chat/mark-read/{userId}', [ChatController::class, 'markRead'])->name('chat.markRead');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unreadCount');
});

Route::middleware('auth:web,customer')->group(function () {

    Route::get('/kwitansi/preview/{tagihan_id}', 'App\Http\Controllers\KwitansiController@preview')
        ->name('kwitansi.preview');

    Route::get('/kwitansi/download/{tagihan_id}', 'App\Http\Controllers\KwitansiController@download')
        ->name('kwitansi.download');

});

// Verifikasi keaslian kwitansi (anti-pemalsuan) - akses publik via link QR signed
Route::get('/kwitansi/verify/{tagihan_id}', [KwitansiController::class, 'verify'])
    ->name('kwitansi.verify');



/*
Route::middleware(['auth'])->group(function () {
    Route::get('/tagihan/push-notification', [PushNotificationController::class, 'index'])
        ->name('push.notification.index');

    Route::post('/push-notification/broadcast', [PushNotificationController::class, 'broadcast'])
        ->name('tagihan.push');

    Route::post('/push-notification/broadcast-info', [PushNotificationController::class, 'broadcastInfo'])
        ->name('push.notification.broadcast.info');

    Route::get('/push-notification/all-tagihan-ids', [PushNotificationController::class, 'getAllTagihanIds'])
        ->name('push.notification.all.ids');
});
*/

Route::middleware(['auth'])->prefix('/dashboard/admin/salary')->name('gaji.')->group(function () {
    Route::get('/', [GajiController::class, 'index'])->name('index');
    Route::get('/create', [GajiController::class, 'create'])->name('create');
    Route::post('/', [GajiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [GajiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [GajiController::class, 'update'])->name('update');
    Route::delete('/{id}', [GajiController::class, 'destroy'])->name('delete');
    Route::get('/{id}/print', [GajiController::class, 'print'])->name('print');
});

Route::middleware(['auth'])->group(function () {

    // ?? Halaman absensi user (check-in / check-out)
    Route::get('dashboard/admin/absensi', [AbsensiController::class, 'index'])
        ->name('absensi');

    // ?? Submit absensi (checkin, checkout, lembur)
    Route::post('dashboard/admin/absensi/submit', [AbsensiController::class, 'submit'])
        ->name('absensi.submit');

    // ?? Admin / HR: lihat semua absensi
    Route::get('dashboard/admin/data/absensi', [AbsensiController::class, 'getAll'])
        ->name('absensi.list');

});


Route::prefix('dashboard/admin/backup')->name('backup.')->group(function () {

    // Halaman list backup
    Route::get('/', [DatabaseBackupController::class, 'index'])
        ->name('index');

    // Proses buat backup database (POST untuk AJAX)
    Route::post('/run', [DatabaseBackupController::class, 'backup'])
        ->name('create');

    // Download file backup
    Route::get('/download/{filename}', [DatabaseBackupController::class, 'download'])
        ->name('download');

    // Hapus file backup
    Route::delete('/delete/{filename}', [DatabaseBackupController::class, 'delete'])
        ->name('delete');

    // Cek status backup (AJAX)
    Route::get('/status', [DatabaseBackupController::class, 'checkStatus'])
        ->name('status');

    // DEBUG: cek fungsi yang tersedia (hapus setelah selesai debug)
    Route::get('/debug-env', function () {
        $fns = ['exec', 'shell_exec', 'proc_open', 'popen', 'system', 'passthru'];
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        $result = [];
        foreach ($fns as $fn) {
            $result[$fn] = function_exists($fn) && !in_array($fn, $disabled) ? 'OK' : 'DISABLED';
        }
        return response()->json([
            'php_binary' => PHP_BINARY,
            'php_version' => PHP_VERSION,
            'disable_functions' => ini_get('disable_functions'),
            'functions' => $result,
            'storage_writable' => is_writable(storage_path('app')),
        ]);
    })->name('backup.debug');

});
Route::middleware(['auth'])->group(function () {

    // List iklan
    Route::get('dashboard/admin/iklan', [IklanController::class, 'index'])
        ->name('iklan.index');

    // Form tambah iklan
    Route::get('dashboard/admin/iklan/create', [IklanController::class, 'create'])
        ->name('iklan.create');

    // Simpan iklan
    Route::post('dashboard/admin/iklan', [IklanController::class, 'store'])
        ->name('iklan.store');

    // Update iklan
    Route::get('dashboard/admin/iklan/{id}/edit', [IklanController::class, 'edit'])
        ->name('iklan.edit');

    // Kirim iklan (OneSignal)
    Route::post('dashboard/admin/iklan/{id}/send', [IklanController::class, 'send'])
        ->name('iklan.send');

    // Hapus iklan
    Route::delete('dashboard/admin/iklan/{id}', [IklanController::class, 'destroy'])
        ->name('iklan.destroy');
});



// Marketing Routes - Pastikan ada middleware auth jika diperlukan
Route::middleware(['auth'])->prefix('dashboard/marketing')->name('marketing.')->group(function () {
    // Index & List
    Route::get('/pelanggan', [MarketingController::class, 'index'])->name('pelanggan');
    Route::get('/status', [MarketingController::class, 'status'])->name('status');

    // Progres & Approve 
    Route::get('/progres', [MarketingController::class, 'progres'])->name('progres');
    Route::get('/progres/belum-progres', [MarketingController::class, 'progresBelum'])->name('progres.belum-progres');
    Route::get('/progres/tarik-kabel', [MarketingController::class, 'progresTarikKabel'])->name('progres.tarik-kabel');
    Route::get('/progres/aktivasi', [MarketingController::class, 'progresAktivasi'])->name('progres.aktivasi');
    Route::get('/progres/registrasi', [MarketingController::class, 'progresRegistrasi'])->name('progres.registrasi');
    Route::get('/approve', [MarketingController::class, 'approve'])->name('approve');

    // Create
    Route::get('/pelanggan/create', [MarketingController::class, 'create'])->name('add-pelanggan');

    // Store
    Route::post('/pelanggan', [MarketingController::class, 'store'])->name('pelanggan.store');

    // Edit
    Route::get('/pelanggan/{id}/edit', [MarketingController::class, 'edit'])->name('pelanggan.edit');

    // Update
    Route::put('/pelanggan/{id}', [MarketingController::class, 'update'])->name('pelanggan.update');

    // Fallback GET agar URL POST tidak melempar MethodNotAllowed saat dibuka ulang.
    Route::get('/pelanggan/{id}/progres', [MarketingController::class, 'redirectProgresUpdatePage'])->name('pelanggan.progres.page');
    // Quick update progres (tanpa harus buka halaman edit)
    Route::post('/pelanggan/{id}/progres', [MarketingController::class, 'updateProgres'])->name('pelanggan.progres');

    // Delete
    Route::delete('/pelanggan/{id}', [MarketingController::class, 'destroy'])->name('pelanggan.delete');

    // AJAX Update SID
    Route::post('/pelanggan/{nomerid}/sid', [MarketingController::class, 'updateSid'])->name('pelanggan.sid');

    // Import Excel
    Route::post('/pelanggan/import', [MarketingController::class, 'importExcel'])->name('pelanggan.import');

    // DataTable JSON (Approve)
    Route::get('/pelanggan/approve/data', [MarketingController::class, 'getDataAprove'])->name('pelanggan.approve.data');
});

Route::get('/test-redis', function () {
    try {
        Redis::set('test', 'Laravel Redis berjalan!');
        $value = Redis::get('test');
        return "Redis OK: " . $value;
    } catch (Exception $e) {
        return "Redis Error: " . $e->getMessage();
    }
});

Route::prefix('mobile/customer')->middleware(['webview.token'])
    ->group(function () {
        Route::get('/tagihan', [MobileTagihanController::class, 'index'])->name('mobile.tagihan.index');
        Route::get('/tagihan/home', [MobileTagihanController::class, 'indexHome'])->name('mobile.tagihan.home');
        Route::get('/tagihan/kwitansi', [MobileTagihanController::class, 'selesai'])->name('mobile.tagihan.selesai');
        Route::get('/tagihan/summary', [MobileTagihanController::class, 'summaryJson']);
        Route::get('/tagihan/{id}', [MobileTagihanController::class, 'show'])->name('mobile.tagihan.show');
        Route::get('/kwitansi', [WebviewKwitansiController::class, 'index'])->name('webview.kwitansi.index');
        Route::get('/kwitansi/{id}/preview', [WebviewKwitansiController::class, 'preview'])->name('webview.kwitansi.preview');
        Route::get('/kwitansi/{id}/download', [WebviewKwitansiController::class, 'download'])->name('webview.kwitansi.download');
        Route::get('/chat', [ChatController::class, 'user']);
        Route::get('/pelanggan/chat', [ChatController::class, 'pelanggan']);
        Route::get('/chat/messages/{userId?}', [ChatController::class, 'getMessages']);
        Route::post('/chat/send', [ChatController::class, 'send']);
        Route::get('/chat/users', [ChatController::class, 'getUserList']);
        Route::post('/chat/mark-read/{userId}', [ChatController::class, 'markRead']);
        Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);

    });

Route::get('/pelanggan/export', [PelangganController::class, 'exportExcel']);

Route::get('/dashboard/admin/pelanggan/search', [TagihanController::class, 'searchPelanggan'])
    ->name('pelanggan.search');



Route::get('tagihan/export', [TagihanController::class, 'export'])->name('tagihan.bayar.export');

// Public Share Gaji
Route::get('/gaji/share/{id}', [GajiController::class, 'print'])->name('gaji.share.public');
