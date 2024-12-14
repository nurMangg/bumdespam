@extends('layouts.app')

@section('content')
<style>
    .table.borderless {
    border: none; 
    width: 100%;
    border-collapse: collapse; 
    }

    .table.borderless tr td {
        padding: 4px 0; 
        border: none; 
    }

    .table.borderless colgroup col {
        width: auto; 
    }

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="#">{{ $breadcrumb ?? env('APP_NAME') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">{{ $title ?? env('APP_NAME') }}</a></li>
                <li class="breadcrumb-item active">Transaksi {{ $detailTagihan->tagihanKode ?? ' -' }}</li>
              </ol>
          </div><!-- /.col -->
          <div class="col-sm-6">
            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container mb-3">
            
        </div>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="card mb-5">

                <img src="{{ asset('images/logo.svg')}}" alt="Logo-bumdes" width="300px" class="img-fluid p-4">
                <div class="pl-5">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Invoice Pembayaran Tagihan AIR PDAM</h6>

                            <div class="d-flex mb-2">
                                <div style="width: 200px;">Nomor Tagihan</div>
                                <div>: {{ $detailTagihan->tagihanKode ?? ' -' }}</div>
                            </div>
                            <div class="d-flex mb-2">
                                <div style="width: 200px;">Tanggal Tagihan</div>
                                <div>: {{ $detailTagihan->created_at ?? ' -' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 pr-5">
                            <label for="">Pilih Metode Pembayaran :</label>
                            <select class="form-control" name="" id="paymentMethod" onchange="updateRow(this)">
                                <option value="" disabled selected>Pilih Metode</option>
                                @foreach($paymentMethod as $pay)
                                    <option value="{{ $pay['value'] }}" data-price="{{ $pay['price'] }}">{{ $pay['label'] }}</option>
                                @endforeach
                            </select>

                            <script>
                                function updateRow(selectElement) {
                                    // Get the selected option
                                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                                    
                                    // Get the value and price from the selected option
                                    const paymentValue = selectedOption.value;
                                    const paymentPrice = selectedOption.getAttribute('data-price');
                                    const paymentLabel = selectedOption.text;
                            
                                    // Get the existing row
                                    const existingRow = document.getElementById('paymentRow');
                            
                                    // Update the existing row's payment method and price
                                    existingRow.cells[0].innerText = 2; // Update the label
                                    existingRow.cells[1].innerText = 'Biaya Admin ' + paymentLabel; // Update the label
                                    existingRow.cells[2].innerHTML = paymentPrice.includes('.') ? paymentPrice : `Rp. ${parseFloat(paymentPrice).toLocaleString('id-ID')}`; // Update the price

                                    calculateTotal();

                                    const payButton = document.getElementById('payButton');
                                    if (paymentValue) {
                                        payButton.disabled = false; // Enable the button
                                    }
                                }
                            </script>
                        </div>
                    </div>
                    
                </div>
                
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tagihanDetailsTable" class="table table-hover">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 70%;">
                    </colgroup>
                    <tr>
                        <td>Kode Pelanggan</td>
                        <td>&emsp;&emsp;: <span
                                id="detailPelangganKode">{{ $detailPelanggan->pelangganKode ?? ' -'}}</span></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td>&emsp;&emsp;: <span id="detailPelangganNama">{{  $detailPelanggan->pelangganNama ?? ' -'}}</span></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>&emsp;&emsp;: <span id="detailPelangganEmail">{{  $detailPelanggan->pelangganEmail ?? ' -' }}</span></td>
                    </tr>
                    <tr>
                        <td>Nomor Hp</td>
                        <td>&emsp;&emsp;: <span
                                id="detailPelangganPhone">{{  $detailPelanggan->pelangganPhone ?? ' -'}}</span></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>&emsp;&emsp;: <span id="detailPelangganAlamat">{{  $detailPelanggan->pelangganAlamat ?? ' -'}}</span></td>
                    </tr>
                    <tr>
                        <td>Golongan Tarif</td>
                        <td>&emsp;&emsp;: <span
                                id="detailPelangganGolonganNama">{{  $detailPelanggan->golongan->golonganNama. ' (' . $detailPelanggan->golongan->golonganTarif . ') ' ?? ' -'}}</span>
                        </td>
                    </tr>

                </table>
                <table class="table table-bordered mb-0 mt-5" id="tindakanTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="65%">Daftar Tagihan</th>
                            <th width="20%">Harga</th>
                        </tr>
                    </thead>
                    <tbody id="paymentTableBody">
                        <tr>
                            <td>1</td>
                            <td>Pembayaran Tagihan AIR PDAM Periode {{ $detailTagihan->tagihanBulan. ' ' . $detailTagihan->tagihanTahun}}</td>
                            <td>Rp. {{ number_format($detailTagihan->pembayaranInfo->pembayaranJumlah ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @if ($detailTagihan->tagihanTanggal && date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1)
                            <tr>
                                <td></td>
                                <td>Tagihan Denda</td>
                                <td>Rp. {{ number_format($detailTagihan->tagihanInfoDenda, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        
                        <tr id="paymentRow">
                            <td></td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        @for ($i = 0; $i < 3; $i++)
                            <tr>
                                <td></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endfor
                        <tr>
                            <td colspan="2" style="text-align: right; font-weight: bold;">Total Tagihan</td>
                            <td id="totalTagihan">
                                <script>
                                    function calculateTotal() {
                                        const rows = document.querySelectorAll('#tindakanTable tbody tr');
                                        let total = 0;

                                        rows.forEach(row => {
                                            const priceCell = row.cells[2]; // Ambil kolom harga
                                            if (priceCell) {
                                                const priceText = priceCell.textContent.trim();
                                                let price = 0;

                                                if (priceText.includes('%')) {
                                                    const percentage = parseFloat(priceText.replace('%', '')) || 0;
                                                    price = total * (percentage / 100);
                                                } else {
                                                    const numericText = priceText.replace(/[^\d]/g, ''); // Hapus karakter non-angka
                                                    price = parseInt(numericText, 10) || 0;
                                                }

                                                total += price; // Tambahkan ke total
                                            }
                                        });

                                        total = Math.round(total); // Pembulatan jika ada koma

                                        // Tampilkan total dalam format Rp.
                                        document.getElementById('totalTagihan').textContent = 
                                            'Rp. ' + total.toLocaleString('id-ID');
                                    }
                                </script>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-5">
                    <h6>Detail Periode Pembayaran :</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <table id="pembayaranDetailsTable" class="table borderless">
                                <colgroup>
                                    <col style="width: 40%;">
                                    <col style="width: 60%;">
                                </colgroup>
                                <tr>
                                    <td>Periode</td>
                                    <td>&emsp;&emsp;: <span
                                            id="periodeTagihan">{{ $detailTagihan->tagihanBulan. ' ' . $detailTagihan->tagihanTahun ?? '-'}}</span></td>
                                </tr>
                                <tr>
                                    <td>Stand Meter</td>
                                    <td>&emsp;&emsp;: <span id="standMeterTagihan">{{ $detailTagihan->tagihanMAwal. ' - ' . $detailTagihan->tagihanMAkhir . ' m3' ?? '-'}}</span></td>
                                </tr>
                                <tr>
                                    <td>Pemakaian</td>
                                    <td>&emsp;&emsp;: <span id="pemakaianTagihan">{{ ($detailTagihan->tagihanMAkhir - $detailTagihan->tagihanMAwal) . ' m3' ?? '-'}}</span></td>
                                </tr>
                                <tr>
                                    <td>Penalty</td>
                                    <td>&emsp;&emsp;: <span id="penaltyTagihan">
                                        {{ $detailTagihan->tagihanTanggal ? (date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1 ? 'Rp. ' . number_format($detailTagihan->tagihanInfoDenda, 0, ',', '.') : '-') : '-' }}
                                    </span></td>
                                </tr>
                                
            
                            </table>
                        </div>
                    </div>
                    
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-right">
                <button id="payButton" class="btn btn-outline-primary" disabled>Bayar Sekarang</button>
              </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>

{{-- <x-form.modal :form="$form" :title="$title ?? env('APP_NAME')" /> --}}

<script type="text/javascript">
    $(document).ready(function () {
        calculateTotal();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('body').on('click', '.edit', function () {
            var id = $(this).data('id');
            window.location.href = '{{ route('aksi-tagihan' . '.index') }}/' + id;
        });

    })
</script>
    
@endsection
