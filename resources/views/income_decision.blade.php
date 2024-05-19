<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Kelayakan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Prediksi Kelayakan Penerima Bantuan</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Klasifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($predictions as $prediction)
                    <tr>
                        <td>{{ $prediction['nama'] }}</td>
                        <td>{{ $prediction['klasifikasi'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
