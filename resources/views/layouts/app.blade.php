<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kelurahan App')</title>

    {{-- ✅ CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- ✅ Chart.js (dipakai di dashboard & laporan) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* === NAVBAR IMPROVED === */
        .main-navbar {
            margin-left: 280px;
            background: white !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            padding: 12px 25px;
            border-bottom: 1px solid #e9ecef;
            z-index: 999;
        }

        .breadcrumb-nav .breadcrumb {
            margin-bottom: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-nav .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-nav .breadcrumb-item a:hover {
            color: #0d6efd;
        }

        .breadcrumb-nav .breadcrumb-item.active {
            color: #495057;
            font-weight: 600;
        }

        .user-greeting {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .user-name {
            font-size: 0.95rem;
        }

        .user-role-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .user-profile-btn {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 25px;
            padding: 6px 15px;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .user-profile-btn:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            border-color: #adb5bd;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .user-avatar-sm {
            font-size: 1.3rem;
            color: #0d6efd;
            margin-right: 8px;
        }

        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            top: -8px;
            left: -5px;
        }

        /* Dropdown Menu Styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 12px;
            padding: 8px;
            margin-top: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            margin: 2px 0;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            transform: translateX(5px);
        }

        .logout-btn {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }

        /* Quick Action Buttons */
        .btn-group .btn {
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 6px 12px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .main-navbar {
                margin-left: 0;
            }
            
            .breadcrumb-nav {
                display: none;
            }
            
            .user-greeting small {
                display: none;
            }
            
            .btn-group {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .main-navbar {
                padding: 10px 15px;
            }
            
            .user-role-badge,
            .user-greeting .user-name {
                display: none;
            }
            
            .user-profile-btn span {
                display: none;
            }
            
            .user-profile-btn {
                padding: 6px 10px;
            }
            
            .notification-badge {
                width: 16px;
                height: 16px;
                font-size: 0.6rem;
            }
        }

        /* Animation for dropdown */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu {
            animation: slideDown 0.3s ease;
        }

        /* Badge styling improvement */
        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Button hover effects */
        .btn-outline-primary:hover,
        .btn-outline-success:hover,
        .btn-outline-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        /* === SIDEBAR IMPROVED === */
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            position: fixed;
            width: 280px;
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
            text-align: center;
        }

        .sidebar-header h4 {
            margin: 0 0 8px 0;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-subtitle {
            opacity: 0.8;
            font-size: 0.85rem;
            display: block;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            margin: 3px 0;
            font-weight: 500;
        }

        .sidebar-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #ffd700;
            padding-left: 25px;
        }

        .sidebar-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #ff6b6b;
            font-weight: 600;
        }

        .sidebar-icon {
            font-size: 1.1rem;
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .sidebar-text {
            font-size: 0.95rem;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-avatar {
            font-size: 2rem;
            margin-right: 12px;
            color: rgba(255,255,255,0.8);
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .user-role {
            font-size: 0.75rem;
            padding: 3px 8px;
        }

        .logout-form {
            margin-top: 10px;
        }

        .btn-logout {
            width: 100%;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
            transform: translateY(-1px);
        }

        /* Scrollbar styling untuk sidebar */
        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        /* === NAVBAR === */
        .navbar {
            margin-left: 280px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 25px;
        }

        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
        }

        /* === CONTENT === */
        .content {
            margin-left: 280px;
            padding: 25px;
            min-height: calc(100vh - 70px);
            background-color: #f8f9fa;
        }

        /* === FOOTER === */
        footer {
            margin-left: 280px;
            background-color: white;
            text-align: center;
            padding: 15px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            font-size: 0.9rem;
        }

        /* === RESPONSIVE DESIGN === */
        @media (max-width: 992px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                margin-bottom: 0;
            }
            
            .navbar, .content, footer {
                margin-left: 0;
            }
            
            .sidebar-header {
                padding: 15px 20px;
            }
            
            .sidebar-menu {
                display: flex;
                flex-wrap: wrap;
                padding: 10px;
            }
            
            .sidebar-item {
                flex: 1;
                min-width: 120px;
                justify-content: center;
                text-align: center;
                flex-direction: column;
                padding: 10px 5px;
                margin: 2px;
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            
            .sidebar-item:hover,
            .sidebar-item.active {
                border-left: none;
                border-bottom-color: #ff6b6b;
                padding-left: 5px;
            }
            
            .sidebar-icon {
                margin-right: 0;
                margin-bottom: 5px;
                font-size: 1.3rem;
            }
            
            .sidebar-text {
                font-size: 0.8rem;
            }
            
            .sidebar-footer {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .sidebar-item {
                min-width: 80px;
            }
            
            .sidebar-text {
                font-size: 0.75rem;
            }
            
            .content {
                padding: 15px;
            }
        }

        /* Animation untuk sidebar items */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar-item {
            animation: slideIn 0.3s ease-out;
        }

        .sidebar-item:nth-child(1) { animation-delay: 0.1s; }
        .sidebar-item:nth-child(2) { animation-delay: 0.2s; }
        .sidebar-item:nth-child(3) { animation-delay: 0.3s; }
        .sidebar-item:nth-child(4) { animation-delay: 0.4s; }
        .sidebar-item:nth-child(5) { animation-delay: 0.5s; }
        .sidebar-item:nth-child(6) { animation-delay: 0.6s; }

        /* Card styling improvement */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        /* Button styling improvement */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>

    {{-- ✅ Bagian Layout Utama --}}
    @include('layouts.partials.sidebar')
    @include('layouts.partials.navbar')

    <main class="content">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    {{-- ✅ JS LIBRARY --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ✅ Global Helper JS --}}
    <script>
        /**
         * Menampilkan modal konfirmasi hapus data
         */
        function showDeleteModal(id, name) {
            const modalElement = document.getElementById('deleteModal');
            if (!modalElement) return alert('Modal tidak ditemukan.');

            document.getElementById('deleteNama').textContent = name;
            const form = document.getElementById('deleteForm');
            if (form) form.action = '/warga/' + id;

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

        /**
         * Menampilkan alert sederhana
         */
        function showAlert(type, message, isSuccess = false) {
            alert((isSuccess ? '✅ ' : '❌ ') + message);
        }
    </script>

    {{-- ✅ Section dinamis untuk halaman lain (chart, js tambahan, dsb) --}}
    @yield('scripts')

</body>
</html>
