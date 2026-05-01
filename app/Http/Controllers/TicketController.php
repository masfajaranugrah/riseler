<?php

namespace App\Http\Controllers;

use App\Events\TicketCreated;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Ticket;
use App\Models\TicketStatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function ticketsJson()
    {
        $tickets = Ticket::with(['pelanggan', 'user', 'creator'])->latest()->get();

        $pelanggan = Pelanggan::all();
        $paket = Paket::all();
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();

        $totalCustomer = $pelanggan->count();
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count();
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count();
        $totalPaket = $paket->count();

        return response()->json([
            'tickets' => $tickets,
            'totalCustomer' => $totalCustomer,
            'lunas' => $lunas,
            'belumLunas' => $belumLunas,
            'totalPaket' => $totalPaket,
        ]);
    }

public function index(Request $request)
{
    // Ambil pelanggan ringan jika masih dibutuhkan untuk dropdown
    $pelanggan = Pelanggan::select('id', 'nama_lengkap', 'no_whatsapp', 'no_telp')->get();
    $paket = Paket::select('id', 'nama_paket')->get();

    $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
    $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
    
    // Statistik Optimal
    $totalCustomer = Pelanggan::count();
    $lunas = Tagihan::where('status_pembayaran', 'lunas')->count();
    $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count();
    $totalPaket = Paket::count();

    // Custom Backend Search
    $search = $request->input('search');

    // ? FILTER STATUS AKTIF (Pending, Assigned, Progress) & Lakukan Pagination
    $query = Ticket::with(['user', 'creator', 'pelanggan'])
        ->whereIn('status', ['pending', 'assigned', 'progress']);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->whereHas('pelanggan', function($pq) use ($search) {
                $pq->where('nama_lengkap', 'like', "%{$search}%");
            })
            ->orWhere('title', 'like', "%{$search}%")
            ->orWhere('issue_description', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
            ->orWhere('priority', 'like', "%{$search}%");
        });
    }

    $tickets = $query->latest()->paginate(40)->withQueryString();

    return view('content.apps.Ticket.ticket', compact(
        'tickets',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}

    public function create()
    {
        // Ambil semua pelanggan lengkap dengan field alamat untuk dropdown autofill
        $pelanggan = Pelanggan::with('paket')
            ->select('id', 'nomer_id', 'nama_lengkap', 'no_whatsapp', 'no_telp',
                     'alamat_jalan', 'rt', 'rw', 'desa', 'kecamatan', 'paket_id')
            ->get();
        $paket = Paket::select('id', 'nama_paket')->get();

        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        
        // Statistik Optimal
        $totalCustomer = Pelanggan::count(); // jumlah pelanggan
        $lunas = Tagihan::where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = Paket::count(); // jumlah paket

        // Ambil semua user dengan role 'team' untuk dijadikan pilihan penugas
        $users = User::where('role', 'team')->get();

        return view('content.apps.Ticket.add-ticket', compact(
            'users',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

     public function store(Request $request)
{
    $ticketType = $request->input('ticket_type', 'customer');

    if ($ticketType === 'internal') {
        // ─── Tiket Internal (tanpa pelanggan) ───
        $request->validate([
            'title'             => 'required|string|max:200',
            'category'          => 'required|string|max:50',
            'issue_description' => 'required|string',
            'priority'          => 'required|in:urgent,medium,low',
            'user_id'           => 'nullable|exists:users,id',
            'location_link'     => 'nullable|url',
            'additional_note'   => 'nullable|string',
        ]);

        $ticket = Ticket::create([
            'ticket_type'       => 'internal',
            'title'             => $request->title,
            'pelanggan_id'      => null,
            'phone'             => null,
            'location_link'     => $request->location_link,
            'category'          => $request->category,
            'issue_description' => $request->issue_description,
            'additional_note'   => $request->additional_note,
            'priority'          => $request->priority,
            'status'            => $request->user_id ? 'assigned' : 'pending',
            'user_id'           => $request->user_id ?: null,
            'created_by'        => Auth::id(),
        ]);

    } else {
        // ─── Tiket Pelanggan ───
        $request->validate([
            'pelanggan_id'      => 'required|exists:pelanggans,id',
            'location_link'     => 'nullable|url',
            'category'          => 'nullable|string|max:50',
            'issue_description' => 'required|string',
            'additional_note'   => 'nullable|string',
            'cs_note'           => 'nullable|string',
            'cs_attachment'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'priority'          => 'required|in:urgent,medium,low',
            'user_id'           => 'nullable|exists:users,id',
        ]);

        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

        $csAttachment = null;
        if ($request->hasFile('cs_attachment')) {
            $csAttachment = $request->file('cs_attachment')->store('tickets/cs', 'public');
        }

        $ticket = Ticket::create([
            'ticket_type'       => 'customer',
            'title'             => null,
            'pelanggan_id'      => $pelanggan->id,
            'phone'             => $pelanggan->no_whatsapp ?? $pelanggan->no_telp,
            'location_link'     => $request->location_link,
            'category'          => $request->category,
            'issue_description' => $request->issue_description,
            'additional_note'   => $request->additional_note,
            'cs_note'           => $request->cs_note,
            'attachment'        => $csAttachment,
            'complaint_source'  => $request->complaint_source ?? 'whatsapp',
            'priority'          => $request->priority,
            'status'            => $request->user_id ? 'assigned' : 'pending',
            'user_id'           => $request->user_id ?: null,
            'created_by'        => Auth::id(),
        ]);

        event(new TicketCreated($ticket));
    }

    // Log status awal
    TicketStatusLog::create([
        'ticket_id' => $ticket->id,
        'status'    => $ticket->status,
        'user_id'   => Auth::id(),
    ]);

    return redirect()->route('tickets.indexs')
        ->with('success', 'Ticket berhasil dibuat.');
}

    public function edit(Ticket $ticket)
    {
        // Ambil pelanggan dan paket seperlunya
        $pelanggan = Pelanggan::select('id', 'nama_lengkap', 'no_whatsapp', 'no_telp')->get();
        $paket = Paket::select('id', 'nama_paket')->get();

        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        
        // Statistik Optimal
        $totalCustomer = Pelanggan::count(); // jumlah pelanggan
        $lunas = Tagihan::where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = Paket::count(); // jumlah paket

        // Ambil semua user role 'team' untuk dropdown
        $users = User::where('role', 'team')->get();

        return view('content.apps.Ticket.edit-ticket', compact(
            'ticket',
            'users',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }



public function finished(Request $request)
{
    $pelanggan = Pelanggan::select('id', 'nama_lengkap', 'no_whatsapp', 'no_telp')->get();
    $paket = Paket::select('id', 'nama_paket')->get();

    $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
    $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
    
    // Statistik Optimal
    $totalCustomer = Pelanggan::count();
    $lunas = Tagihan::where('status_pembayaran', 'lunas')->count();
    $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count();
    $totalPaket = Paket::count();

    $search = $request->input('search');

    // ? TICKET YANG STATUS 'finished' SAJA
    $query = Ticket::with(['user', 'creator', 'pelanggan'])
        ->where('status', 'finished');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->whereHas('pelanggan', function($pq) use ($search) {
                $pq->where('nama_lengkap', 'like', "%{$search}%");
            })
            ->orWhere('title', 'like', "%{$search}%")
            ->orWhere('issue_description', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
            ->orWhere('priority', 'like', "%{$search}%");
        });
    }

    $tickets = $query->latest()->paginate(40)->withQueryString();

    return view('content.apps.Ticket.ticket-finished', compact(
        'tickets',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}







public function approved()
{
    $pelanggan = Pelanggan::select('id', 'nama_lengkap', 'no_whatsapp', 'no_telp')->get();
    $paket = Paket::select('id', 'nama_paket')->get();

    $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
    $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
    
    // Statistik Optimal
    $totalCustomer = Pelanggan::count();
    $lunas = Tagihan::where('status_pembayaran', 'lunas')->count();
    $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count();
    $totalPaket = Paket::count();

    // ? TICKET YANG STATUS 'approved' SAJA
    $tickets = Ticket::with(['user', 'creator', 'pelanggan'])
        ->where('status', 'approved')
        ->latest()
        ->get();

    return view('content.apps.Ticket.ticket-approved', compact(
        'tickets',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}







    public function update(Request $request, Ticket $ticket)
    {
        // Validasi
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'phone' => 'nullable|string|max:20',
            'location_link' => 'nullable|url',
            'category' => 'nullable|string|max:50',
            'issue_description' => 'required|string',
            'additional_note' => 'nullable|string',
            'cs_note' => 'nullable|string',
            'cs_attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'priority' => 'required|in:urgent,medium,low',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,assigned,progress,finished,approved,rejected',
        ]);

        // Hapus & upload cs_attachment baru jika ada
        if ($request->hasFile('cs_attachment')) {
            if ($ticket->cs_attachment) {
                \Storage::disk('public')->delete($ticket->cs_attachment);
            }
            $ticket->cs_attachment = $request->file('cs_attachment')->store('tickets/cs', 'public');
        }
        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

        // Simpan update ticket
        $ticket->update([
            'pelanggan_id' => $request->pelanggan_id,
            'customer_name' => optional($request->pelanggan_id ? \App\Models\Pelanggan::find($request->pelanggan_id) : null)->nama_lengkap ?? $request->customer_name,
            'phone' => $request->phone,
            'location_link' => $request->location_link,
            'category' => $request->category,
            'issue_description' => $request->issue_description,
            'additional_note' => $request->additional_note,
            'cs_note' => $request->cs_note,
            'cs_attachment' => $ticket->cs_attachment ?? $ticket->cs_attachment,
            'priority' => $request->priority,
            'user_id' => $request->user_id,
            'status' => $request->status,
        ]);

        // Tambahkan log status baru
        TicketStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => $request->status,
            'user_id' => Auth::id(), // siapa yang update
        ]);

        return redirect()->route('tickets.indexs')->with('success', 'Ticket berhasil diperbarui dan status tercatat.');
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->cs_attachment) {
            \Storage::disk('public')->delete($ticket->cs_attachment);
        }
        $ticket->delete();

        return redirect()->route('tickets.indexs')->with('success', 'Ticket berhasil dihapus');
    }
}
