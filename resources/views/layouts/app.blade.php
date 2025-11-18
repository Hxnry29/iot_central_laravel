<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Central - @yield('title')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ==== ESTILO MODERNO Y MINIMALISTA ==== -->
    <style>
        :root {
            --bg-dark: #0f0f11;
            --bg-sidebar: #0a0a0c;
            --bg-navbar: #161618;
            --bg-card: #18181b;

            --text-light: #e4e4e7;
            --text-muted: #9a9aa0;

            --primary-blue: #7db4ff;
            --primary-blue-dark: #4a8af7;

            --border-light: #2a2a2e;
        }

        * {
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light) !important;
        }

        /* ==== SIDEBAR ==== */
        .sidebar {
            width: 250px;
            background: var(--bg-sidebar);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            border-right: 1px solid var(--border-light);
            transition: all .3s ease;
        }

        .sidebar .brand {
            font-size: 1.4rem;
            padding: 1.3rem;
            font-weight: bold;
            color: var(--primary-blue);
            text-align: center;
            border-bottom: 1px solid var(--border-light);
        }

        .sidebar a {
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 14px 20px;
            font-size: 0.95rem;
            color: var(--text-light);
            transition: .2s;
        }

        .sidebar a i {
            margin-right: 12px;
            opacity: .85;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(125, 180, 255, .12);
            color: var(--primary-blue);
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        /* ==== CONTENT ==== */
        .content {
            margin-left: 250px;
            transition: all .3s ease;
            padding: 20px 32px;
        }

        .content.expanded {
            margin-left: 70px;
        }

        /* ==== NAVBAR ==== */
        .navbar {
            background: var(--bg-navbar) !important;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 12px 18px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }

        .navbar-brand {
            color: var(--primary-blue) !important;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .btn-toggle {
            color: var(--primary-blue) !important;
            border: none;
            font-size: 1.4rem;
            background: transparent;
        }

        /* ==== DROPDOWNS ==== */
        .dropdown-menu {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
        }

        .dropdown-item {
            color: var(--text-light);
        }

        .dropdown-item:hover {
            background: rgba(255,255,255,0.08);
        }

        /* ==== CARDS ==== */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            color: var(--text-light);
        }

        .card-footer {
            background: #131316 !important;
            border-top: 1px solid var(--border-light);
        }

        .card-title {
            color: var(--primary-blue) !important;
        }

        /* ==== TEXTOS ==== */
        h3, h4, h5, p, small, strong {
            color: var(--text-light) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* ==== BOTONES ==== */
        .btn-outline-primary {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }
        .btn-outline-primary:hover {
            background-color: rgba(125,180,255,0.15);
            color: var(--primary-blue);
        }

        .btn-outline-secondary {
            border-color: var(--text-muted);
            color: var(--text-light);
        }
        .btn-outline-secondary:hover {
            background-color: rgba(255,255,255,0.07);
        }

        .btn-outline-danger {
            border-color: #ff6b6b;
            color: #ff6b6b;
        }
        .btn-outline-danger:hover {
            background-color: rgba(255,107,107,0.15);
        }

        .btn-info {
            background-color: #006eff;
            border-color: #006eff;
        }

        .btn-info:hover {
            background-color: #1a7cff;
        }

        /* ==== RESPONSIVE ==== */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- ==== SIDEBAR ==== -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-hdd-network"></i> IoT Central
        </div>

        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span> Dashboard</span>
        </a>

        <a href="{{ route('devices.index') }}" class="{{ request()->is('devices*') ? 'active' : '' }}">
            <i class="bi bi-cpu"></i><span> Dispositivos</span>
        </a>

        
    </div>

    <!-- ==== CONTENT ==== -->
    <div class="content" id="main-content">

        <nav class="navbar navbar-expand-lg mb-4 px-3">
            <button class="btn-toggle me-3" id="toggle-btn">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand fw-bold" href="#">
                Panel de Control
            </a>

            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-dark border-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Ajustes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts --> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0"></script>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
        });
    </script>

    @stack('scripts')
</body>
</html>
