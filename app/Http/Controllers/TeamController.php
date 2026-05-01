<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('content.apps.Team.team', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    // Ambil semua data karyawan
    $employees = Employee::orderBy('full_name', 'asc')->get();

    return view('content.apps.Team.add-team', compact('employees'));
}


    /**
     * Store a newly created user.
     */
public function register(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:administrator,admin,logistic,marketing,customer_service,team,teknisi,koordinator,customer,karyawan,directur,verifikasi',
    ]);

    // Ambil data karyawan
    $employee = Employee::findOrFail($request->employee_id);

    User::create([
        'id' => Str::uuid(),
        'name' => $employee->full_name, // otomatis dari karyawan
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
}


    /**
     * Show the form for editing the specified user.
     */
public function edit(string $id)
{
    $user = User::findOrFail($id);
    $employees = Employee::orderBy('full_name', 'asc')->get();

    return view('content.apps.Team.edit-team', compact('user', 'employees'));
}


    /**
     * Update the specified user in storage.
     */
public function update(Request $request, string $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8|confirmed',
        'role' => 'required|in:administrator,logistic,admin,marketing,customer_service,team,teknisi,koordinator,customer,karyawan,directur,verifikasi',
    ]);

    $employee = Employee::findOrFail($request->employee_id);

    $data = [
        'name'  => $employee->full_name,
        'email' => $request->email,
        'role'  => $request->role,
    ];

    if (!empty($request->password)) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
}


    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
