@extends('layouts.app')

<?php 
  $totalPelanggan = \App\Models\Pelanggan::all()->count();
  $totalTagihan = \App\Models\Tagihan::all()->count();

  $tagihan = \App\Models\Tagihan::whereNull('deleted_at')->get();
  $tagihanLimit4 = \App\Models\Tagihan::orderBy('tagihanId', 'desc')->limit(4)->get();
  
  $tagihan->transform(function($item) {
      $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
      $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
      return $item;
  });

  $totalSemuaTagihanBelumLunas = $tagihan->where('tagihanStatus', 'Belum Lunas')->sum('tagihanJumlahTotal');
  $totalSemuaTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal');

  //Transaksi tabel
  function getTagihanByStatus(string $status)
  {
      return DB::table('tagihans')
          ->join('msbulan', 'tagihans.tagihanBulan', '=', 'msbulan.bulanId')
          ->selectRaw('
              CONCAT(msbulan.bulanNama, " ", tagihanTahun) as bulanTahun,
              SUM((tagihans.tagihanMAkhir - tagihans.tagihanMAwal) * tagihans.tagihanInfoTarif + tagihans.tagihanInfoAbonemen) as totalJumlah
          ')
          ->whereNull('tagihans.deleted_at')
          ->where('tagihanStatus', $status)
          ->groupBy('bulanTahun')
          ->orderByRaw('tagihanTahun, tagihanBulan')
          ->pluck('totalJumlah', 'bulanTahun');
  }

  // Fetch data for both statuses
  $tagihanByMonthLunas = getTagihanByStatus('Lunas');
  $tagihanByMonthBelumLunas = getTagihanByStatus('Belum Lunas');

  // Merge keys for all months and sort them
  $allMonths = $tagihanByMonthLunas->keys()
    ->merge($tagihanByMonthBelumLunas->keys())
    ->unique()
    ->sort(function ($a, $b) {
        // Extract year and month from "NamaBulan Tahun" format
        [$monthA, $yearA] = explode(' ', $a);
        [$monthB, $yearB] = explode(' ', $b);

        $monthOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Compare by year first, then by month order
        return ($yearA <=> $yearB) ?: (array_search($monthA, $monthOrder) <=> array_search($monthB, $monthOrder));
    });

  // Prepare labels and datasets
  $labels = $allMonths->values();
  $dataLunas = $labels->map(fn($bulanTahun) => $tagihanByMonthLunas->get($bulanTahun, 0));
  $dataBelumLunas = $labels->map(fn($bulanTahun) => $tagihanByMonthBelumLunas->get($bulanTahun, 0));

  // Data Pelanggan PIE CHART
  $dataPelanggan = \App\Models\Pelanggan::selectRaw("CONCAT('RW ', pelangganRw) as pelangganDesa, COUNT(*) as total")
    ->groupBy('pelangganDesa')
    ->get();

    $backgroundColor = [
        '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de',
        '#39cccc', '#001f3f', '#85144b', '#f012be', '#b10dc9', '#ff851b',
        '#ff4136', '#2ecc40', '#3d9970', '#111111', '#aaaaaa', '#dddddd',
        '#ff851b', '#7fdbff', '#39cccc', '#3b9dcd', '#01ff70', '#ffdc00',
        '#ff851b', '#bada55', '#ff69b4', '#ff6347', '#ffa500', '#8a2be2'
    ];

    $dataPelangganChart = $dataPelanggan->map(function($item, $index) use ($backgroundColor) {
        return [
            'label' => $item->pelangganDesa,
            'backgroundColor' => $backgroundColor[$index % count($backgroundColor)],
            'data' => [$item->total]
        ];
    })->toArray();


?>
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->

        <div class="row">
          @if (Hash::check('password', Auth::user()->password))
          <div class="col-12">
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
              Anda masih menggunakan password default, silakan ubah password default Anda</a>.
            </div>
          </div>
          @endif
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Pelanggan</span>
                <span class="info-box-number">
                  {{ $totalPelanggan }}
                  <small></small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-invoice"></i></span>

              <div class="info-box-content">
                <span class="info-box-text" data-toggle="tooltip" data-placement="top" title="Total tagihan yang belum dibayar">Total Tagihan</span>
                <span class="info-box-number">{{ $totalTagihan }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-circle"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Tagihan Belum Lunas</span>
                <span class="info-box-number">Rp. {{ number_format($totalSemuaTagihanBelumLunas, 0, ',', '.') }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Tagihan Lunas</span>
                <span class="info-box-number">Rp. {{ number_format($totalSemuaTagihanLunas, 0, ',', '.') }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          
        </div>

        <div class="row">
          <div class="col-md-6">
            {{-- <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Online Store Visitors</h3>
                  <a href="javascript:void(0);">View Report</a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">820</span>
                    <span>Visitors Over Time</span>
                  </p>
                  <p class="ml-auto d-flex flex-column text-right">
                    <span class="text-success">
                      <i class="fas fa-arrow-up"></i> 12.5%
                    </span>
                    <span class="text-muted">Since last week</span>
                  </p>
                </div>
                <!-- /.d-flex -->
  
                <div class="position-relative mb-4">
                  <canvas id="visitors-chart" height="200"></canvas>
                </div>
  
                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This Week
                  </span>
  
                  <span>
                    <i class="fas fa-square text-gray"></i> Last Week
                  </span>
                </div>
              </div>
            </div> --}}
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Jumlah Pelanggan Per Desa</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="chart-responsive">
                      <canvas id="pieChart" height="150"></canvas>
                    </div>
                    <!-- ./chart-responsive -->
                  </div>
                  <!-- /.col -->
                  <div class="col-md-3">
                    <ul class="chart-legend clearfix">
                      @foreach (array_slice($dataPelangganChart, 0, ceil(count($dataPelangganChart) / 2)) as $label)
                          <li> <i class="far fa-circle" style="color: {{ $label['backgroundColor'] }}"></i>  {{ $label['label'] }}</li>
                      @endforeach
                    </ul>
                  </div>
                  <div class="col-md-3">
                    <ul class="chart-legend clearfix">
                      @foreach (array_slice($dataPelangganChart, ceil(count($dataPelangganChart) / 2)) as $label)
                          <li> <i class="far fa-circle" style="color: {{ $label['backgroundColor'] }}"></i>  {{ $label['label'] }}</li>
                      @endforeach
                    </ul>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer p-0">
                <ul class="nav nav-pills flex-column">
                  @foreach ($dataPelangganChart as $item)
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      {{ $item['label'] }}
                      <span class="float-right">
                        {{ $item['data'][0] }}
                      </span>
                    </a>
                  </li>
                  @endforeach
                </ul>
              </div>
              <!-- /.footer -->
            </div>
            <!-- /.card -->
          </div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Tagihan Baru</h3>
                <div class="card-tools">
                  {{-- <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-download"></i>
                  </a>
                  <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-bars"></i>
                  </a> --}}
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                  <thead>
                  <tr>
                    <th>Tagihan Kode</th>
                    <th>Nama Pelanggan</th>
                    <th>Pemakaian Air (m3)</th>
                    <th>Tagihan</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach ($tagihanLimit4 as $item)
                  <tr>
                    <td>{{ $item->tagihanKode }}</td>
                    <td>{{ $item->pelanggan->pelangganNama }}</td>
                    <td>{{ $item->tagihanMAwal }} - {{ $item->tagihanMAkhir }} m3</td>
                    <td>Rp. {{ number_format(($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif, 0, ',', '.') }}</td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Total Transaksi</h3>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">Rp. {{ number_format($tagihan->sum('tagihanJumlahTotal'), 0, ',', '.') }} </span>
                    <span>Transaksi per Waktu</span>
                  </p>
                  
                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                  <canvas id="sales-chart" height="200"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> Lunas
                  </span>

                  <span>
                    <i class="fas fa-square text-gray"></i> Belum Lunas
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
</div>
    
@endsection

@push('scripts')

<script>
  var chartLabels = @json($labels);
  var chartDataLunas = @json($dataLunas);
  var chartDataBelumLunas = @json($dataBelumLunas);

  // PIE CHART
  var dataPelangganChart = @json($dataPelangganChart);

  // Ekstrak labels dan data
  var pieLabels = dataPelangganChart.map(item => item.label);
  var pieData = dataPelangganChart.map(item => item.data[0]);
  var backgroundColors = dataPelangganChart.map(item => item.backgroundColor);

</script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard3.js') }}" defer></script>
{{-- <script src="{{ asset('dist/js/pages/dashboard2.js') }}" defer></script> --}}
@endpush
