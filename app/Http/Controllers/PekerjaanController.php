<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pekerjaan = Pekerjaan::with('penduduk')->get();
        return view('pekerjaan.index', compact('pekerjaan'));
    }

    public function create()
    {
        $penduduk = Penduduk::all();
        return view('pekerjaan.create', compact('penduduk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penduduk' => 'required',
            'Pekerjaan' => 'required',
            'Penghasilan' => 'required'
        ]);

        try {
            Pekerjaan::create($request->all());
            return redirect()->route('pekerjaan.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pekerjaan = Pekerjaan::find($id);
        $penduduk = Penduduk::all();
        return view('pekerjaan.edit', compact('pekerjaan', 'penduduk'));
    }

    public function update(Request $request, $id)
    {
        Pekerjaan::find($id)->update([
            'Pekerjaan' => $request->Pekerjaan,
            'Penghasilan' => $request->Penghasilan
        ]);

        return redirect()->route('pekerjaan.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Pekerjaan::destroy($id);
        return redirect()->route('pekerjaan.index')->with('success', 'Data berhasil dihapus');
    }
}
