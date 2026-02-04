<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 🔐 Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===============================
// 🧭 Route untuk Admin & Petugas
// ===============================
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //pengaturan 
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');


    
    // 🔥 PERBAIKAN: Route force logout dengan parameter
    Route::post('/dashboard/force-logout/{user}', [DashboardController::class, 'forceLogout'])->name('dashboard.force-logout');
    
    // Warga Routes
    Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');
    Route::get('/warga/search-ajax', [WargaController::class, 'searchAjax'])->name('warga.search.ajax');
    
    // Hanya admin yang bisa CRUD warga
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/warga/create', [WargaController::class, 'create'])->name('warga.create');
        Route::post('/warga', [WargaController::class, 'store'])->name('warga.store');
        Route::get('/warga/{warga}/edit', [WargaController::class, 'edit'])->name('warga.edit');
        Route::put('/warga/{warga}', [WargaController::class, 'update'])->name('warga.update');
        Route::delete('/warga/{warga}', [WargaController::class, 'destroy'])->name('warga.destroy');
    });
    
    // Laporan Routes
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/download', [LaporanController::class, 'download'])->name('laporan.download'); 
    
    // Surat Routes
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/generate/{warga_id}/{template_id}', [SuratController::class, 'generate'])->name('surat.generate');
    Route::get('/surat/search-warga', [SuratController::class, 'searchWarga'])->name('surat.search-warga');
    
    // Monitor nomor surat & fields
    Route::get('/surat/monitor', [SuratController::class, 'monitorNomorSurat'])->name('surat.monitor');
    Route::post('/surat/reset-counter', [SuratController::class, 'resetCounter'])->name('surat.reset-counter');
    Route::get('/surat/fields', [SuratController::class, 'showAvailableFields'])->name('surat.fields');
    Route::get('/debug-template/{template_id}', [SuratController::class, 'debugTemplate']);
});

// ===============================
// 🛠️ DEBUG & UTILITY ROUTES
// ===============================

// COMPLETE RESET: Reset semua session dan clear semua
Route::get('/complete-reset-sessions', function() {
    // Reset semua session_id ke empty string
    \App\Models\User::query()->update(['session_id' => '']);
    
    // Clear semua session files
    $sessionPath = storage_path('framework/sessions');
    if (file_exists($sessionPath)) {
        $files = glob($sessionPath . '/*');
        foreach($files as $file) {
            if(is_file($file) && !str_contains($file, '.gitignore')) {
                unlink($file);
            }
        }
    }
    
    // Clear cache
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('cache:clear');
    
    return "✅ SEMUA SESSION TELAH DIBERSIHKAN!<br>
           🔄 <a href='/login'>Klik di sini untuk login</a>";
});

// Check status session di database
Route::get('/check-session-status', function() {
    $users = \App\Models\User::select('id', 'name', 'email', 'session_id')->get();
    
    echo "<h3>Status Session User:</h3>";
    foreach($users as $user) {
        $status = empty($user->session_id) ? '🔴 TIDAK AKTIF' : '🟢 AKTIF';
        echo "{$user->name} ({$user->email}): {$status} - Session: '{$user->session_id}'<br>";
    }
    
    echo "<br><a href='/complete-reset-sessions'>Reset Semua Session</a>";
});

// Check semua route yang tersedia
Route::get('/check-routes', function() {
    $routes = Route::getRoutes();
    
    echo "<h3>Available Routes:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></tr>";
    
    foreach ($routes as $route) {
        echo "<tr>";
        echo "<td>" . implode('|', $route->methods()) . "</td>";
        echo "<td>" . $route->uri() . "</td>";
        echo "<td>" . ($route->getName() ?? '-') . "</td>";
        echo "<td>" . $route->getActionName() . "</td>";
        echo "</tr>";
    }
    echo "</table>";
});

// Debug template fields
Route::get('/debug-template-fields', function() {
    $fields = \App\Models\Template::getAvailableFields();
    
    echo "<h3>🔥 Available Fields untuk Template Word:</h3>";
    foreach($fields as $category => $fieldList) {
        echo "<h4>📁 {$category}:</h4>";
        echo "<div style='background: #f8f9fa; padding: 10px; margin-bottom: 10px; border-left: 4px solid #007bff;'>";
        foreach($fieldList as $field) {
            echo "<code style='background: #e9ecef; padding: 2px 6px; margin: 2px; border-radius: 3px;'>{{$field}}</code> ";
        }
        echo "</div>";
    }
    
    echo "<h3>🎯 Contoh Penggunaan di Word:</h3>";
    echo "<p>Gunakan field dengan kurung kurawal: <code>{nomor_surat}</code></p>";
    echo "<p><strong>Field Nomor Surat:</strong> <code>{nomor_surat}</code>, <code>{no_surat}</code>, <code>{nomor}</code></p>";
    echo "<p><strong>Field Masa Berlaku:</strong> <code>{masa_berlaku}</code>, <code>{berlaku_sampai}</code>, <code>{berlaku_hingga}</code></p>";
    echo "<p><strong>Field Tanggal:</strong> <code>{tanggal}</code>, <code>{tanggal_sekarang}</code></p>";
});