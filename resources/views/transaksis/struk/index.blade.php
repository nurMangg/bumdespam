<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran PAMSIMAS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .struk {
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
            box-sizing: border-box;
        }

        .struk h1, .struk h2, .struk h3 {
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .kop, .footer {
            text-align: center;
        }
        .header, .content, .footer {
            margin-bottom: 20px;
        }
        .content {
            text-align: left;
        }
        .content table {
            width: 100%;
        }
        .content table td {
            padding: 4px;
        }
        .footer {
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="kop">
            {{-- <h2>KOP</h2> --}}
            <img src="{{ public_path('images/logolandscape.png') }}" alt="Logo-bumdes">
            <hr>
        </div>
        <div class="header">
            <h3>STRUK PEMBAYARAN PAMSIMAS</h3>
            <p>No. Ref : {{ $data['tagihanKode']}}</p>
            <p>Kasir : {{ $data['pembayaranKasirName'] ?? 'Aplikasi' }}</p>
        </div>
        <div class="content">
            <table>
                <tr>
                    <td>Tgl. Bayar</td>
                    <td>: {{ $data['date'] }}</td>
                </tr>
                <tr>
                    <td>No. Meter</td>
                    <td>: {{ $data['pelangganKode'] }}</td>
                </tr>
                <tr>
                    <td>Nama Pelanggan</td>
                    <td>: {{ $data['pelangganNama'] }}</td>
                </tr>
                <tr>
                    <td>Stand Meter</td>
                    <td>: {{ $data['tagihanMeteranAwal']}} - {{$data['tagihanMeteranAkhir'] }}</td>
                </tr>
                <tr>
                    <td>Tagihan bln.</td>
                    <td>: {{ $data['nama_bulan'] }} &nbsp; {{ $data['tagihanTahun'] }}</td>
                </tr>
                <tr>
                    <td>Jml. Tagihan</td>
                    <td>: Rp. {{ $data['formattedTagihanTotal'] }},-</td>
                </tr>
                <tr>
                    <td>Abonemen</td>
                    <td>: Rp. {{ $data['formattedTotalDenda'] }},-</td>
                </tr>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td><strong>: Rp. {{ $data['formattedTotal'] }},-</strong></td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <p>Simpanlah Struk Ini Sebagai</p>
            <p>Bukti Pembayaran Anda</p>
            <hr>
            <p><strong>TERIMA KASIH</strong></p>
        </div>
    <p>Dicetak pada tanggal : {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>

    </div>
</body>
</html>