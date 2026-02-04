<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        $filter = $request->get('filter', 'agama');
        $dataRW = $this->getLaporanData($filter);
        $chartData = $this->prepareChartDataPerRT($dataRW, $filter);

        // 🔥 Statistik tambahan untuk card
        $totalWarga = Warga::count();
        $totalKK = Warga::distinct('no_kk')->count('no_kk');
        $totalRT = $this->getTotalRT($dataRW);

        return view('laporan.index', compact('dataRW', 'filter', 'chartData', 'totalWarga', 'totalKK', 'totalRT'));
    }

    private function getLaporanData($filter)
    {
        $wargas = Warga::all();
        $dataRW = [];

        foreach ($wargas->groupBy('rw') as $rw => $groupRW) {
            $dataRT = [];
            foreach ($groupRW->groupBy('rt') as $rt => $groupRT) {
                $counts = match ($filter) {
                    'agama' => $groupRT->countBy('agama'),
                    'jenis_kelamin' => $groupRT->countBy('jenis_kelamin'),
                    'status_nikah' => $groupRT->countBy('status_nikah'),
                    'pekerjaan' => $groupRT->countBy('pekerjaan'),
                    'pendidikan' => $groupRT->countBy('pendidikan'),
                    'usia' => $this->kelompokUsia($groupRT),
                    'kk' => collect(['Jumlah KK' => $groupRT->unique('no_kk')->count()]),
                    default => collect(),
                };
                $dataRT[$rt] = $counts;
            }
            $dataRW[$rw] = $dataRT;
        }

        return $dataRW;
    }

    public function download(Request $request)
    {
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        $filter = $request->get('filter', 'agama');
        $dataRW = $this->getLaporanData($filter);

        $pdf = Pdf::loadView('laporan.pdf', compact('dataRW', 'filter'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("laporan_penyebaran_warga_{$filter}.pdf");
    }

    private function kelompokUsia($group)
    {
        $ranges = [
            'Balita (0-5)' => [0, 5],
            'Anak (6-12)' => [6, 12],
            'Remaja (13-17)' => [13, 17],
            'Pemuda (18-25)' => [18, 25],
            'Dewasa (26-35)' => [26, 35],
            'Paruh Baya (36-45)' => [36, 45],
            'Pra-Lansia (46-55)' => [46, 55],
            'Lansia (56-65)' => [56, 65],
            'Manula (65+)' => [66, 150],
        ];

        $result = collect();
        
        foreach ($ranges as $label => [$min, $max]) {
            $count = $group->filter(function ($w) use ($min, $max) {
                if (!$w->tanggal_lahir) return false;
                
                $umur = Carbon::parse($w->tanggal_lahir)->age;
                return $umur >= $min && $umur <= $max;
            })->count();
            
            if ($count > 0) {
                $result[$label] = $count;
            }
        }

        // Tambahkan kategori untuk yang tidak ada tanggal lahir
        $tanpaTanggalLahir = $group->filter(function ($w) {
            return !$w->tanggal_lahir;
        })->count();

        if ($tanpaTanggalLahir > 0) {
            $result['Tidak Diketahui'] = $tanpaTanggalLahir;
        }

        return $result;
    }

    /**
     * 🔥 METHOD BARU: Siapkan data untuk Pie Chart per RT
     */
    private function prepareChartDataPerRT($dataRW, $filter)
    {
        $chartData = [];

        foreach ($dataRW as $rw => $rtGroup) {
            foreach ($rtGroup as $rt => $counts) {
                // Filter hanya kategori yang memiliki data
                $filteredData = $counts->filter(fn($count) => $count > 0);

                if ($filteredData->count() > 0) {
                    $chartKey = "RW{$rw}_RT{$rt}";
                    
                    $chartData[$chartKey] = [
                        'rw' => $rw,
                        'rt' => $rt,
                        'labels' => $filteredData->keys()->toArray(),
                        'datasets' => [
                            [
                                'label' => $this->getFilterLabel($filter),
                                'data' => $filteredData->values()->toArray(),
                                'total' => $filteredData->sum()
                            ]
                        ]
                    ];
                }
            }
        }

        return $chartData;
    }

    /**
     * 🔥 METHOD BARU: Get label untuk filter
     */
    private function getFilterLabel($filter)
    {
        return match ($filter) {
            'agama' => 'Agama',
            'jenis_kelamin' => 'Jenis Kelamin',
            'status_nikah' => 'Status Nikah',
            'pekerjaan' => 'Pekerjaan',
            'pendidikan' => 'Pendidikan',
            'usia' => 'Kelompok Usia',
            'kk' => 'Kartu Keluarga',
            default => 'Data',
        };
    }

    /**
     * 🔥 METHOD BARU: Hitung total RT
     */
    private function getTotalRT($dataRW)
    {
        $total = 0;
        foreach ($dataRW as $rw => $rtGroup) {
            $total += count($rtGroup);
        }
        return $total;
    }

    /**
     * 🔥 METHOD BARU: Untuk keperluan PDF (jika perlu)
     */
    private function prepareChartDataForPDF($dataRW, $filter)
    {
        $chartData = [];

        foreach ($dataRW as $rw => $rtGroup) {
            $columns = collect($rtGroup)->flatMap(fn($r) => $r->keys())->unique()->values();
            $datasets = [];

            foreach ($rtGroup as $rt => $counts) {
                $datasets[] = [
                    'label' => "RT $rt",
                    'data' => $columns->map(fn($col) => $counts[$col] ?? 0)->values(),
                ];
            }

            $chartData[$rw] = [
                'labels' => $columns,
                'datasets' => $datasets
            ];
        }

        return $chartData;
    }
}