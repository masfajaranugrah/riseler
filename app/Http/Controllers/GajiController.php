<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji;
use App\Models\Employee;
use Illuminate\Support\Str;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $salaries = Gaji::with('employee')
            ->when($search, function($query) use ($search) {
                $query->whereHas('employee', function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%");
                })->orWhere('total', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(40);
            
        return view('content.apps.Gaji.gaji-list', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('content.apps.Gaji.add-gaji', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'gaji_pokok' => 'nullable|numeric',
            'tunj_jabatan' => 'nullable|numeric',
            'tunj_fungsional' => 'nullable|numeric',
            'transport' => 'nullable|numeric',
            'makan' => 'nullable|numeric',
            'tunj_kehadiran' => 'nullable|numeric',
            'lembur' => 'nullable|numeric',
            'pot_sosial' => 'nullable|numeric',
            'pot_denda' => 'nullable|numeric',
            'pot_koperasi' => 'nullable|numeric',
            'pot_pajak' => 'nullable|numeric',
            'pot_lain' => 'nullable|numeric',
            'tunj_dynamic' => 'nullable|array',
            'tunj_keterangan' => 'nullable|array',
        ]);

        // Hitung total tunjangan dinamis
        $totalTunjanganDynamic = 0;
        if (!empty($validated['tunj_dynamic'])) {
            foreach ($validated['tunj_dynamic'] as $value) {
                $totalTunjanganDynamic += (float) $value;
            }
        }

        // Hitung total gaji
        $totalGaji =
            ($validated['gaji_pokok'] ?? 0) +
            ($validated['tunj_jabatan'] ?? 0) +
            ($validated['tunj_fungsional'] ?? 0) +
            ($validated['transport'] ?? 0) +
            ($validated['makan'] ?? 0) +
            ($validated['tunj_kehadiran'] ?? 0) +
            ($validated['lembur'] ?? 0) +
            $totalTunjanganDynamic -
            ($validated['pot_sosial'] ?? 0) -
            ($validated['pot_denda'] ?? 0) -
            ($validated['pot_koperasi'] ?? 0) -
            ($validated['pot_pajak'] ?? 0) -
            ($validated['pot_lain'] ?? 0);

        // Simpan data
        $gaji = Gaji::create([
            'id' => Str::uuid(),
            'employee_id' => $validated['employee_id'],
            'gaji_pokok' => $validated['gaji_pokok'] ?? 0,
            'tunj_jabatan' => $validated['tunj_jabatan'] ?? 0,
            'tunj_fungsional' => $validated['tunj_fungsional'] ?? 0,
            'transport' => $validated['transport'] ?? 0,
            'makan' => $validated['makan'] ?? 0,
            'tunj_kehadiran' => $validated['tunj_kehadiran'] ?? 0,
            'lembur' => $validated['lembur'] ?? 0,
            'pot_sosial' => $validated['pot_sosial'] ?? 0,
            'pot_denda' => $validated['pot_denda'] ?? 0,
            'pot_koperasi' => $validated['pot_koperasi'] ?? 0,
            'pot_pajak' => $validated['pot_pajak'] ?? 0,
            'pot_lain' => $validated['pot_lain'] ?? 0,
            'total' => $totalGaji,
            'grand_total' => $totalGaji, // bisa ditambahkan logika lain jika perlu
            'tunj_dynamic' => json_encode($validated['tunj_dynamic'] ?? []),
            'tunj_keterangan' => json_encode($validated['tunj_keterangan'] ?? [])
        ]);

        return redirect()->route('gaji.index')->with('success', 'Gaji berhasil ditambahkan.');
    }

public function edit($id)
{
    $gaji = Gaji::findOrFail($id);
    $employees = Employee::all();

    // Pastikan tunjangan lama berupa array
    $gaji->tunj_dynamic = is_array($gaji->tunj_dynamic) ? $gaji->tunj_dynamic : json_decode($gaji->tunj_dynamic, true) ?? [];
    $gaji->tunj_keterangan = is_array($gaji->tunj_keterangan) ? $gaji->tunj_keterangan : json_decode($gaji->tunj_keterangan, true) ?? [];

    return view('content.apps.Gaji.edit-gaji', compact('gaji', 'employees'));
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'gaji_pokok' => 'nullable|numeric',
            'tunj_jabatan' => 'nullable|numeric',
            'tunj_fungsional' => 'nullable|numeric',
            'transport' => 'nullable|numeric',
            'makan' => 'nullable|numeric',
            'tunj_kehadiran' => 'nullable|numeric',
            'lembur' => 'nullable|numeric',
            'pot_sosial' => 'nullable|numeric',
            'pot_denda' => 'nullable|numeric',
            'pot_koperasi' => 'nullable|numeric',
            'pot_pajak' => 'nullable|numeric',
            'pot_lain' => 'nullable|numeric',
            'tunj_dynamic' => 'nullable|array',
            'tunj_keterangan' => 'nullable|array',
        ]);

        // Hitung total tunjangan dinamis
        $totalTunjanganDynamic = 0;
        if (!empty($validated['tunj_dynamic'])) {
            foreach ($validated['tunj_dynamic'] as $value) {
                $totalTunjanganDynamic += (float) $value;
            }
        }

        // Hitung total gaji
        $totalGaji =
            ($validated['gaji_pokok'] ?? 0) +
            ($validated['tunj_jabatan'] ?? 0) +
            ($validated['tunj_fungsional'] ?? 0) +
            ($validated['transport'] ?? 0) +
            ($validated['makan'] ?? 0) +
            ($validated['tunj_kehadiran'] ?? 0) +
            ($validated['lembur'] ?? 0) +
            $totalTunjanganDynamic -
            ($validated['pot_sosial'] ?? 0) -
            ($validated['pot_denda'] ?? 0) -
            ($validated['pot_koperasi'] ?? 0) -
            ($validated['pot_pajak'] ?? 0) -
            ($validated['pot_lain'] ?? 0);

        $gaji = Gaji::findOrFail($id);
        $gaji->update([
            'employee_id' => $validated['employee_id'],
            'gaji_pokok' => $validated['gaji_pokok'] ?? 0,
            'tunj_jabatan' => $validated['tunj_jabatan'] ?? 0,
            'tunj_fungsional' => $validated['tunj_fungsional'] ?? 0,
            'transport' => $validated['transport'] ?? 0,
            'makan' => $validated['makan'] ?? 0,
            'tunj_kehadiran' => $validated['tunj_kehadiran'] ?? 0,
            'lembur' => $validated['lembur'] ?? 0,
            'pot_sosial' => $validated['pot_sosial'] ?? 0,
            'pot_denda' => $validated['pot_denda'] ?? 0,
            'pot_koperasi' => $validated['pot_koperasi'] ?? 0,
            'pot_pajak' => $validated['pot_pajak'] ?? 0,
            'pot_lain' => $validated['pot_lain'] ?? 0,
            'total' => $totalGaji,
            'grand_total' => $totalGaji,
            'tunj_dynamic' => json_encode($validated['tunj_dynamic'] ?? []),
            'tunj_keterangan' => json_encode($validated['tunj_keterangan'] ?? [])
        ]);

        return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Gaji::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function print($id)
    {
        $gaji = Gaji::with('employee')->findOrFail($id);

        // Pastikan format data array aman
        $gaji->tunj_dynamic = is_array($gaji->tunj_dynamic) ? $gaji->tunj_dynamic : json_decode($gaji->tunj_dynamic, true) ?? [];
        $gaji->tunj_keterangan = is_array($gaji->tunj_keterangan) ? $gaji->tunj_keterangan : json_decode($gaji->tunj_keterangan, true) ?? [];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('content.apps.Gaji.gaji-print', compact('gaji'));
        return $pdf->stream('Slip_Gaji_' . $gaji->employee->full_name . '_' . date('Ymd') . '.pdf');
    }
}
