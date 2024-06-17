<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Klasifikasi;
use App\Models\KondisiRumah;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Phpml\Classification\NaiveBayes;

class PendudukController extends Controller
{
    // Method untuk menampilkan data penduduk
    public function index(Request $request)
    {
        $query = Penduduk::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('No_KK', 'like', "%{$search}%")
                ->orWhere('NIK', 'like', "%{$search}%")
                ->orWhere('Nama_lengkap', 'like', "%{$search}%")
                ->orWhere('Hbg_kel', 'like', "%{$search}%")
                ->orWhere('JK', 'like', "%{$search}%")
                ->orWhere('tmpt_lahir', 'like', "%{$search}%")
                ->orWhere('tgl_lahir', 'like', "%{$search}%")
                ->orWhere('Agama', 'like', "%{$search}%")
                ->orWhere('Pendidikan_terakhir', 'like', "%{$search}%")
                ->orWhere('Jenis_bantuan', 'like', "%{$search}%")
                ->orWhere('Penerima_bantuan', 'like', "%{$search}%")
                ->orWhere('Jenis_bantuan_lain', 'like', "%{$search}%");
        }

        $penduduk = $query->get();
        return view('penduduk.index', compact('penduduk'));
    }


    public function cetakpenduduk()
    {
        $penduduk = Penduduk::with(['pekerjaan', 'kondisiRumah'])->get();

        // Cek apakah ada penduduk yang tidak memiliki pekerjaan atau kondisi rumah
        foreach ($penduduk as $p) {
            if (is_null($p->pekerjaan) || is_null($p->kondisiRumah)) {
                return redirect()->back()->with('error', 'Lengkapi data pekerjaan dan kondisi rumah untuk cetak');
            }
        }

        return view('cetakpenduduk', compact('penduduk'));
    }


    public function cari(Request $request)
    {
        $pendudukIds = Penduduk::where('Nama_lengkap', 'like', "%" . $request->nama . "%")->pluck('id');
        $data = Pekerjaan::with('penduduk')->whereIn('id_penduduk', $pendudukIds)->get();
        $kondisi = KondisiRumah::with('penduduk')->whereIn('id_penduduk', $pendudukIds)->get();
        $samples = [];
        $labels = [];
        $dataToSend = [];

        if ($data->isEmpty()) {
            return redirect()->route('index')->with('error', 'Data tidak ditemukan');
        }

        foreach ($data as $item) {
            $samples[] = [$item->Penghasilan];
            $labels[] = $item->Penghasilan < 5000000 ? 'layak' : 'tidak layak';
            $dataToSend[] = ['id_penduduk' => $item->id_penduduk, 'penghasilan' => $item->Penghasilan, 'nama' => $item->penduduk->Nama_lengkap, 'nik' => $item->penduduk->NIK, 'pas_foto' => $item->penduduk->pas_foto, 'id' => $item->id];
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
                'pas_foto' => $info['pas_foto'],
                'id' => $info['id'],
                'klasifikasi' => $result
            ];
        }
        return view('penduduk', ['predictions' => $predictions], ['kondisi' => $kondisi]);
    }

    public function cetakklasifikasi(Request $request)
    {
        $pendudukIds = Penduduk::where('Nama_lengkap', 'like', "%" . $request->nama . "%")->pluck('id');
        $data = Klasifikasi::with('penduduk')->whereIn('id_penduduk', $pendudukIds)->get();
        $kondisi = KondisiRumah::with('penduduk')->whereIn('id_penduduk', $pendudukIds)->get();
        $samples = [];
        $labels = [];
        $dataToSend = [];

        if ($data->isEmpty()) {
            return redirect()->route('klasifikasi.index')->with('error', 'Data tidak ditemukan');
        }

        foreach ($data as $item) {
            $samples[] = [$item->keterangan];
            $labels[] = $item->keterangan == 'layak' ? 'layak' : 'tidak layak';
            $dataToSend[] = ['id_penduduk' => $item->id_penduduk, 'keterangan' => $item->keterangan, 'nama' => $item->penduduk->Nama_lengkap, 'nik' => $item->penduduk->NIK, 'pas_foto' => $item->penduduk->pas_foto, 'id' => $item->id];
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
                'pas_foto' => $info['pas_foto'],
                'id' => $info['id'],
                'klasifikasi' => $result
            ];
        }
        return view('cetakklasifikasi', ['predictions' => $predictions], ['kondisi' => $kondisi]);
    }

    public function create()
    {
        return view('penduduk.create');
    }

    public function store(Request $request)
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

            // inserrt id penduduk ke tabel klasifikasi
            $klasifikasi = new Klasifikasi();
            $klasifikasi->id_penduduk = $penduduk->id;
            $klasifikasi->save();

            $hasil = new Hasil();
            $hasil->id_penduduk = $penduduk->id;
            $hasil->save();

            return redirect()->route('penduduk.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Error: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $penduduk = Penduduk::find($id);
        return view('penduduk.edit', compact('penduduk'));
    }

    public function update(Request $request, $id)
    {
        if ($request->hasFile('pas_foto')) {
            $file = $request->file('pas_foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/pas_foto', $fileName);
        }
        Penduduk::find($id)->update([
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

        return redirect()->route('penduduk.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Penduduk::find($id)->delete();
        Pekerjaan::where('id_penduduk', $id)->delete();
        Pendidikan::where('id_penduduk', $id)->delete();
        KondisiRumah::where('id_penduduk', $id)->delete();

        return redirect()->route('penduduk.index')->with('success', 'Data berhasil dihapus');
    }
}
