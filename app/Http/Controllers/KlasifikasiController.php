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
        $klasifikasi = Klasifikasi::with('penduduk')->get();
        // C4.5
        $counttot = Klasifikasi::count();
        $countya = Klasifikasi::where('kecocokan', 'Ya')->count();
        $counttidak = Klasifikasi::where('kecocokan', 'Tidak')->count();
        $countpeklayak = Klasifikasi::where('pekerjaan', 'Layak')->count();
        $countpeklayakya = Klasifikasi::where('pekerjaan', 'Layak')->where('kecocokan', 'Ya')->count();
        $countpeklayaktidak = Klasifikasi::where('pekerjaan', 'Layak')->where('kecocokan', 'Tidak')->count();
        $countpektidaklayak = Klasifikasi::where('pekerjaan', 'Tidak Layak')->count();
        $countpektidaklayakya = Klasifikasi::where('pekerjaan', 'Tidak Layak')->where('kecocokan', 'Ya')->count();
        $countpektidaklayaktidak = Klasifikasi::where('pekerjaan', 'Tidak Layak')->where('kecocokan', 'Tidak')->count();
        $countkondisilayak = Klasifikasi::where('kondisi', 'Layak')->count();
        $countkondisilayakya = Klasifikasi::where('kondisi', 'Layak')->where('kecocokan', 'Ya')->count();
        $countkondisilayaktidak = Klasifikasi::where('kondisi', 'Layak')->where('kecocokan', 'Tidak')->count();
        $countkondisitidaklayak = Klasifikasi::where('kondisi', 'Tidak Layak')->count();
        $countkondisitidaklayakya = Klasifikasi::where('kondisi', 'Tidak Layak')->where('kecocokan', 'Ya')->count();
        $countkondisitidaklayaktidak = Klasifikasi::where('kondisi', 'Tidak Layak')->where('kecocokan', 'Tidak')->count();
        $countpendlayak = Klasifikasi::where('pendidikan', 'Layak')->count();
        $countpendlayakya = Klasifikasi::where('pendidikan', 'Layak')->where('kecocokan', 'Ya')->count();
        $countpendlayaktidak = Klasifikasi::where('pendidikan', 'Layak')->where('kecocokan', 'Tidak')->count();
        $countpendtidaklayak = Klasifikasi::where('pendidikan', 'Tidak Layak')->count();
        $countpendtidaklayakya = Klasifikasi::where('pendidikan', 'Tidak Layak')->where('kecocokan', 'Ya')->count();
        $countpendtidaklayaktidak = Klasifikasi::where('pendidikan', 'Tidak Layak')->where('kecocokan', 'Tidak')->count();
        $entropytot = $this->calculateEntropy($counttidak, $countya, $counttot);
        $entropypeklayak = $this->calculateEntropy($countpeklayakya, $countpeklayaktidak, $countpeklayak);
        $entropypektidaklayak = $this->calculateEntropy($countpektidaklayakya, $countpektidaklayaktidak, $countpektidaklayak);
        $entropykondisilayak = $this->calculateEntropy($countkondisilayakya, $countkondisilayaktidak, $countkondisilayak);
        $entropykondisitidaklayak = $this->calculateEntropy($countkondisitidaklayakya, $countkondisitidaklayaktidak, $countkondisitidaklayak);
        $entropypendlayak = $this->calculateEntropy($countpendlayakya, $countpendlayaktidak, $countpendlayak);
        $entropypendtidaklayak = $this->calculateEntropy($countpendtidaklayakya, $countpendtidaklayaktidak, $countpendtidaklayak);

        $gainpekerjaan = $entropytot - (($countpeklayak / $counttot) * $entropypeklayak + ($countpektidaklayak / $counttot) * $entropypektidaklayak);
        $gainkondisi = $entropytot - (($countkondisilayak / $counttot) * $entropykondisilayak + ($countkondisitidaklayak / $counttot) * $entropykondisitidaklayak);
        $gainpendidikan = $entropytot - (($countpendlayak / $counttot) * $entropypendlayak + ($countpendtidaklayak / $counttot) * $entropypendtidaklayak);

        $gains = [
            'gainpekerjaan' => $gainpekerjaan,
            'gainkondisi' => $gainkondisi,
            'gainpendidikan' => $gainpendidikan
        ];
        $highestGain = max($gains);
        $highestGainKey = array_search($highestGain, $gains);

        // Jika $highestGainKey adalah 'gainpekerjaan' maka yang pekeerjaan valuenya layak di tabel klasifikasi keterangannya diupdate menjadi 'Ya'
        // Jika $highestGainKey adalah 'gainkondisi' maka yang kondisi valuenya layak di tabel klasifikasi keterangannya diupdate menjadi 'Ya'
        // Jika $highestGainKey adalah 'gainpendidikan' maka yang pendidikan valuenya layak di tabel klasifikasi keterangannya diupdate menjadi 'Ya'
        if ($highestGainKey == 'gainpekerjaan') {
            Klasifikasi::where('pekerjaan', 'Layak')->update(['keterangan' => 'layak']);
            Klasifikasi::where('pekerjaan', 'Tidak Layak')->update(['keterangan' => 'tidak layak']);
        } elseif ($highestGainKey == 'gainkondisi') {
            Klasifikasi::where('kondisi', 'Layak')->update(['keterangan' => 'layak']);
            Klasifikasi::where('kondisi', 'Tidak Layak')->update(['keterangan' => 'tidak layak']);
        } elseif ($highestGainKey == 'gainpendidikan') {
            Klasifikasi::where('pendidikan', 'Layak')->update(['keterangan' => 'layak']);
            Klasifikasi::where('pendidikan', 'Tidak Layak')->update(['keterangan' => 'tidak layak']);
        }

        // naive byes dari tabel klasifikasi

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

        return view('klasifikasi.index', ['predictions' => $predictions, 'klasifikasi' => $klasifikasi], compact('klasifikasi', 'highestGainKey', 'counttot', 'countya', 'counttidak', 'countpeklayak', 'countpeklayakya', 'countpeklayaktidak', 'countpektidaklayak', 'countpektidaklayakya', 'countpektidaklayaktidak', 'countkondisilayak', 'countkondisilayakya', 'countkondisilayaktidak', 'countkondisitidaklayak', 'countkondisitidaklayakya', 'countkondisitidaklayaktidak', 'entropytot', 'entropypeklayak', 'entropypektidaklayak', 'entropykondisilayak', 'entropykondisitidaklayak', 'gainpekerjaan', 'gainkondisi', 'gainpendidikan', 'countpendlayak', 'countpendlayakya', 'countpendlayaktidak', 'countpendtidaklayak', 'countpendtidaklayakya', 'countpendtidaklayaktidak', 'entropypendlayak', 'entropypendtidaklayak'));
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
