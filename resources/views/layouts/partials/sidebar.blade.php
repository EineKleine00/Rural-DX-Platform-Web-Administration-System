<div class="sidebar">
    <!-- Header Sidebar -->
    <div class="sidebar-header">
        <h4>
            <i class="fas fa-building me-2"></i>
            Kelurahan App
        </h4>
        <small class="sidebar-subtitle">Sistem Persuratan</small>
    </div>

    <!-- Menu Navigation -->
    <div class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <span class="sidebar-icon">
                <i class="fas fa-chart-bar"></i>
            </span>
            <span class="sidebar-text">Dashboard</span>
        </a>
        
        <a href="{{ route('warga.index') }}" class="sidebar-item {{ request()->is('warga*') ? 'active' : '' }}">
            <span class="sidebar-icon">
                <i class="fas fa-users"></i>
            </span>
            <span class="sidebar-text">Data Warga</span>
        </a>
        
        <a href="{{ route('surat.index') }}" class="sidebar-item {{ request()->is('surat*') && !request()->is('surat/monitor') && !request()->is('surat/fields') ? 'active' : '' }}">
            <span class="sidebar-icon">
                <i class="fas fa-file-alt"></i>
            </span>
            <span class="sidebar-text">Buat Surat</span>
        </a>

        <!-- 🔥 MENU BARU: Laporan -->
        <a href="{{ route('laporan.index') }}" class="sidebar-item {{ request()->is('laporan*') ? 'active' : '' }}">
            <span class="sidebar-icon">
                <i class="fas fa-chart-pie"></i>
            </span>
            <span class="sidebar-text">Laporan</span>
        </a>

        <!-- 🔥 MENU BARU: Monitor Nomor Surat -->
        <a href="{{ route('surat.monitor') }}" class="sidebar-item {{ request()->is('surat/monitor') ? 'active' : '' }}">
            <span class="sidebar-icon">
                <i class="fas fa-list-ol"></i>
            </span>
            <span class="sidebar-text">Monitor Surat</span>
        </a>

        <!-- 🔥 MENU BARU: Available Fields -->
        <a href="{{ route('surat.fields') }}" class="sidebar-item {{ request()->is('surat/fields') ? 'active' : '' }}">
            <span class="sidebar-icon">
                <i class="fas fa-code"></i>
            </span>
            <span class="sidebar-text">Template Fields</span>
        </a>
    </div>

    <!-- User Info & Logout -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <small class="user-role badge bg-success">{{ auth()->user()->role ?? 'Petugas' }}</small>
            </div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt me-2"></i>
                Logout
            </button>
        </form>
    </div>
</div>