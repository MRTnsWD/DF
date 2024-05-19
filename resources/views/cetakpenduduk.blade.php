<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .table {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .table th,
            .table td {
                padding: 8px;
                border: 1px solid #dee2e6;
            }

            h3 {
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center mb-4">Data Penduduk</h3>

        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No KK</th>
                    <th class="text-center">NIK</th>
                    <th class="text-center">Pas Foto</th>
                    <th class="text-center">Nama Lengkap</th>
                    <th class="text-center">Hubungan Keluarga</th>
                    <th class="text-center">Jenis Kelamin</th>
                    <th class="text-center">Tempat Lahir</th>
                    <th class="text-center">Tanggal Lahir</th>
                    <th class="text-center">Agama</th>
                    <th class="text-center">Pendidikan Terakhir</th>
                    <th class="text-center">Jenis Bantuan</th>
                    <th class="text-center">Penerima Bantuan</th>
                    <th class="text-center">Pekerjaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penduduk as $p)
                    <tr>
                        <td>{{ $p->No_KK }}</td>
                        <td>{{ $p->NIK }}</td>
                        <td class="text-center">
                            @if ($p->pas_foto)
                                <img src="{{ asset('storage/pas_foto/' . $p->pas_foto) }}" alt="Pas Foto"
                                    style="width: 50px;">
                            @else
                                Tidak Ada Foto
                            @endif
                        </td>
                        <td>{{ $p->Nama_lengkap }}</td>
                        <td>{{ $p->JK }}</td>
                        <td>{{ $p->tmpt_lahir }}</td>
                        <td>{{ $p->tgl_lahir }}</td>
                        <td>{{ $p->Agama }}</td>
                        <td>{{ $p->Pendidikan_terakhir }}</td>
                        <td>{{ $p->Jenis_bantuan }}</td>
                        <td>{{ $p->Penerima_bantuan }}</td>
                        <td>{{ $p->pekerjaan->Pekerjaan }}</td>
                        <td class="text-center">
                            @if ($p->kondisiRumah->foto_rumah)
                                <img src="{{ asset('storage/foto_rumah/' . $p->kondisiRumah->foto_rumah) }}"
                                    alt="Pas Foto" style="width: 50px;">
                            @else
                                Tidak Ada Foto
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data dalam database kami.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Print Windows --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
