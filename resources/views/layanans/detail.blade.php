@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="#">{{ $breadcrumb ?? env('APP_NAME') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">{{ $title ?? env('APP_NAME') }}</a></li>
                <li class="breadcrumb-item active">{{ $detailPelanggan->pelangganKode ?? ' -' }}</li>
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
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ __('Detail Pelanggan') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" onclick="location.reload()" title="Refresh">
                      <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                
              </div>
              <!-- /.card-header -->
              <div class="card-body" id="collapseExample">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pelanggan</h6>
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
                                        id="detailPelangganGolonganNama">{{  $detailPelanggan->golongan->golonganNama ?? ' -'}}</span>
                                </td>
                            </tr>

                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Tarif Harga</h6>
                        <table id="tagihanDetailsTable2" class="table table-hover">
                            <colgroup>
                                <col style="width: 30%;">
                                <col style="width: 70%;">
                            </colgroup>
                            <tr>
                                <td>Tarif Harga</td>
                                <td>&emsp;&emsp;: <span
                                        id="detailPelangganGolonganHarga">{{ $detailPelanggan->golongan->golonganTarif ?? ' -'}}</span></td>
                            </tr>
                            <tr>
                                <td>Tarif Denda</td>
                                <td>&emsp;&emsp;: <span id="detailPelangganGolonganDenda">{{  $detailPelanggan->golongan->golonganDenda ?? ' -'}}</span></td>
                            </tr>
                        </table>
                        <h6>Informasi Penggunaan</h6>
                        <table id="tagihanDetailsTable3" class="table table-hover">
                            <colgroup>
                                <col style="width: 30%;">
                                <col style="width: 70%;">
                            </colgroup>
                            <tr>
                                <td>Penggunaan Air</td>
                                <td>&emsp;&emsp;: <span
                                        id="detailPelangganGolonganHarga">{{ $penggunaanTagihan->tagihanMAkhir ?? ' -'}} m3</span></td>
                            </tr>
                            <tr>
                                <td>Total Tagihan</td>
                                <td>&emsp;&emsp;: <span id="detailPelangganGolonganDenda">Rp. {{ number_format($penggunaanTagihan->totalTagihan ?? 0, 0, ',', '.') }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>


      {{-- Detail Tagihan Pelanggan --}}
      <div class="container mb-3 mt-5">
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <div class="float-sm-right">
                    <button id="create" class="btn" style="background-color: #608BC1; color: #ffffff">
                        Tambah {{ $title ?? env('APP_NAME') }}
                    </button>
                </div>
                
            </div>
        </div>
        </div>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Daftar {{ __('Data Tagihan Pelanggan') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    
                  </div>
                
              </div>
              <!-- /.card-header -->
              <div class="card-body" id="collapseExample">
                <table class="table" id="laravel_datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            @foreach ($form as $item)
                                <th>{{ $item['label'] }}</th>
                            @endforeach
                            <th>Jumlah Tagihan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
              </div>
              <!-- /.card-body -->
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

<x-form.modal :form="$form" :title="$title ?? env('APP_NAME')" />

<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('#laravel_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route($route . '.index') }}',
                data: {
                    pelangganKode: "{{ $detailPelanggan->pelangganKode ?? '' }}"
                },

                type: 'GET',
                dataType: 'json',
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            },
            columns: [
              {
                data: 'id',
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
              },
              @foreach ($form as $field)
                  {data: '{{ $field['field'] }}', name: '{{ $field['field'] }}'},
              @endforeach
              {data: 'tagihanJumlah', name: 'tagihanJumlah'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            responsive: true,
            scrollX: true,
        });

        $('#create').click(function () {
            $('#saveBtn').val("create-action");
            $('#id').val('');
            $('#addForm').trigger("reset");
            $('#modelHeading').html("Tambah Baru {{ $title ?? env('APP_NAME') }}");
            $('#ajaxModel').modal('show');
            $('#tagihanMAwal').val('{{ number_format(($penggunaanTagihan->tagihanMAkhir ?? 0) + 1, 0, ',', '.') }}')
        });

        $('body').on('click', '.edit', function () {
            var id = $(this).data('id');
            $.get('{{ route($route . '.index') }}/' + id + '/edit', function (data) {
                $('#modelHeading').html("Edit {{ $title ?? env('APP_NAME') }}");
                $('#saveBtn').val("edit-action");
                $('#ajaxModel').modal('show');
                $('#id').val(id);
                @foreach ($form as $field)
                    $('#{{ $field['field'] }}').val(data.{{ $field['field'] }});
                @endforeach
            })
        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $('#saveBtn').html('Mengirim..');

            // Reset error messages
            @foreach ($form as $field)
                @if ($field['required'] ?? false)
                    $('#{{ $field['field'] }}Error').text('');
                @endif
            @endforeach

            // Validate non-empty fields
            var isValid = true;
            @foreach ($form as $field)
                @if ($field['required'] ?? false)
                    if (!$('#{{ $field['field'] }}').val()) {
                        $('#{{ $field['field'] }}Error').text('This field is required.');
                        isValid = false;
                    }
                @endif
            @endforeach

            if (!isValid) {
                $('#saveBtn').html('Simpan Data');
                return;
            }

            var actionType = $(this).val();
            console.log(actionType);
            var url = actionType === "create-action" ? "{{ route($route . '.store') }}" :
                "{{ route($route . '.index') }}/" + $('#id').val();

            // Tentukan jenis permintaan (POST atau PUT)
            var requestType = actionType === "create-action" ? "POST" : "PUT";

            $.ajax({
                data: $('#addForm').serialize() + "&pelangganId=" + "{{ $detailPelanggan->pelangganId ?? '' }}",
                url: url,
                type: requestType,
                dataType: 'json',
                success: function (data) {
                    $('#addForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    $('#laravel_datatable').DataTable().ajax.reload();
                    $('#saveBtn').html('Simpan Data');

                    var message = actionType === "create-action" ? "Data Berhasil ditambahkan!" : "Data berhasil diperbarui!";
                    toastr.success(message);
                },
                error: function (xhr) {
                    $('#saveBtn').html('Simpan Data');

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        @foreach ($form as $field)
                            @if ($field['required'] ?? false)
                                if (errors.{{ $field['field'] }}) {
                                    $('#{{ $field['field'] }}Error').text(errors.{{ $field['field'] }});
                                }
                            @endif
                        @endforeach
                    } else {
                      toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                }
            });
        });

        $('body').on('click', '.delete', function () {
            var id = $(this).data('id');
            confirm("Apakah Anda yakin ingin menghapus data ini?");
            $.ajax({
                type: "DELETE",
                url: "{{ route($route . '.index') }}/" + id,
                success: function (data) {
                    $('#laravel_datatable').DataTable().ajax.reload();
                    toastr.success('Data berhasil dihapus!');
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.error('Terjadi kesalahan saat menghapus data.');
                }
            });
        });
            


    })
</script>
    
@endsection