<?php

namespace App\Http\Controllers;

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
        $data = Pekerjaan::with('penduduk')->get();
        $samples = [];
        $labels = [];
        $dataToSend = [];

        foreach ($data as $item) {
            $samples[] = [$item->Penghasilan];
            $labels[] = $item->Penghasilan < 5000000 ? 'layak' : 'tidak layak';
            $dataToSend[] = ['id_penduduk' => $item->id_penduduk, 'penghasilan' => $item->Penghasilan, 'nama' => $item->penduduk->Nama_lengkap, 'nik' => $item->penduduk->NIK, 'id' => $item->id];
        }

        $classifier = new NaiveBayes();
        $classifier->train($samples, $labels);

        $predictions = [];
        foreach ($dataToSend as $index => $info) {
            $result = $classifier->predict([$info['penghasilan']]);
            $predictions[] = [
                'id_penduduk' => $info['id_penduduk'],
                'nama' => $info['nama'],
                'nik' => $info['nik'],
                'id' => $info['id'],
                'klasifikasi' => $result
            ];
        }

        return view('klasifikasi.index', ['predictions' => $predictions]);
    }
}
