<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top main-navbar">
  <div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <i class="fas fa-home me-1"></i>Home
          </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          @yield('page-title', 'Dashboard')
        </li>
      </ol>
    </nav>

    <!-- Right Side Menu -->
    <div class="navbar-nav align-items-center">
      <!-- User Greeting -->
      <div class="user-greeting me-3">
        <small class="text-muted d-none d-md-block">Halo,</small>
        <span class="user-name fw-bold text-primary">
          {{ Auth::user()->name ?? 'Admin' }}
        </span>
      </div>

      <!-- User Role Badge -->
      <span class="badge user-role-badge bg-success me-3">
        <i class="fas fa-user-shield me-1"></i>
        {{ Auth::user()->role ?? 'Petugas' }}
      </span>

      <!-- Quick Actions -->
      <div class="btn-group me-2 d-none d-md-flex">
        <a href="{{ route('surat.index') }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-plus me-1"></i>Buat Surat
        </a>
        <a href="{{ route('warga.index') }}" class="btn btn-outline-success btn-sm">
          <i class="fas fa-users me-1"></i>Data Warga
        </a>
      </div>

      <!-- Notifications (Optional) -->
      <div class="dropdown me-2">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <i class="fas fa-bell"></i>
          <span class="notification-badge">3</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><h6 class="dropdown-header">Notifikasi</h6></li>
          <li><a class="dropdown-item" href="#"><i class="fas fa-file-text text-primary me-2"></i>5 surat baru hari ini</a></li>
          <li><a class="dropdown-item" href="#"><i class="fas fa-users text-success me-2"></i>2 warga baru</a></li>
          <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar text-info me-2"></i>Laporan bulanan siap</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-center" href="#">Lihat Semua</a></li>
        </ul>
      </div>

      <!-- User Profile & Logout -->
      <div class="dropdown">
        <button class="btn user-profile-btn" type="button" data-bs-toggle="dropdown">
          <div class="user-avatar-sm">
            <i class="fas fa-user-circle"></i>
          </div>
          <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
          <i class="fas fa-chevron-down ms-1 small"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <h6 class="dropdown-header">
              <div class="fw-bold">{{ Auth::user()->name ?? 'Admin' }}</div>
              <small class="text-muted">{{ Auth::user()->email ?? 'admin@kelurahan.id' }}</small>
            </h6>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item" href="{{ route('dashboard') }}">
              <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
            </a>
          </li>
          <li>
          <a class="dropdown-item" href="{{ route('settings.index') }}">
    <i class="fa fa-cog me-2"></i> Pengaturan
</a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="dropdown-item logout-btn">
                <i class="fas fa-sign-out-alt me-2 text-danger"></i>Logout
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>