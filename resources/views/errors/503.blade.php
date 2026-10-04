<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($isAdmin ?? false) ? 'Portal Admin Dalam Pemeliharaan — REVARO' : 'Sistem Dalam Pemeliharaan — REVARO' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Base Root Variables - Adapts based on Theme (Dark for Admin, Light for Regular User) */
        :root {
            --anim-duration: 2s;
        }

        /* ── THEME LIGHT (Regular User: Pelanggan, Teknisi, Toko, Guest) ── */
        body.theme-light {
            --bg-color: #f8fafc;
            --header-border: #e2e8f0;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
            --gear-color: #cbd5e1;
            --gear-stroke: #94a3b8;
            --wrench-color: #ffffff;
            --wrench-outline: #1e293b;
            --wrench-shadow: rgba(0, 0, 0, 0.15);
            --spark-color: #ea580c;
            --spark-glow: rgba(234, 88, 12, 0.45);
            --text-title: #0f172a;
            --text-subtitle: #475569;
            --text-muted: #64748b;
            --badge-bg: #fff7ed;
            --badge-border: #fed7aa;
            --badge-text: #c2410c;
            --eta-bg: #f8fafc;
            --eta-border: #e2e8f0;
            --eta-val: #ea580c;
            --btn-bg: #0b4a8f;
            --btn-hover: #083b74;
            --btn-text: #ffffff;
            --btn-sec-bg: #ffffff;
            --btn-sec-border: #cbd5e1;
            --btn-sec-text: #1e293b;
            --footer-text: #94a3b8;
        }

        /* ── THEME DARK (Admin: Admin Verifikator, Admin Resolusi, Admin Portal) ── */
        body.theme-dark {
            --bg-color: #0b0f17;
            --header-border: #1e293b;
            --card-bg: rgba(15, 23, 42, 0.75);
            --card-border: rgba(255, 255, 255, 0.08);
            --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            --gear-color: #cbd5e1;
            --gear-stroke: #64748b;
            --wrench-color: #f8fafc;
            --wrench-outline: #334155;
            --wrench-shadow: rgba(0, 0, 0, 0.6);
            --spark-color: #fca311;
            --spark-glow: rgba(252, 163, 17, 0.6);
            --text-title: #f8fafc;
            --text-subtitle: #cbd5e1;
            --text-muted: #64748b;
            --badge-bg: rgba(249, 115, 22, 0.15);
            --badge-border: rgba(249, 115, 22, 0.35);
            --badge-text: #fb923c;
            --eta-bg: rgba(15, 23, 42, 0.9);
            --eta-border: #1e293b;
            --eta-val: #fca311;
            --btn-bg: #ff6b00;
            --btn-hover: #e05e00;
            --btn-text: #ffffff;
            --btn-sec-bg: rgba(15, 23, 42, 0.8);
            --btn-sec-border: #334155;
            --btn-sec-text: #cbd5e1;
            --footer-text: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-subtitle);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
            position: relative;
            padding: 1.5rem;
            overflow-x: hidden;
        }

        /* Ambient Glow Blobs */
        .ambient-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
        }
        body.theme-light .ambient-glow-1 {
            width: 380px; height: 380px;
            background: rgba(255, 127, 0, 0.08);
            top: -100px; left: -100px;
        }
        body.theme-light .ambient-glow-2 {
            width: 450px; height: 450px;
            background: rgba(11, 74, 143, 0.08);
            bottom: -150px; right: -150px;
        }
        body.theme-dark .ambient-glow-1 {
            width: 380px; height: 380px;
            background: rgba(255, 107, 0, 0.12);
            top: -100px; left: -100px;
        }
        body.theme-dark .ambient-glow-2 {
            width: 450px; height: 450px;
            background: rgba(30, 58, 138, 0.18);
            bottom: -150px; right: -150px;
        }

        /* Header */
        header {
            max-width: 1080px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            position: relative;
            z-index: 10;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ff6b00 0%, #ea580c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #ffffff;
            font-size: 1.125rem;
            box-shadow: 0 8px 16px -4px rgba(234, 88, 12, 0.35);
        }
        .brand-info h1 {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--text-title);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .brand-info p {
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--badge-text);
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .header-link {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--header-border);
            background: var(--card-bg);
            transition: all 0.2s ease;
        }
        .header-link:hover {
            color: var(--text-title);
            border-color: var(--badge-text);
        }

        /* Main Center Card */
        main {
            max-width: 640px;
            margin: 2rem auto;
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 28px;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            z-index: 10;
            backdrop-filter: blur(16px);
        }

        /* ── ANIMATION WRAPPER (From User Template) ── */
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin-bottom: 2rem;
        }

        .animation-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .gear-svg {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            transform-origin: 50% 50%;
            animation: gearTurn var(--anim-duration) cubic-bezier(0.17, 0.89, 0.32, 1.28) infinite;
            filter: drop-shadow(0 6px 12px rgba(0,0,0,0.25));
        }

        @keyframes gearTurn {
            0%, 20% { transform: rotate(0deg); }
            50%, 100% { transform: rotate(45deg); }
        }

        .wrench-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            transform-origin: 50% 50%;
            animation: wrenchStroke var(--anim-duration) cubic-bezier(0.17, 0.89, 0.32, 1.28) infinite;
            z-index: 2;
        }

        .wrench-svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(2px 6px 8px var(--wrench-shadow));
        }

        @keyframes wrenchStroke {
            0% { transform: rotate(-15deg) translateY(-30px) scale(1.1); opacity: 0; }
            15% { transform: rotate(-30deg) translateY(0) scale(1); opacity: 1; }
            20% { transform: rotate(-30deg) translateY(0) scale(1); opacity: 1; }
            50% { transform: rotate(15deg) translateY(0) scale(1); opacity: 1; }
            55% { transform: rotate(15deg) translateY(0) scale(1); opacity: 1; }
            70% { transform: rotate(30deg) translateY(-30px) scale(1.1); opacity: 0; }
            100% { transform: rotate(-15deg) translateY(-30px) scale(1.1); opacity: 0; }
        }

        .spark {
            position: absolute;
            width: 6px;
            height: 6px;
            background-color: var(--spark-color);
            border-radius: 50%;
            opacity: 0;
            z-index: 3;
            box-shadow: 0 0 10px 2px var(--spark-glow);
            top: 87px;
            left: 87px;
        }

        .spark-1 { animation: sparkAnim1 var(--anim-duration) ease-out infinite; }
        .spark-2 { animation: sparkAnim2 var(--anim-duration) ease-out infinite; }
        .spark-3 { animation: sparkAnim3 var(--anim-duration) ease-out infinite; }

        @keyframes sparkAnim1 {
            0%, 20% { opacity: 0; transform: translate(0, 0) scale(0); }
            25% { opacity: 1; transform: translate(-20px, -30px) scale(1.5); }
            45%, 100% { opacity: 0; transform: translate(-40px, -50px) scale(0); }
        }

        @keyframes sparkAnim2 {
            0%, 25% { opacity: 0; transform: translate(0, 0) scale(0); }
            30% { opacity: 1; transform: translate(25px, -20px) scale(1.2); }
            50%, 100% { opacity: 0; transform: translate(45px, -30px) scale(0); }
        }

        @keyframes sparkAnim3 {
            0%, 15% { opacity: 0; transform: translate(0, 0) scale(0); }
            20% { opacity: 1; transform: translate(15px, 25px) scale(1.8); }
            40%, 100% { opacity: 0; transform: translate(30px, 45px) scale(0); }
        }

        .center-glow {
            position: absolute;
            width: 36px;
            height: 36px;
            background: radial-gradient(circle, var(--spark-glow) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            opacity: 0;
            animation: glowAnim var(--anim-duration) infinite;
        }

        @keyframes glowAnim {
            0%, 15% { opacity: 0; transform: scale(0.5); }
            35% { opacity: 1; transform: scale(1.5); }
            60%, 100% { opacity: 0; transform: scale(0.5); }
        }

        /* ── TYPOGRAPHY & MESSAGING ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            background: var(--badge-bg);
            border: 1px solid var(--badge-border);
            color: var(--badge-text);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.25rem;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            animation: pulseDot 1.5s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .headline {
            font-size: 1.85rem;
            font-weight: 900;
            color: var(--text-title);
            line-height: 1.25;
            letter-spacing: -0.03em;
            margin-bottom: 0.75rem;
        }
        .description {
            font-size: 0.875rem;
            line-height: 1.6;
            color: var(--text-subtitle);
            max-width: 520px;
            margin: 0 auto 1.75rem;
        }

        /* ETA Countdown Display */
        .eta-card {
            background: var(--eta-bg);
            border: 1px solid var(--eta-border);
            border-radius: 18px;
            padding: 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
        }
        .eta-label {
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .eta-time {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--eta-val);
            font-family: monospace;
            letter-spacing: -0.01em;
        }

        /* Action Buttons */
        .actions-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.75rem;
            border-radius: 14px;
            font-size: 0.8125rem;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            outline: none;
            border: none;
        }
        .btn-primary {
            background-color: var(--btn-bg);
            color: var(--btn-text);
            box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.3);
        }
        .btn-primary:hover {
            background-color: var(--btn-hover);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: var(--btn-sec-bg);
            color: var(--btn-sec-text);
            border: 1px solid var(--btn-sec-border);
        }
        .btn-secondary:hover {
            border-color: var(--badge-text);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            max-width: 1080px;
            margin: 0 auto;
            width: 100%;
            text-align: center;
            padding: 1.25rem 0;
            font-size: 0.75rem;
            color: var(--footer-text);
            position: relative;
            z-index: 10;
        }

        @media (max-width: 640px) {
            main {
                padding: 2.25rem 1.25rem;
            }
            .headline {
                font-size: 1.5rem;
            }
            .animation-wrapper {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>
<body id="maintenanceBody" class="{{ ($isAdmin ?? false) ? 'theme-dark' : 'theme-light' }}">

    <!-- Ambient Lighting Effects -->
    <div class="ambient-glow ambient-glow-1"></div>
    <div class="ambient-glow ambient-glow-2"></div>

    <!-- Header -->
    <header>
        <a href="/" class="brand">
            <div class="brand-logo">R</div>
            <div class="brand-info">
                <h1>REVARO</h1>
                <p id="portalSubHeader">{{ ($isAdmin ?? false) ? 'Portal Khusus Administrator' : 'Platform Servis Elektronik' }}</p>
            </div>
        </a>
        <a href="/login-admin" class="header-link">
            <span>Portal Super Admin</span>
        </a>
    </header>

    <!-- Main Content -->
    <main>
        <!-- The Animated SVG Loader (Gear + Wrench with Snap Animation) -->
        <div class="loader-container">
            <div class="animation-wrapper">
                <!-- Glow effect behind center -->
                <div class="center-glow"></div>

                <!-- Gear SVG -->
                <svg class="gear-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <mask id="gear-mask">
                            <rect width="200" height="200" fill="white"/>
                            <circle cx="100" cy="100" r="22" fill="black"/>
                        </mask>
                    </defs>
                    <g mask="url(#gear-mask)">
                        <circle cx="100" cy="100" r="48" fill="var(--gear-color)"/>
                        <rect x="86" y="38" width="28" height="124" rx="4" fill="var(--gear-color)"/>
                        <rect x="86" y="38" width="28" height="124" rx="4" fill="var(--gear-color)" transform="rotate(45 100 100)"/>
                        <rect x="86" y="38" width="28" height="124" rx="4" fill="var(--gear-color)" transform="rotate(90 100 100)"/>
                        <rect x="86" y="38" width="28" height="124" rx="4" fill="var(--gear-color)" transform="rotate(135 100 100)"/>
                    </g>
                    <circle cx="100" cy="100" r="28" fill="none" stroke="var(--gear-stroke)" stroke-width="2" opacity="0.5"/>
                    <circle cx="100" cy="100" r="22" fill="none" stroke="var(--gear-stroke)" stroke-width="3"/>
                </svg>

                <!-- Wrench Container & SVG -->
                <div class="wrench-container">
                    <svg class="wrench-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <path d="
                            M 118,87 
                            A 22,22 0 1,0 118,113 
                            L 170,116 
                            L 184,116 
                            A 8,8 0 0,0 192,108 
                            A 14,14 0 0,1 192,92 
                            A 8,8 0 0,0 184,84 
                            L 170,84 
                            Z" 
                            fill="var(--wrench-color)" 
                            stroke="var(--wrench-outline)" 
                            stroke-width="5" 
                            stroke-linejoin="round" />
                            
                        <polygon points="111,100 105.5,109.5 94.5,109.5 89,100 94.5,90.5 105.5,90.5" 
                                 fill="var(--bg-color)" 
                                 stroke="var(--wrench-outline)" 
                                 stroke-width="4" 
                                 stroke-linejoin="round"/>
                                 
                        <line x1="125" y1="100" x2="165" y2="100" stroke="var(--wrench-outline)" stroke-width="3" stroke-linecap="round" opacity="0.4"/>
                    </svg>
                </div>

                <!-- Sparks -->
                <div class="spark spark-1"></div>
                <div class="spark spark-2"></div>
                <div class="spark spark-3"></div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="status-badge">
            <span class="pulse-dot"></span>
            <span id="statusBadgeText">{{ ($isAdmin ?? false) ? 'PORTAL ADMINISTRATOR — MODE PEMELIHARAAN' : 'STATUS SISTEM — MODE PEMELIHARAAN' }}</span>
        </div>

        <!-- Headline & Description -->
        <h2 class="headline" id="headlineText">
            {{ ($isAdmin ?? false) ? 'Portal Admin Ditangguhkan Sementara' : 'Sistem Sedang Dalam Pemeliharaan' }}
        </h2>
        <p class="description" id="messageText">
            {{ $message ?? 'Platform REVARO sedang dalam pemeliharaan berkala untuk peningkatan stabilitas dan kualitas layanan. Silakan coba beberapa saat lagi.' }}
        </p>

        <!-- ETA Countdown Display (If available) -->
        @if(!empty($eta))
        <div class="eta-card">
            <span class="eta-label">Perkiraan Waktu Selesai (ETA)</span>
            <span class="eta-time" id="etaTimeDisplay">{{ \Carbon\Carbon::parse($eta)->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="actions-group">
            <button onclick="checkStatusAndReload()" class="btn btn-primary" id="btnReload">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span id="btnReloadText">Cek Status Kembali</span>
            </button>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        &copy; 2026 REVARO — Replikasi & Variasi Otomotif. Seluruh hak cipta dilindungi.
    </footer>

    <!-- Client-side script to enforce dark mode if admin is detected in localStorage -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.getElementById('maintenanceBody');
            const isAdminToken = localStorage.getItem('admin_token');
            const adminUserRaw = localStorage.getItem('admin_user');
            const isPathAdmin = window.location.pathname.startsWith('/admin') || window.location.pathname.startsWith('/superadmin');

            // If user was logged in as Admin or is on an admin route, switch to dark theme
            if (isAdminToken || adminUserRaw || isPathAdmin) {
                body.classList.remove('theme-light');
                body.classList.add('theme-dark');
                const subHeader = document.getElementById('portalSubHeader');
                if (subHeader) subHeader.innerText = 'Portal Khusus Administrator';
                const badgeText = document.getElementById('statusBadgeText');
                if (badgeText) badgeText.innerText = 'PORTAL ADMINISTRATOR — MODE PEMELIHARAAN';
                const headline = document.getElementById('headlineText');
                if (headline) headline.innerText = 'Portal Admin Ditangguhkan Sementara';
            }
        });

        async function checkStatusAndReload() {
            const btn = document.getElementById('btnReload');
            const btnText = document.getElementById('btnReloadText');
            btnText.innerText = 'Memeriksa Status...';
            btn.disabled = true;

            try {
                // Ping status endpoint
                const res = await fetch('/up', { cache: 'no-store' });
                // If ping succeeds and maintenance is lifted, reload page
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } catch(e) {
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            }
        }
    </script>
</body>
</html>
