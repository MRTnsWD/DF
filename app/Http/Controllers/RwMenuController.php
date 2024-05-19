<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RwMenuController extends Controller
{
    public function index()
    {
        $pendidikan = User::where('role', 'rw')->get();
        return view('rwmenu.index', compact('pendidikan'));
    }

    public function create()
    {
        return view('rwmenu.create');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Pastikan tabel users disebutkan secara spesifik
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            // Membuat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'rw',
                'password' => Hash::make($request->password),
            ]);

            // Memicu event Registered
            event(new Registered($user));

            // Redirect jika berhasil dengan pesan sukses
            return redirect()->route('rwmenu.index')->with('success', 'User successfully registered.');
        } catch (\Exception $e) {
            // Kembali ke halaman sebelumnya dengan input dan pesan error
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $pendidikan = User::find($id);
        return view('rwmenu.edit', compact('pendidikan'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id], // Pastikan tabel users disebutkan secara spesifik
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ]);

            // Mengambil data user
            $user = User::find($id);

            // Mengupdate data user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            // Redirect jika berhasil dengan pesan sukses
            return redirect()->route('rwmenu.index')->with('success', 'User successfully updated.');
        } catch (\Exception $e) {
            // Kembali ke halaman sebelumnya dengan input dan pesan error
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            // Menghapus data user
            User::destroy($id);

            // Redirect jika berhasil dengan pesan sukses
            return redirect()->route('rwmenu.index')->with('success', 'User successfully deleted.');
        } catch (\Exception $e) {
            // Redirect jika gagal dengan pesan error
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
