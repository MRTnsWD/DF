<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Klasifikasi;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Phpml\Classification\NaiveBayes;

class KlasifikasiController extends Controller
{
    public function index()
    {
        $klasifikasi = Klasifikasi::with('penduduk')->get();
        return view('klasifikasi.index', compact('klasifikasi'));
    }

    public function create()
    {
        return view('klasifikasi.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_penduduk' => 'required',
            'Hasil_klasifikasi' => 'required',
        ]);

        try {
            Klasifikasi::create($validatedData);
            return redirect()->route('klasifikasi.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $klasifikasi = Klasifikasi::find($id);
        return view('klasifikasi.edit', compact('klasifikasi'));
    }

    public function update(Request $request, $id)
    {
        Klasifikasi::find($id)->update([
            'id_penduduk' => $request->id_penduduk,
            'Hasil_klasifikasi' => $request->Hasil_klasifikasi,
        ]);

        return redirect()->route('klasifikasi.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Klasifikasi::destroy($id);
        return redirect()->route('klasifikasi.index')->with('success', 'Data berhasil dihapus');
    }

    public function predict()
    {
        $hasils = Hasil::with('penduduk')->get();
        $klasifikasis = Klasifikasi::with('penduduk')->get();



        return view('klasifikasi.index', compact('hasils', 'klasifikasis'));
    }



    private function calculateEntropy($countTidak, $countYa, $total)
    {
        $entropy = 0;
        if ($total > 0) {
            $pTidak = $countTidak / $total;
            $pYa = $countYa / $total;

            $entropyPartTidak = $pTidak > 0 ? - ($pTidak * log($pTidak, 2)) : 0;
            $entropyPartYa = $pYa > 0 ? - ($pYa * log($pYa, 2)) : 0;

            $entropy = $entropyPartTidak + $entropyPartYa;
        }

        return $entropy;
    }
}
