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
                            @if ($detailTagihan->tagihanStatus == "Belum Lunas")
                                <select class="form-control" name="" id="paymentMethod" onchange="updateRow(this)">
                                    <option value="" disabled selected>Pilih Metode</option>
                                    @foreach($paymentMethod as $pay)
                                        <option value="{{ $pay['value'] }}" data-price="{{ $pay['price'] }}">{{ $pay['label'] }}</option>
                                    @endforeach
                                </select>
                            @else
                                <h5>{{ $detailTagihan->pembayaranInfo->pembayaranMetode }}</h5>                                
                            @endif
                            

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
                                    const paymentPriceNumber = parseFloat(paymentPrice.replace('%', '')) || 0;
                                    document.getElementById('pembayaranAdminFee').value = paymentPriceNumber;
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
                        @if ($detailTagihan->tagihanStatus == "Lunas")
                            <tr>
                                <td></td>
                                <td>Abondemen ({{ date_create($detailTagihan->pembayaranInfo->updated_at)->diff(date_create($detailTagihan->tagihanTanggal))->m > 1 ? date_create($detailTagihan->pembayaranInfo->updated_at)->diff(date_create($detailTagihan->tagihanTanggal))->m . ' bulan' : '' }})</td>
                                <td>Rp. {{ number_format($detailTagihan->pembayaranInfo->pembayaranDenda ?? 0, 0, ',', '.') }}</td>
                                
                            </tr>
                        @else
                            @if ($detailTagihan->tagihanTanggal && date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1)
                                <tr>
                                    <td></td>
                                    <td>Abondemen ({{ date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1 ? date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m . ' bulan' : '' }})</td>
                                    <td>Rp. {{ number_format((date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1 ? $detailTagihan->tagihanInfoDenda * date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m : 0), 0, ',', '.') }}</td>
                                    
                                </tr>
                            @endif
                        @endif
                        
                        @if ($detailTagihan->tagihanStatus == "Belum Lunas")
                        <tr id="paymentRow">
                            <td></td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        @else
                        <tr id="">
                            <td>2</td>
                            <td>Biaya Admin {{ $detailTagihan->pembayaranInfo->pembayaranMetode }}</td>
                            <td>{{ $detailTagihan->pembayaranInfo->pembayaranMetode == "QRIS" ? $detailTagihan->pembayaranInfo->pembayaranAdminFee : 'Rp. ' . number_format($detailTagihan->pembayaranInfo->pembayaranAdminFee ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        
                        @for ($i = 0; $i < 3; $i++)
                            <tr>
                                <td></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endfor
                        <tr>
                            <td colspan="2" style="text-align: right; font-weight: bold;">Total Tagihan</td>
                            <td id="totalTagihan"></td>
                        </tr>
                        <input type="text" id="pembayaranAdminFee" hidden>
                        <input type="number" id="totalTagihanVal" hidden>
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
                                document.getElementById('totalTagihanVal').value = total;
                            }
                        </script>
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
                                @if($detailTagihan->tagihanStatus != "Lunas")
                                <tr>
                                    <td>Penalty</td>
                                    <td>&emsp;&emsp;: <span id="penaltyTagihan">
                                        Rp. {{ number_format((date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1 ? $detailTagihan->tagihanInfoDenda * date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m : 0), 0, ',', '.') }}
                                        <input type="text" id="penaltyTagihanVal" value="{{ (date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m > 1 ? $detailTagihan->tagihanInfoDenda * date_create()->diff(date_create($detailTagihan->tagihanTanggal))->m : 0) }}" hidden>
                                    </span></td>
                                </tr>
                                @endif
                                
            
                            </table>
                        </div>
                        <div class="col-md-4"></div>
                        @if ($detailTagihan->tagihanStatus == "Lunas")
                        <div class="col-md-4 text-center">
                            <img src="{{ asset('images/pembayaranlunas.png') }}" alt="Bayar" width="150px">
                        </div>
                        @endif
                        
                    </div>
                    
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-right">
                @if ($detailTagihan->tagihanStatus == "Belum Lunas")
                    <button id="payButtonTunai" class="btn btn-success">Bayar Tunai</button>
                    <button id="payButton" class="btn btn-outline-primary" disabled>Bayar Sekarang</button>
                    
                @elseif ($detailTagihan->tagihanStatus == "Lunas")
                    <button id="cetakStruk" class="btn btn-outline-primary">Unduh Struk</button>
                @else
                    <button id="cekPay" class="btn btn-outline-primary">Cek Pembayaran</button>

                @endif
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

        $('#payButton').click(function (e) {
            e.preventDefault();
            const paymentValue = $('#paymentMethod').val();
            if (!paymentValue) {
                alert('Please select a payment method.');
                return;
            }

            $(this).html('Processing...').prop('disabled', true);

            $.ajax({
                url: '{{route('transaksi.createsnaptoken')}}',
                type: 'POST',
                data: {
                    tagihanId: "{{ $tagihanIdCrypt ?? '' }}",
                    paymentMethod: paymentValue,
                    pembayaranDenda: $('#penaltyTagihanVal').val(),
                    totalTagihan: $('#totalTagihanVal').val(),
                    pembayaranAdminFee: $('#pembayaranAdminFee').val()

                },
                success: function (response) {
                    $('#payButton').html('Bayar Sekarang').prop('disabled', false);

                    const snapToken = response.snap_token;
                    const orderId = response.order_id;
                    
                    executeSnap(snapToken);
                    
                },
                error: function (xhr) {
                    toastr.error("Pembayaran Sudah Ada!, Selesaikan Pembayaran");
                    $('#payButton').html('Bayar Sekarang').prop('disabled', false);
                }
            });
        });

        $('#payButtonTunai').click(function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Pembayaran Tunai',
                text: "Pastikan Anda sudah membayar tagihan sebelum mengkonfirmasi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, saya sudah membayar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{route('transaksi.pembayarantunai')}}',
                        type: 'POST',
                        data: {
                            tagihanId: "{{ $tagihanIdCrypt ?? '' }}",
                            pembayaranDenda: $('#penaltyTagihanVal').val(),
                            totalTagihan: $('#totalTagihanVal').val(),
                            pembayaranAdminFee: $('#pembayaranAdminFee').val()
                        },
                        success: function (response) {
                            toastr.success("Pembayaran Berhasil!");
                            window.location.reload();
                        },
                        error: function (xhr) {
                            toastr.error("Terjadi kesalahan saat memproses pembayaran.");
                        }
                    });
                }
            });
        });

        $('#cetakStruk').click(function (e) {
            e.preventDefault();
            var id = "{{ $tagihanIdCrypt ?? '' }}";
            window.open("{{ route('transaksi.struk', ':id') }}".replace(':id', id), '_blank');
        });



        $('#cekPay').click(function (e) {
            var snapToken = '{{ $detailTagihan->pembayaranInfo->midtransPayment->midtransPaymentSnapToken ?? '' }}';
            executeSnap(snapToken);
        })

        function executeSnap(snapToken) {
            var tagihanId = "{{ $detailTagihan->tagihanId }}";
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    toastr.warning("Pembayaran Berhasil!");
                    // $.ajax({
                    //     url: '{{ route('transaksi.updateDatabase') }}',
                    //     type: 'POST',
                    //     data: {
                    //         tagihanId: tagihanId,
                    //         orderId: result.order_id,
                    //         transactionId: result.transaction_id,
                    //         status: result.transaction_status
                    //     },
                    //     success: function(response) {
                    //         window.location.reload();
                    //     },
                    //     error: function(xhr) {
                    //         console.error('Database update error:', xhr);
                    //         alert('An error occurred while updating the database.');
                    //     }
                    // });
                    window.location.reload();

                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    toastr.warning("Pembayaran Pending, Menunggu Konfirmasi");

                    // $.ajax({
                    //     url: '{{ route('transaksi.updateDatabase') }}',
                    //     type: 'POST',
                    //     data: {
                    //         tagihanId: tagihanId,
                    //         orderId: result.order_id,
                    //         snapToken: snapToken,
                    //         transactionId: result.transaction_id,
                    //         status: result.transaction_status
                    //     },
                    //     success: function(response) {
                    //         window.location.reload();
                    //     },
                    //     error: function(xhr) {
                    //         console.error('Database update error:', xhr);
                    //         alert('An error occurred while updating the database.');
                    //     }
                    // });
                    window.location.reload();
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    $('#payButton').html('Bayar Sekarang').prop('disabled', false);
                    alert('There was an error processing the payment. Please try again.');
                }
            });
        }

    })
</script>
    
@endsection
