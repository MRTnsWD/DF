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
                    <th class="text-center">PAS FOTO</th>
                    <th class="text-center">NIK</th>
                    <th class="text-center">NAMA</th>
                    <th class="text-center">KONDISI RUMAH</th>
                    <th class="text-center">Klasifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($predictions as $prediction)
                    @php
                        // Temukan kondisi yang sesuai untuk prediksi ini
                        $kondisiItem = $kondisi->firstWhere('id_penduduk', $prediction['id_penduduk']);
                    @endphp
                    <tr>
                        <td class="text-center">
                            @if ($prediction['pas_foto'])
                                <img src="{{ asset('storage/pas_foto/' . $prediction['pas_foto']) }}" alt="Pas Foto"
                                    style="width: 50px;">
                            @else
                                Tidak Ada Foto
                            @endif
                        </td>
                        <td>{{ $prediction['nik'] }}</td>
                        <td>{{ $prediction['nama'] }}</td>
                        <td>
                            @if ($kondisiItem)
                                <img src="{{ asset('storage/foto_rumah/' . $kondisiItem->foto_rumah) }}" alt=""
                                    style="width: 100px">
                            @else
                                <span>Foto tidak tersedia</span>
                            @endif
                        </td>
                        <td>{{ $prediction['klasifikasi'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data dalam database
                            kami.</td>
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
