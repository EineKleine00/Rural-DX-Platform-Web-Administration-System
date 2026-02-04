<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        $totalWarga = Warga::count();
        $totalTemplates = Template::count();
        $userRole = auth()->user()->role;
        $isAdmin = $userRole === 'admin';

        // Data untuk chart pertumbuhan penduduk
        $pertumbuhanPenduduk = $this->getPertumbuhanPenduduk();
        
        // Data untuk pie chart jenis surat
        $jenisSuratData = $this->getJenisSuratData();

        // Statistik tambahan
        $stats = $this->getAdditionalStats();

        // 🔥 DATA REAL-TIME: Statistik surat bulan ini
        $suratStatistik = $this->getSuratStatistik();

        // 🔥 DATA USER ONLINE (Hanya untuk admin)
        $onlineUsers = [];
        if ($isAdmin) {
            $onlineUsers = User::whereNotNull('session_id')->get();
        }

        // 🔥 DATA AKTIVITAS TERBARU
        $recentActivities = $this->getRecentActivities();

        // 🔥 DATA WARGA BARU
        $newWargas = Warga::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalWarga', 
            'totalTemplates', 
            'userRole', 
            'isAdmin',
            'pertumbuhanPenduduk',
            'jenisSuratData',
            'stats',
            'onlineUsers',
            'suratStatistik',
            'recentActivities',
            'newWargas'
        ));
    }

    private function getPertumbuhanPenduduk()
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->translatedFormat('M Y');
            $months[] = $monthName;

            // Hitung total warga sampai bulan tersebut (gunakan created_at)
            $total = Warga::whereYear('created_at', '<=', $month->year)
                         ->whereMonth('created_at', '<=', $month->month)
                         ->count();
            $data[] = $total;
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getJenisSuratData()
    {
        // Data real dari counter files
        $counterDir = storage_path('app/counters');
        $tahunIni = date('Y');
        $bulanIni = date('n');
        
        $suratData = [
            'Surat Keterangan' => 0,
            'Surat Pengantar' => 0,
            'Surat Pernyataan' => 0,
            'Lainnya' => 0
        ];

        if (file_exists($counterDir)) {
            $files = glob($counterDir . "/*_{$tahunIni}_{$bulanIni}.txt");
            
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/(\d+)_(\d+)_(\d+)\.txt/', $filename, $matches)) {
                    $kode = $matches[1];
                    $count = (int) file_get_contents($file);
                    
                    // Kategorikan berdasarkan kode
                    if (in_array($kode, ['301','302','303','304','305'])) {
                        $suratData['Surat Keterangan'] += $count;
                    } elseif (in_array($kode, ['401','402','403'])) {
                        $suratData['Surat Pengantar'] += $count;
                    } elseif (in_array($kode, ['501','502','503'])) {
                        $suratData['Surat Pernyataan'] += $count;
                    } else {
                        $suratData['Lainnya'] += $count;
                    }
                }
            }
        }

        return [
            'labels' => array_keys($suratData),
            'data' => array_values($suratData),
            'colors' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
        ];
    }

    private function getAdditionalStats()
    {
        $wargaHariIni = Warga::whereDate('created_at', today())->count();
        $wargaBulanIni = Warga::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        return [
            'warga_aktif' => Warga::where('status_hidup', 'Hidup')->count(),
            'warga_pria' => Warga::where('jenis_kelamin', 'Laki-laki')->count(),
            'warga_wanita' => Warga::where('jenis_kelamin', 'Perempuan')->count(),
            'kk_total' => Warga::whereNotNull('no_kk')->distinct('no_kk')->count('no_kk'),
            'rt_terbanyak' => $this->getRTterbanyak(),
            'warga_hari_ini' => $wargaHariIni,
            'warga_bulan_ini' => $wargaBulanIni,
            'rata_keluarga' => $this->getRataKeluarga(),
        ];
    }

    private function getRTterbanyak()
    {
        $rt = Warga::select('rt', DB::raw('COUNT(*) as total'))
            ->groupBy('rt')
            ->orderBy('total', 'desc')
            ->first();

        return $rt ? "RT {$rt->rt} ({$rt->total} warga)" : '-';
    }

    private function getRataKeluarga()
    {
        $totalWarga = Warga::count();
        $totalKK = Warga::whereNotNull('no_kk')->distinct('no_kk')->count('no_kk');
        
        return $totalKK > 0 ? round($totalWarga / $totalKK, 1) : 0;
    }

    /**
     * 🔥 METHOD BARU: Statistik Surat Real-time
     */
    private function getSuratStatistik()
    {
        $counterDir = storage_path('app/counters');
        $tahunIni = date('Y');
        $bulanIni = date('n');
        
        $totalBulanIni = 0;
        $totalTahunIni = 0;

        if (file_exists($counterDir)) {
            // Hitung bulan ini
            $filesBulanIni = glob($counterDir . "/*_{$tahunIni}_{$bulanIni}.txt");
            foreach ($filesBulanIni as $file) {
                $totalBulanIni += (int) file_get_contents($file);
            }

            // Hitung tahun ini
            $filesTahunIni = glob($counterDir . "/*_{$tahunIni}_*.txt");
            foreach ($filesTahunIni as $file) {
                $totalTahunIni += (int) file_get_contents($file);
            }
        }

        return [
            'bulan_ini' => $totalBulanIni,
            'tahun_ini' => $totalTahunIni,
            'target' => 100, // Target bulanan
            'progress' => min(100, ($totalBulanIni / 100) * 100)
        ];
    }

    /**
     * 🔥 METHOD BARU: Aktivitas Terbaru
     */
    private function getRecentActivities()
    {
        $activities = [];

        // Warga baru hari ini
        $wargaBaru = Warga::whereDate('created_at', today())->count();
        if ($wargaBaru > 0) {
            $activities[] = [
                'icon' => 'fas fa-user-plus',
                'color' => 'success',
                'text' => "{$wargaBaru} warga baru ditambahkan hari ini",
                'time' => 'Hari ini'
            ];
        }

        // Surat dibuat hari ini (dummy data - bisa diintegrasikan dengan log)
        $suratHariIni = rand(0, 5);
        if ($suratHariIni > 0) {
            $activities[] = [
                'icon' => 'fas fa-file-alt',
                'color' => 'primary',
                'text' => "{$suratHariIni} surat dibuat hari ini",
                'time' => 'Hari ini'
            ];
        }

        // System update
        $activities[] = [
            'icon' => 'fas fa-sync',
            'color' => 'info',
            'text' => 'Sistem berjalan normal',
            'time' => 'Terakhir update: ' . now()->format('H:i')
        ];

        return $activities;
    }

    /**
     * 🔥 METHOD UNTUK FORCE LOGOUT USER (Hanya admin)
     */
    /**
 * 🔥 METHOD UNTUK FORCE LOGOUT USER (Hanya admin)
 */
    public function forceLogout(Request $request, $userId)
    {
        // Check session validity dulu
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // Hanya admin yang bisa force logout
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $user = User::find($userId);
        if ($user) {
            // Reset session_id di database
            $user->update(['session_id' => '']);
            
            // Hapus session dari table sessions
            \DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();
                
            \Log::info('User force logged out', [
                'admin' => Auth::user()->name,
                'target_user' => $user->name,
                'ip' => $request->ip()
            ]);
                
            return redirect()->back()->with('success', 'Berhasil force logout: ' . $user->name);
        }

        return redirect()->back()->with('error', 'User tidak ditemukan.');
    }
}