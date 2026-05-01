<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\EmployeeAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    // public function getDataJson()
    // {
    //     $employees = Employee::latest()->get();

    //     return response()->json([
    //         'draw' => request('draw') ?? 0,               // DataTables draw counter
    //         'recordsTotal' => $employees->count(),       // Total data sebelum filter
    //         'recordsFiltered' => $employees->count(),    // Total data setelah filter (saat ini sama)
    //         'data' => $employees,                          // Array data karyawan
    //     ]);
    // }

    // Menampilkan semua data
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        return view('content.apps.Karyawan.karyawan-list', compact('employees'));
    }

    // Form tambah
    public function create()
    {
        return view('content.apps.Karyawan.add-karyawan');
    }

    public function upload()
    {
        return view('content.apps.Karyawan.upload');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'full_address' => 'nullable|string',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'no_hp' => 'nullable|string|max:50',
            'tanggal_masuk' => 'nullable|date',
            'jabatan' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
            'foto_ktp' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));
            $path = 'employees/foto/' . uniqid() . '.dat';
            Storage::put($path, $encryptedContent);
            $data['foto'] = $path;
        }

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));
            $path = 'employees/ktp/' . uniqid() . '.dat';
            Storage::put($path, $encryptedContent);
            $data['foto_ktp'] = $path;
        }

        Employee::create($data);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    // Form edit
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        return view('content.apps.Karyawan.karyawan-edit', compact('employee'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
            'foto_ktp' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nik' => $request->nik, // Fixed typo from 'nip' to 'nik' if necessary, or kept as requested
            'full_name' => $request->full_name,
            'full_address' => $request->full_address,
            'place_of_birth' => $request->place_of_birth,
            'date_of_birth' => $request->date_of_birth,
            'no_hp' => $request->no_hp,
            'tanggal_masuk' => $request->tanggal_masuk,
            'jabatan' => $request->jabatan,
            'bank' => $request->bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
        ];

        if ($request->hasFile('foto')) {
            // Delete old file
            if ($employee->foto && Storage::exists($employee->foto)) {
                Storage::delete($employee->foto);
            }
            $file = $request->file('foto');
            $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));
            $path = 'employees/foto/' . uniqid() . '.dat';
            Storage::put($path, $encryptedContent);
            $data['foto'] = $path;
        }

        if ($request->hasFile('foto_ktp')) {
            // Delete old file
            if ($employee->foto_ktp && Storage::exists($employee->foto_ktp)) {
                Storage::delete($employee->foto_ktp);
            }
            $file = $request->file('foto_ktp');
            $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));
            $path = 'employees/ktp/' . uniqid() . '.dat';
            Storage::put($path, $encryptedContent);
            $data['foto_ktp'] = $path;
        }

        $employee->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    // Hapus data
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new EmployeeImport, $request->file('file'));

        return redirect()->route('karyawan.index')->with('success', '? Data Excel berhasil diimport!');
    }

    public function showImage(Request $request, $id, $type)
    {
        $employee = Employee::findOrFail($id);

        // Audit Log: Catat siapa yang melihat
        EmployeeAccessLog::create([
            'user_id' => Auth::id(),
            'employee_id' => $id,
            'type' => $type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        $path = $type == 'foto' ? $employee->foto : $employee->foto_ktp;

        if (!$path || !Storage::exists($path)) {
            abort(404);
        }

        $encryptedContent = Storage::get($path);
        try {
            // Try decryptString first, then fallback to decrypt for legacy
            if (strpos($encryptedContent, 'eyJpdiI6') === 0) { // Check if it looks like a Laravel encryption payload
                try {
                    $decryptedContent = Crypt::decryptString($encryptedContent);
                } catch (\Exception $e) {
                    $decryptedContent = Crypt::decrypt($encryptedContent);
                }
            } else {
                $decryptedContent = Crypt::decrypt($encryptedContent);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mendeskripsi gambar karyawan: ' . $e->getMessage(), [
                'id' => $id,
                'type' => $type,
                'path' => $path
            ]);
            abort(500, 'Gagal mendeskripsi gambar.');
        }

        // Detect mime type from content
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($decryptedContent);
        
        if (ob_get_level()) ob_end_clean();
        
        return response()->make($decryptedContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
