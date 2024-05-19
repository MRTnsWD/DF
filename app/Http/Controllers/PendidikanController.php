<?php

namespace App\Http\Controllers;

use App\Models\Pendidikan;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function index()
    {
        $pendidikan = Pendidikan::with('penduduk')->get();
        return view('pendidikan.index', compact('pendidikan'));
    }

    public function create()
    {
        $penduduk = Penduduk::all();
        return view('pendidikan.create', compact('penduduk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penduduk' => 'required',
            'Nama' => 'required',
            'Pendidikan_terakhir' => 'required',
        ]);

        try {
            Pendidikan::create($request->all());
            return redirect()->route('pendidikan.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pendidikan = Pendidikan::find($id);
        $penduduk = Penduduk::all();
        return view('pendidikan.edit', compact('pendidikan', 'penduduk'));
    }

    public function update(Request $request, $id)
    {
        Pendidikan::find($id)->update([
            'Pendidikan_terakhir' => $request->Pendidikan_terakhir,
        ]);

        return redirect()->route('pendidikan.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Pendidikan::destroy($id);
        return redirect()->route('pendidikan.index')->with('success', 'Data berhasil dihapus');
    }
}
