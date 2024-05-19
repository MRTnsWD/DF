<?php

namespace App\Http\Controllers;

use App\Models\KondisiRumah;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Phpml\Classification\NaiveBayes;

class RwController extends Controller
{
    public function indexpenduduk()
    {
        $penduduk = Penduduk::all();
        return view('rw.penduduk.index', compact('penduduk'));
    }

    public function creatependuduk()
    {
        return view('rw.penduduk.create');
    }

    public function storependuduk(Request $request)
    {
        $validatedData = $request->validate([
            'No_KK' => 'required',
            'NIK' => 'required',
            'Nama_lengkap' => 'required',
            'Hbg_kel' => 'required',
            'JK' => 'required',
            'tmpt_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            'Agama' => 'required',
            'Pendidikan_terakhir' => 'required',
            'Jenis_bantuan' => 'required',
            'Penerima_bantuan' => 'required',
            'Jenis_bantuan_lain' => 'required',
        ]);

        try {
            Penduduk::create($validatedData);
            return redirect()->route('rw.penduduk.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }

    public function indexpekerjaan()
    {
        $pekerjaan = Pekerjaan::with('penduduk')->get();
        return view('rw.pekerjaan.index', compact('pekerjaan'));
    }

    public function createpekerjaan()
    {
        $penduduk = Penduduk::all();
        return view('rw.pekerjaan.create', compact('penduduk'));
    }

    public function storepekerjaan(Request $request)
    {
        $request->validate([
            'id_penduduk' => 'required',
            'Pekerjaan' => 'required',
            'Penghasilan' => 'required'
        ]);

        try {
            Pekerjaan::create($request->all());
            return redirect()->route('rw.pekerjaan.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }

    public function indexpendidikan()
    {
        $pendidikan = Pendidikan::with('penduduk')->get();
        return view('rw.pendidikan.index', compact('pendidikan'));
    }

    public function creatependidikan()
    {
        $penduduk = Penduduk::all();
        return view('rw.pendidikan.create', compact('penduduk'));
    }

    public function storependidikan(Request $request)
    {
        $request->validate([
            'id_penduduk' => 'required',
            'Nama' => 'required',
            'Pendidikan_terakhir' => 'required',
        ]);

        try {
            Pendidikan::create($request->all());
            return redirect()->route('rw.pendidikan.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }

    public function indexkondisi()
    {
        $kondisi = KondisiRumah::with('penduduk')->get();
        return view('rw.kondisi.index', compact('kondisi'));
    }

    public function createkondisi()
    {
        $penduduk = Penduduk::all();
        return view('rw.kondisi.create', compact('penduduk'));
    }

    public function storekondisi(Request $request)
    {
        $request->validate([
            'id_penduduk' => 'required',
            'Luas_lantai' => 'required|numeric',
            'Jenis_lantai' => 'required|string|max:255',
            'Jenis_dinding' => 'required|string|max:255',
            'Fasilitas_BAB' => 'required|string|max:255',
            'Penerangan' => 'required|string|max:255',
            'Air_minum' => 'required|string|max:255',
            'BB_masak' => 'required|string|max:255',
            'foto_rumah' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Mengelola unggahan file
        if ($request->hasFile('foto_rumah')) {
            $path = $request->file('foto_rumah')->store('public/foto_rumah');
        }

        // Membuat entri baru
        $penduduk = new KondisiRumah([
            'id_penduduk' => $request->id_penduduk,
            'Luas_lantai' => $request->Luas_lantai,
            'Jenis_lantai' => $request->Jenis_lantai,
            'Jenis_dinding' => $request->Jenis_dinding,
            'Fasilitas_BAB' => $request->Fasilitas_BAB,
            'Penerangan' => $request->Penerangan,
            'Air_minum' => $request->Air_minum,
            'BB_masak' => $request->BB_masak,
            'foto_rumah' => $path ?? null,
        ]);

        $penduduk->save(); // Menyimpan data ke basis data

        return redirect()->route('rw.kondisi.index')->with('success', 'Kondisi berhasil ditambahkan.');
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

        return view('rw.klasifikasi.index', ['predictions' => $predictions]);
    }
}
