<?php

namespace App\Http\Controllers;

use App\Models\Klasifikasi;
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
            'pas_foto' =>
            'required|image|mimes:jpeg,png,jpg|max:2048',
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

        if ($request->hasFile('pas_foto')) {
            $file = $request->file('pas_foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/pas_foto', $fileName);
        }

        try {
            $penduduk = new Penduduk([
                'No_KK' => $request->No_KK,
                'NIK' => $request->NIK,
                'pas_foto' => $fileName ?? null,
                'Nama_lengkap' => $request->Nama_lengkap,
                'Hbg_kel' => $request->Hbg_kel,
                'JK' => $request->JK,
                'tmpt_lahir' => $request->tmpt_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'Agama' => $request->Agama,
                'Pendidikan_terakhir' => $request->Pendidikan_terakhir,
                'Jenis_bantuan' => $request->Jenis_bantuan,
                'Penerima_bantuan' => $request->Penerima_bantuan,
                'Jenis_bantuan_lain' => $request->Jenis_bantuan_lain
            ]);

            $penduduk->save();

            $pend = '';
            if ($request->Pendidikan_terakhir == 'SD' || $request->Pendidikan_terakhir == 'SMP' || $request->Pendidikan_terakhir == 'SMA') {
                $pend = 'Layak';
            } else {
                $pend = 'Tidak Layak';
            }

            // inserrt id penduduk ke tabel klasifikasi
            $klasifikasi = new Klasifikasi();
            $klasifikasi->id_penduduk = $penduduk->id;
            $klasifikasi->pendidikan = $pend;
            $klasifikasi->save();

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
            $hit = '';
            if ($request->Penghasilan <= 3000000) {
                $hit = 'Layak';
            } else {
                $hit = 'Tidak Layak';
            }

            $klasifikasi = Klasifikasi::where('id_penduduk', $request->id_penduduk)->first();
            $klasifikasi->update([
                'pekerjaan' => $hit,
            ]);
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
            'kesimpulan' => 'required|string|max:255'

        ]);

        // Mengelola unggahan file
        if ($request->hasFile('foto_rumah')) {
            $file = $request->file('foto_rumah');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_rumah', $fileName);
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
            'foto_rumah' => $fileName ?? null,
            'kesimpulan' => $request->kesimpulan,

        ]);

        $penduduk->save(); // Menyimpan data ke basis data

        $klasifikasi = Klasifikasi::where('id_penduduk', $request->id_penduduk)->first();
        $pek = $klasifikasi->pekerjaan;
        $countBaik = 0;
        $countBaik += ($pek === 'Layak' ? 1 : 0);
        $countBaik += ($request->kesimpulan === 'Layak' ? 1 : 0);
        $kecocokan = $countBaik >= 2 ? 'Ya' : 'Tidak';
        $klasifikasi->update([
            'kondisi' => $request->kesimpulan,
            'kecocokan' => $kecocokan,
        ]);

        return redirect()->route('rw.kondisi.index')->with('success', 'Kondisi berhasil ditambahkan.');
    }

    public function predict()
    {
        $data = Klasifikasi::with('penduduk')->get();
        $samples = [];
        $labels = [];
        $dataToSend = [];

        foreach ($data as $item) {
            $samples[] = [$item->keterangan];
            $labels[] = $item->keterangan == 'layak' ? 'layak' : 'tidak layak';
            $dataToSend[] = ['id_penduduk' => $item->id_penduduk, 'keterangan' => $item->keterangan, 'nama' => $item->penduduk->Nama_lengkap, 'nik' => $item->penduduk->NIK, 'id' => $item->id];
        }

        $classifier = new NaiveBayes();
        $classifier->train($samples, $labels);

        $predictions = [];
        foreach ($dataToSend as $index => $info) {
            $result = $classifier->predict([$info['keterangan']]);
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
