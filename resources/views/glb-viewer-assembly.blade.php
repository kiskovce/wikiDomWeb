<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WikiDom – Konfigurátor domu</title>
    <script type="importmap">
    {
        "imports": {
            "three": "/libs/three.module.js",
            "three/addons/controls/OrbitControls.js": "/libs/OrbitControls.js",
            "three/addons/loaders/GLTFLoader.js": "/libs/GLTFLoader.js"
        }
    }
    </script>
    <style>
        :root {
            --bg: #f6f7f9;
            --surface: #ffffff;
            --surface-2: #f2f4f7;
            --stage: #dce8f0;
            --border: #e4e7ec;
            --text: #101828;
            --muted: #667085;
            --subtle: #98a2b3;
            --icon: #475467;
            --accent: #2f6b55;
            --accent-strong: #24564a;
            --accent-soft: #e7f1ec;
            --ring: rgba(47, 107, 85, 0.35);
            --danger: #b42318;
            --danger-soft: #fef3f2;
            --glass: #cfe3f3;
            --radius: 14px;
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.06);
            --shadow: 0 1px 3px rgba(16, 24, 40, 0.08), 0 4px 12px rgba(16, 24, 40, 0.06);
            --shadow-lg: 0 10px 28px rgba(16, 24, 40, 0.12);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            color: var(--text);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }
        button { font: inherit; color: inherit; }
        button:focus-visible { outline: 3px solid var(--ring); outline-offset: 2px; }

        .app {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 420px;
            height: 100vh;
            height: 100dvh;
        }

        .stage { position: relative; min-width: 0; background: var(--stage); overflow: hidden; }
        #canvas, #canvas2d { display: block; width: 100%; height: 100%; }
        #canvas2d { display: none; cursor: grab; }
        #canvas2d:active { cursor: grabbing; }
        .stage.is-software #canvas { display: none; }
        .stage.is-software #canvas2d { display: block; }
        .stage-top {
            position: absolute;
            top: 16px;
            left: 16px;
            right: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            pointer-events: none;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 999px;
            box-shadow: var(--shadow);
            white-space: nowrap;
        }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); }
        .chip-busy { opacity: 0; transform: translateY(-4px); transition: opacity 0.2s, transform 0.2s; color: var(--muted); }
        .chip-busy.is-visible { opacity: 1; transform: none; }
        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        .spinner-lg { width: 32px; height: 32px; border-width: 3px; }
        .notice {
            position: absolute;
            top: 68px;
            left: 50%;
            max-width: calc(100% - 32px);
            padding: 10px 16px;
            font-size: 14px;
            color: var(--danger);
            background: var(--surface);
            border: 1px solid #fecdca;
            border-radius: 12px;
            box-shadow: var(--shadow);
            opacity: 0;
            transform: translate(-50%, -8px);
            transition: opacity 0.2s, transform 0.2s;
            pointer-events: none;
        }
        .notice.is-visible { opacity: 1; transform: translate(-50%, 0); }
        .hint {
            position: absolute;
            bottom: 20px;
            left: 16px;
            right: 16px;
            width: fit-content;
            max-width: calc(100% - 32px);
            margin: 0 auto;
            font-weight: 500;
            color: var(--muted);
            text-align: center;
            white-space: normal;
            pointer-events: none;
            transition: opacity 0.4s;
        }
        .hint.hidden { opacity: 0; }
        .hint svg, .icon {
            flex: none;
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .hint-touch { display: none; }
        .empty-state {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 24px;
            font-size: 15px;
            color: var(--muted);
            text-align: center;
            background: var(--stage);
        }
        .empty-state.hidden { display: none; }
        .empty-state.is-error .spinner { display: none; }
        .empty-state.is-error p { color: var(--danger); font-weight: 500; }
        .empty-state.is-degraded .spinner { display: none; }

        .panel {
            display: flex;
            flex-direction: column;
            min-height: 0;
            background: var(--surface);
            border-left: 1px solid var(--border);
        }
        .panel-head { padding: 28px 28px 4px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 18px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
        }
        .back-link:hover { color: var(--text); }
        .eyebrow {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent);
        }
        .panel-head h1 { margin: 4px 0 6px; font-size: 26px; line-height: 1.2; font-weight: 700; letter-spacing: -0.02em; }
        .lead { font-size: 14px; color: var(--muted); }
        .panel-body { flex: 1; min-height: 0; overflow-y: auto; padding: 0 28px 24px; }
        .step { padding-top: 24px; }
        .step + .step { margin-top: 24px; border-top: 1px solid var(--border); }
        .step-head { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
        .step-num {
            display: grid;
            place-items: center;
            flex: none;
            width: 28px;
            height: 28px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            background: var(--accent);
            border-radius: 50%;
        }
        .step-head h2 { font-size: 16px; line-height: 28px; font-weight: 600; }
        .step-head p { font-size: 13px; color: var(--muted); }
        .muted { font-size: 14px; color: var(--muted); }

        .art { display: grid; place-items: center; color: var(--icon); }
        .art svg { display: block; width: 100%; height: auto; overflow: visible; }
        .art .wall { fill: #fff; stroke: currentColor; stroke-width: 2; stroke-linejoin: round; }
        .art .glass { fill: var(--glass); stroke: currentColor; stroke-width: 2; }
        .art .frame { stroke: currentColor; stroke-width: 2; }
        .art .seam { stroke: currentColor; stroke-width: 1.5; stroke-dasharray: 3 3; opacity: 0.35; }

        .catalog { display: grid; grid-template-columns: minmax(0, 1fr); gap: 10px; }
        .card {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 8px 12px 8px 8px;
            text-align: left;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
        }
        .card:hover { border-color: var(--accent); box-shadow: var(--shadow-lg); transform: translateY(-1px); }
        .card:active { transform: translateY(0); }
        .card-art {
            flex: none;
            width: 56px;
            aspect-ratio: 4 / 3;
            background: var(--surface-2);
            border-radius: 10px;
            transition: color 0.15s, background 0.15s;
        }
        .card-art svg { width: 76%; }
        .card:hover .card-art { color: var(--accent); background: var(--accent-soft); }
        .card-body { display: flex; flex-direction: column; flex: 1; min-width: 0; }
        .card-title { font-size: 14px; font-weight: 600; line-height: 1.3; }
        .card-desc { font-size: 12px; line-height: 1.35; color: var(--muted); }
        .card-side { display: flex; align-items: center; gap: 10px; flex: none; }
        .card-price { font-size: 13px; font-weight: 600; white-space: nowrap; }
        .card-add {
            display: grid;
            place-items: center;
            width: 30px;
            height: 30px;
            font-size: 17px;
            font-weight: 600;
            line-height: 1;
            color: var(--accent);
            background: var(--accent-soft);
            border-radius: 50%;
            transition: background 0.15s, color 0.15s;
        }
        .card:hover .card-add { color: #fff; background: var(--accent); }
        .add-done { display: none; }
        .card.is-added { border-color: var(--accent); animation: pop 0.35s ease; }
        .card.is-added .card-add { color: #fff; background: var(--accent); }
        .card.is-added .add-idle { display: none; }
        .card.is-added .add-done { display: inline; }
        .card.is-skeleton {
            min-height: 76px;
            border-color: transparent;
            box-shadow: none;
            cursor: default;
            background: linear-gradient(90deg, var(--surface-2) 25%, #e9ecf1 50%, var(--surface-2) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s linear infinite;
        }

        .sequence { display: flex; flex-direction: column; gap: 8px; list-style: none; }
        .seq-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
        }
        .seq-item.is-locked { background: var(--surface-2); border-style: dashed; }
        .seq-item.is-new { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); animation: slide-in 0.35s ease; }
        .seq-num {
            display: grid;
            place-items: center;
            flex: none;
            width: 24px;
            height: 24px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            background: var(--surface-2);
            border-radius: 50%;
        }
        .is-locked .seq-num { background: var(--surface); }
        .seq-art { flex: none; width: 40px; }
        .seq-text { display: flex; flex-direction: column; flex: 1; min-width: 0; }
        .seq-text strong { font-size: 14px; font-weight: 600; }
        .seq-text small { font-size: 12px; color: var(--muted); }
        .seq-remove, .seq-lock {
            display: grid;
            place-items: center;
            flex: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }
        .seq-remove {
            color: var(--muted);
            background: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }
        .seq-remove:hover { color: var(--danger); background: var(--danger-soft); border-color: #fecdca; }
        .seq-lock { color: var(--subtle); }
        .seq-empty {
            padding: 14px;
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            border: 1px dashed var(--border);
            border-radius: 12px;
        }

        .summary {
            padding: 18px 28px 22px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            box-shadow: 0 -8px 24px rgba(16, 24, 40, 0.04);
        }
        .summary-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .summary-stats dt {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--subtle);
        }
        .summary-stats dd { font-size: 16px; font-weight: 600; }
        .summary-price {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }
        .summary-price span { font-size: 14px; color: var(--muted); }
        .summary-price strong { font-size: 28px; font-weight: 700; letter-spacing: -0.02em; }
        .link-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 4px 0;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
        }
        .link-btn:hover { color: var(--text); text-decoration: underline; }

        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes shimmer { to { background-position: -200% 0; } }
        @keyframes pop { 50% { transform: scale(0.97); } }
        @keyframes slide-in { from { opacity: 0; transform: translateY(-6px); } }

        @media (max-width: 1200px) {
            .app { grid-template-columns: minmax(0, 1fr) 370px; }
            .panel-head { padding: 24px 22px 4px; }
            .panel-body { padding: 0 22px 22px; }
            .summary { padding: 16px 22px 20px; }
        }
        @media (max-width: 900px) {
            html, body { height: auto; }
            body { overflow: auto; }
            .app { display: block; height: auto; }
            .stage { height: 55vh; height: 55svh; min-height: 300px; }
            .panel { border-left: none; border-top: 1px solid var(--border); }
            .panel-body { overflow: visible; }
            .summary { position: sticky; bottom: 0; z-index: 5; padding: 12px 20px 14px; }
            .summary-stats dd { font-size: 15px; }
            .summary-price { margin-top: 10px; padding-top: 10px; }
            .summary-price strong { font-size: 22px; }
            .link-btn { margin-top: 4px; }
        }
        @media (hover: none) and (pointer: coarse) {
            .hint-mouse { display: none; }
            .hint-touch { display: inline; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
    <div class="app">
        <main class="stage" id="viewerContent">
            <canvas id="canvas" aria-label="3D náhľad domu"></canvas>
            <canvas id="canvas2d" aria-label="3D náhľad domu (softvérové vykresľovanie)"></canvas>
            <div class="stage-top">
                <div class="chip"><span class="dot"></span><span id="modelName">Váš dom</span></div>
                <div class="chip chip-busy" id="busy" role="status"><span class="spinner"></span>Skladám dom…</div>
            </div>
            <div class="notice" id="notice" role="alert"></div>
            <div class="chip hint" id="hint">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></svg>
                <span class="hint-mouse">Ťahaním myšou otáčate · kolieskom približujete · pravým tlačidlom posúvate</span>
                <span class="hint-touch">Potiahnutím otáčate · dvoma prstami približujete</span>
            </div>
            <div class="empty-state" id="emptyState">
                <span class="spinner spinner-lg"></span>
                <p id="emptyText">Načítavam dom…</p>
            </div>
        </main>
        <aside class="panel">
            <header class="panel-head">
                <a class="back-link" href="/">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                    Späť na úvod
                </a>
                <p class="eyebrow">WikiDom</p>
                <h1>Navrhnite si svoj dom</h1>
                <p class="lead">Pridajte segmenty a dom sa s každým z nich predĺži o 1,2 m. Zmeny hneď uvidíte v 3D.</p>
            </header>
            <div class="panel-body">
                <section class="step" aria-labelledby="step1">
                    <div class="step-head">
                        <span class="step-num">1</span>
                        <div>
                            <h2 id="step1">Pridajte segmenty</h2>
                            <p>Kliknite na typ steny. Môžete ho pridať viackrát.</p>
                        </div>
                    </div>
                    <div class="catalog" id="objectsList">
                        <div class="card is-skeleton"></div>
                        <div class="card is-skeleton"></div>
                        <div class="card is-skeleton"></div>
                        <div class="card is-skeleton"></div>
                    </div>
                </section>
                <section class="step" aria-labelledby="step2">
                    <div class="step-head">
                        <span class="step-num">2</span>
                        <div>
                            <h2 id="step2">Vaša skladba</h2>
                            <p>Poradie od prednej po zadnú čelnú stenu. Čelné steny sú vždy súčasťou domu.</p>
                        </div>
                    </div>
                    <ol class="sequence" id="sequenceList"></ol>
                </section>
            </div>
            <footer class="summary" aria-live="polite">
                <dl class="summary-stats">
                    <div><dt>Dĺžka</dt><dd id="sumLength">–</dd></div>
                    <div><dt>Plocha</dt><dd id="sumArea">–</dd></div>
                    <div><dt>Segmenty</dt><dd id="sumCount">–</dd></div>
                </dl>
                <div class="summary-price">
                    <span>Orientačná cena</span>
                    <strong id="sumPrice">–</strong>
                </div>
                <button type="button" class="link-btn" id="clearAssembly">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                    Začať odznova
                </button>
            </footer>
        </aside>
    </div>
    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

        const SEGMENT_WIDTH = 1.2;
        // Šírka domu podľa bbox endwall (X -0,04 až 4,76 m).
        const HOUSE_WIDTH = 4.8;
        const PRICING = { endwall: 3000, segment: 2000 };
        const CATALOG = {
            'default': { label: 'Plná stena', desc: 'Bez okna, viac súkromia', icon: 'plain', order: 1 },
            'small_window': { label: 'Malé okno', desc: 'Vhodné do kúpeľne a WC', icon: 'small', order: 2 },
            'narrow window': { label: 'Úzke okno', desc: 'Zvislý pás svetla', icon: 'narrow', order: 3 },
            'window': { label: 'Štandardné okno', desc: 'Vyvážené svetlo do izby', icon: 'standard', order: 4 },
            'big window': { label: 'Veľké okno', desc: 'Výhľad a maximum svetla', icon: 'big', order: 5 },
            'endwall_1': { label: 'Čelná stena', icon: 'end' },
            'endwall_2': { label: 'Čelná stena', icon: 'end' }
        };
        const ICON_LOCK = '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>';
        const ICON_CLOSE = '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';
        const money = new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });
        const decimal = new Intl.NumberFormat('sk-SK', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        const models = [];
        const sequence = [];
        let endwallStart = null;
        let endwallEnd = null;
        let hasFramed = false;
        let lastAdded = -1;
        let noticeTimer = 0;
        const groundCenter = new THREE.Vector2(0, 0);
        const emptyState = document.getElementById('emptyState');
        const emptyText = document.getElementById('emptyText');
        const busyEl = document.getElementById('busy');
        const noticeEl = document.getElementById('notice');
        const hintEl = document.getElementById('hint');
        const viewer = document.getElementById('viewerContent');
        const canvas = document.getElementById('canvas');
        const canvas2d = document.getElementById('canvas2d');
        const catalogEl = document.getElementById('objectsList');
        const sequenceList = document.getElementById('sequenceList');

        function setBusy(isBusy) {
            busyEl.classList.toggle('is-visible', isBusy);
        }

        function showFatal(message) {
            setBusy(false);
            emptyText.textContent = message;
            emptyState.classList.remove('is-degraded');
            emptyState.classList.add('is-error');
            emptyState.classList.remove('hidden');
        }

        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0xdce8f0);
        const camera = new THREE.PerspectiveCamera(42, 1, 0.01, 1000);
        camera.up.set(0, 0, 1);
        camera.position.set(-8, 6, 4);
        const assembly = new THREE.Group();
        scene.add(assembly);
        const shadow = new THREE.Mesh(
            new THREE.CircleGeometry(1, 48),
            new THREE.MeshBasicMaterial({ color: 0x7f9484, transparent: true, opacity: 0.28, depthWrite: false, side: THREE.DoubleSide })
        );
        shadow.material.userData.role = 'shadow';
        shadow.position.z = 0.012;
        shadow.renderOrder = 1;
        scene.add(shadow);
        const loader = new GLTFLoader();
        const templateCache = new Map();

        // Nastavenie rendereru môže zlyhať, ak prehliadač/zariadenie nepodporuje WebGL.
        let renderer, controls;
        let webglAvailable = true;
        try {
            renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
            renderer.outputColorSpace = THREE.SRGBColorSpace;

            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.05;
            scene.add(new THREE.HemisphereLight(0xd7e8f6, 0xc5d4bc, 0.72));
            const keyLight = new THREE.DirectionalLight(0xfff6ec, 1.25);
            keyLight.position.set(-7, -4, 9);
            scene.add(keyLight);
            const fillLight = new THREE.DirectionalLight(0xc5ddf2, 0.38);
            fillLight.position.set(6, 5, 4);
            scene.add(fillLight);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.minPolarAngle = 0.18;
            controls.maxPolarAngle = Math.PI / 2 - 0.04;
        } catch (error) {
            console.error(error);
            webglAvailable = false;
            viewer.classList.add('is-software');
            controls = new OrbitControls(camera, canvas2d);
            controls.enableDamping = false;
            controls.minPolarAngle = 0.15;
            controls.maxPolarAngle = Math.PI / 2 - 0.02;
            controls.addEventListener('change', renderSoftware);
        }

        function resize() {
            const width = viewer.clientWidth || 1;
            const height = viewer.clientHeight || 1;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            if (webglAvailable) {
                renderer.setSize(width, height, false);
            } else {
                const dpr = Math.min(window.devicePixelRatio || 1, 1.25);
                canvas2d.width = Math.round(width * dpr);
                canvas2d.height = Math.round(height * dpr);
                renderSoftware();
            }
        }

        function frameObject(object) {
            const box = new THREE.Box3().setFromObject(object);
            if (box.isEmpty()) return;
            const center = box.getCenter(new THREE.Vector3());
            controls.target.copy(center);
            camera.up.set(0, 0, 1);
            // 3/4 pohľad: čelný štít (+Y) aj okenná stena (−X), nie kolmý bočný rez.
            camera.position.set(center.x - 1.35, center.y + 1.05, center.z + 0.72);
            fitObjectKeepingAngle(object);
        }

        function fitObjectKeepingAngle(object) {
            const box = new THREE.Box3().setFromObject(object);
            if (box.isEmpty()) return;
            const center = box.getCenter(new THREE.Vector3());
            const direction = camera.position.clone().sub(controls.target);
            if (direction.lengthSq() < 1e-8) direction.set(-1.2, 0, 0.28);
            direction.normalize();

            const up = camera.up.clone().normalize();
            const right = new THREE.Vector3().crossVectors(direction, up);
            if (right.lengthSq() < 1e-8) right.set(1, 0, 0);
            right.normalize();
            const cameraUp = new THREE.Vector3().crossVectors(right, direction).normalize();
            const halfFovV = (camera.fov * Math.PI) / 360;
            const tanV = Math.tan(halfFovV);
            const tanH = Math.tan(halfFovV) * Math.max(camera.aspect, 0.1);
            const offset = new THREE.Vector3();
            let distance = 0.5;
            const corners = [
                [box.min.x, box.min.y, box.min.z],
                [box.min.x, box.min.y, box.max.z],
                [box.min.x, box.max.y, box.min.z],
                [box.min.x, box.max.y, box.max.z],
                [box.max.x, box.min.y, box.min.z],
                [box.max.x, box.min.y, box.max.z],
                [box.max.x, box.max.y, box.min.z],
                [box.max.x, box.max.y, box.max.z]
            ];
            corners.forEach(([x, y, z]) => {
                offset.set(x, y, z).sub(center);
                const towardCamera = offset.dot(direction);
                distance = Math.max(
                    distance,
                    towardCamera + Math.abs(offset.dot(right)) / tanH,
                    towardCamera + Math.abs(offset.dot(cameraUp)) / tanV
                );
            });4
            distance *= 1.12;
            controls.target.copy(center);
            camera.position.copy(center).addScaledVector(direction, distance);
            camera.near = Math.max(distance / 100, 0.05);
            camera.far = Math.max(distance * 8, 40);
            camera.updateProjectionMatrix();
            controls.update();
        }

        const FALLBACK_COLOR = new THREE.Color(0xd8dee3);
        const KEY_LIGHT = new THREE.Vector3(-0.55, -0.42, 0.72).normalize();
        const FILL_LIGHT = new THREE.Vector3(0.62, 0.28, 0.42).normalize();
        const ROLE_MATS = new Map();
        const tmpBox = new THREE.Box3();
        const tmpSize = new THREE.Vector3();
        const tmpCenter = new THREE.Vector3();
        const tmpNormal = new THREE.Vector3();
        let softFrame = null;

        function roleMaterial(role) {
            if (ROLE_MATS.has(role)) return ROLE_MATS.get(role);
            const spec = {
                plaster: { color: 0xf3efe6, roughness: 0.86 },
                plasterShade: { color: 0xe4dbd0, roughness: 0.9 },
                gable: { color: 0xf7f4ee, roughness: 0.84 },
                roof: { color: 0x5c6a72, roughness: 0.7 },
                roofUnder: { color: 0x3f4a51, roughness: 0.92 },
                wood: { color: 0xc4a36e, roughness: 0.76 },
                trim: { color: 0x8a8176, roughness: 0.62 },
                grass: { color: 0xd7e3cf, roughness: 1 },
                glass: { color: 0x8ec4de, roughness: 0.08, metalness: 0.04, transparent: true, opacity: 0.72, depthWrite: false }
            }[role] || { color: 0xf3efe6, roughness: 0.86 };
            const mat = new THREE.MeshStandardMaterial({
                color: spec.color,
                roughness: spec.roughness,
                metalness: spec.metalness || 0,
                side: THREE.DoubleSide,
                transparent: !!spec.transparent,
                opacity: spec.opacity ?? 1,
                depthWrite: spec.depthWrite !== false
            });
            mat.userData.role = role;
            ROLE_MATS.set(role, mat);
            return mat;
        }

        function faceRole(normal, box, isGround) {
            if (isGround) return 'grass';
            const center = box.getCenter(tmpCenter);
            const size = box.getSize(tmpSize);
            const ax = Math.abs(normal.x);
            const ay = Math.abs(normal.y);
            const az = Math.abs(normal.z);
            if (az > 0.4 && ax > 0.2 && center.z > 2.2) return normal.z > 0 ? 'roof' : 'roofUnder';
            if (az > 0.85 && center.z > 0.05 && center.z < 0.8) return 'wood';
            if (az > 0.85) return 'plasterShade';
            // Ostenie okna: tenký výrez v hrúbke steny, nie celá fasáda.
            if (center.x < 0.45 && size.x > 0.12 && size.x < 0.45 && size.y < 1.15 && size.z < 2.55) return 'trim';
            if (ax > 0.75 && (center.x < 0.2 || center.x > 4.55)) return 'plaster';
            if (ax > 0.75) return 'plasterShade';
            if (ay > 0.75 && size.x > 3.5) return 'gable';
            if (ay > 0.75) return 'plasterShade';
            return 'plaster';
        }

        function stylizeTemplate(root, isGround) {
            root.updateMatrixWorld(true);
            const reveals = new THREE.Box3();
            let hasReveal = false;
            root.traverse((obj) => {
                if (!obj.isMesh || !obj.geometry || obj.userData.glass) return;
                const geom = obj.geometry;
                if (!geom.boundingBox) geom.computeBoundingBox();
                const normalAttr = geom.attributes.normal;
                if (normalAttr) tmpNormal.fromBufferAttribute(normalAttr, 0);
                else tmpNormal.set(0, 0, 1);
                tmpNormal.transformDirection(obj.matrixWorld).normalize();
                tmpBox.copy(geom.boundingBox).applyMatrix4(obj.matrixWorld);
                const role = faceRole(tmpNormal, tmpBox, isGround);
                obj.material = roleMaterial(role);
                if (role === 'trim') {
                    if (!hasReveal) {
                        reveals.copy(tmpBox);
                        hasReveal = true;
                    } else {
                        reveals.union(tmpBox);
                    }
                }
            });
            if (!hasReveal || isGround) return;
            const size = reveals.getSize(tmpSize);
            if (size.y < 0.25 || size.z < 0.4) return;
            const x = reveals.min.x - 0.01;
            const y0 = reveals.min.y;
            const y1 = reveals.max.y;
            const z0 = reveals.min.z;
            const z1 = reveals.max.z;
            const glassGeo = new THREE.BufferGeometry();
            glassGeo.setAttribute('position', new THREE.Float32BufferAttribute([
                x, y0, z0, x, y1, z0, x, y1, z1,
                x, y0, z0, x, y1, z1, x, y0, z1
            ], 3));
            glassGeo.computeVertexNormals();
            const glass = new THREE.Mesh(glassGeo, roleMaterial('glass'));
            glass.userData.glass = true;
            glass.renderOrder = 2;
            root.add(glass);
        }

        function srgbByte(channel) {
            const c = Math.min(1, Math.max(0, channel));
            const encoded = c <= 0.0031308 ? c * 12.92 : 1.055 * Math.pow(c, 1 / 2.4) - 0.055;
            return Math.round(encoded * 255);
        }

        function shadeBytes(color, normal, role) {
            const key = Math.max(0, normal.dot(KEY_LIGHT));
            const fill = Math.max(0, normal.dot(FILL_LIGHT));
            const sky = normal.z * 0.5 + 0.5;
            let light = 0.42 + 0.46 * key + 0.14 * fill + 0.08 * sky;
            if (role === 'roof' || role === 'roofUnder') light *= 0.94;
            if (role === 'trim') light *= 0.8;
            if (role === 'grass') light = 0.72 + 0.2 * sky;
            if (role === 'shadow') light = 1;
            if (role === 'glass') light = 0.85 + 0.25 * sky;
            return [srgbByte(color.r * light), srgbByte(color.g * light), srgbByte(color.b * light)];
        }

        function projectToScreen(scratch, world, w, h) {
            scratch.copy(world).applyMatrix4(camera.matrixWorldInverse);
            const viewDepth = -scratch.z;
            scratch.applyMatrix4(camera.projectionMatrix);
            return {
                x: (scratch.x * 0.5 + 0.5) * w,
                y: (1 - (scratch.y * 0.5 + 0.5)) * h,
                z: viewDepth > 1e-4 ? 1 / viewDepth : 0,
                ok: viewDepth > 0.02 && Number.isFinite(scratch.x) && Number.isFinite(scratch.y)
            };
        }

        function rasterTriangle(frame, p0, p1, p2, rgb, alpha, writeDepth) {
            if (!p0.ok || !p1.ok || !p2.ok) return;
            if (p0.z > 1 && p1.z > 1 && p2.z > 1) return;
            let x0 = p0.x, y0 = p0.y, z0 = p0.z;
            let x1 = p1.x, y1 = p1.y, z1 = p1.z;
            let x2 = p2.x, y2 = p2.y, z2 = p2.z;
            let area = (x1 - x0) * (y2 - y0) - (x2 - x0) * (y1 - y0);
            if (area < 0) {
                let sx = x1, sy = y1, sz = z1;
                x1 = x2; y1 = y2; z1 = z2;
                x2 = sx; y2 = sy; z2 = sz;
                area = -area;
            }
            if (area < 0.4) return;
            const w = frame.w;
            const h = frame.h;
            let minX = Math.max(0, Math.floor(Math.min(x0, x1, x2)));
            let maxX = Math.min(w - 1, Math.ceil(Math.max(x0, x1, x2)));
            let minY = Math.max(0, Math.floor(Math.min(y0, y1, y2)));
            let maxY = Math.min(h - 1, Math.ceil(Math.max(y0, y1, y2)));
            if (maxX - minX > w * 4 || maxY - minY > h * 4) return;
            const inv = 1 / area;
            const data = frame.image.data;
            const depth = frame.depth;
            const r = rgb[0], g = rgb[1], b = rgb[2];
            for (let y = minY; y <= maxY; y++) {
                const row = y * w;
                for (let x = minX; x <= maxX; x++) {
                    const px = x + 0.5;
                    const py = y + 0.5;
                    const w0 = ((x1 - px) * (y2 - py) - (x2 - px) * (y1 - py)) * inv;
                    if (w0 < 0) continue;
                    const w1 = ((x2 - px) * (y0 - py) - (x0 - px) * (y2 - py)) * inv;
                    if (w1 < 0) continue;
                    const w2 = 1 - w0 - w1;
                    if (w2 < 0) continue;
                    const z = w0 * z0 + w1 * z1 + w2 * z2;
                    const idx = row + x;
                    // z je 1/vzdialenosť od kamery — väčšia hodnota je bližšie.
                    if (z <= depth[idx]) continue;
                    const p = idx * 4;
                    if (writeDepth) depth[idx] = z;
                    if (alpha >= 250) {
                        data[p] = r;
                        data[p + 1] = g;
                        data[p + 2] = b;
                        data[p + 3] = 255;
                    } else {
                        const t = alpha / 255;
                        data[p] = r * t + data[p] * (1 - t);
                        data[p + 1] = g * t + data[p + 1] * (1 - t);
                        data[p + 2] = b * t + data[p + 2] * (1 - t);
                        data[p + 3] = 255;
                    }
                }
            }
        }

        // Softvérový rasterizer skutočnej geometrie (bez WebGL): z-buffer, tiene a hrany.
        function renderSoftware() {
            const w = canvas2d.width;
            const h = canvas2d.height;
            if (!w || !h) return;
            const ctx = canvas2d.getContext('2d');
            if (!softFrame || softFrame.w !== w || softFrame.h !== h) {
                softFrame = { w, h, depth: new Float32Array(w * h), image: ctx.createImageData(w, h) };
            }
            const frame = softFrame;
            frame.depth.fill(0);
            const data = frame.image.data;
            for (let y = 0; y < h; y++) {
                const t = y / h;
                const r = 214 + (228 - 214) * t;
                const g = 228 + (236 - 228) * t;
                const b = 240 + (232 - 240) * t;
                const row = y * w * 4;
                for (let x = 0; x < w; x++) {
                    const p = row + x * 4;
                    data[p] = r;
                    data[p + 1] = g;
                    data[p + 2] = b;
                    data[p + 3] = 255;
                }
            camera.matrixWorldInverse.copy(camera.matrixWorld).invert();
            }
            scene.updateMatrixWorld(true);
            camera.updateMatrixWorld(true);
            const camPos = camera.position;
            const opaque = [];
            const transparent = [];
            const edges = new Map();
            const a = new THREE.Vector3();
            const b = new THREE.Vector3();
            const c = new THREE.Vector3();
            const pa = new THREE.Vector3();
            const pb = new THREE.Vector3();
            const pc = new THREE.Vector3();
            const ab = new THREE.Vector3();
            const ac = new THREE.Vector3();
            scene.traverse((obj) => {
                if (!obj.isMesh || !obj.geometry || !obj.visible) return;
                const posAttr = obj.geometry.attributes.position;
                if (!posAttr) return;
                const index = obj.geometry.index;
                const mat = Array.isArray(obj.material) ? obj.material[0] : obj.material;
                const color = (mat && mat.color) ? mat.color : FALLBACK_COLOR;
                const role = (mat && mat.userData && mat.userData.role) || '';
                const side = mat ? mat.side : THREE.FrontSide;
                const opacity = mat && mat.transparent ? (mat.opacity ?? 1) : 1;
                const count = index ? index.count : posAttr.count;
                const bucket = opacity < 0.98 ? transparent : opaque;
                for (let i = 0; i < count; i += 3) {
                    const ia = index ? index.getX(i) : i;
                    const ib = index ? index.getX(i + 1) : i + 1;
                    const ic = index ? index.getX(i + 2) : i + 2;
                    a.fromBufferAttribute(posAttr, ia).applyMatrix4(obj.matrixWorld);
                    b.fromBufferAttribute(posAttr, ib).applyMatrix4(obj.matrixWorld);
                    c.fromBufferAttribute(posAttr, ic).applyMatrix4(obj.matrixWorld);
                    ab.subVectors(b, a);
                    ac.subVectors(c, a);
                    const normal = new THREE.Vector3().crossVectors(ab, ac);
                    if (normal.lengthSq() < 1e-12) continue;
                    normal.normalize();
                    const cx = (a.x + b.x + c.x) / 3;
                    const cy = (a.y + b.y + c.y) / 3;
                    const cz = (a.z + b.z + c.z) / 3;
                    const facing = normal.x * (camPos.x - cx) + normal.y * (camPos.y - cy) + normal.z * (camPos.z - cz);
                    if (side === THREE.FrontSide && facing <= 0) continue;
                    if (side === THREE.BackSide && facing > 0) continue;
                    if (facing < 0) normal.negate();
                    const sa = projectToScreen(pa, a, w, h);
                    const sb = projectToScreen(pb, b, w, h);
                    const sc = projectToScreen(pc, c, w, h);
                    const rgb = shadeBytes(color, normal, role);
                    const depth = camPos.distanceToSquared(tmpCenter.set(cx, cy, cz));
                    bucket.push({ sa, sb, sc, rgb, alpha: Math.round(opacity * 255), depth, role, normal: normal.clone(), a: a.clone(), b: b.clone(), c: c.clone() });
                }
            });
            for (const tri of opaque) {
                rasterTriangle(frame, tri.sa, tri.sb, tri.sc, tri.rgb, 255, true);
                if (tri.role === 'grass' || tri.role === 'shadow') continue;
                rememberEdge(edges, tri.a, tri.b, tri.sa, tri.sb, tri.normal);
                rememberEdge(edges, tri.b, tri.c, tri.sb, tri.sc, tri.normal);
                rememberEdge(edges, tri.c, tri.a, tri.sc, tri.sa, tri.normal);
            }
            ctx.putImageData(frame.image, 0, 0);
            ctx.save();
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            edges.forEach((edge) => {
                const crease = edge.count === 1 || (edge.n1 && edge.n0.dot(edge.n1) < 0.86);
                if (!crease || !edgeOnTop(frame, edge)) return;
                ctx.strokeStyle = edge.count === 1 ? 'rgba(72, 66, 58, 0.42)' : 'rgba(96, 88, 78, 0.28)';
                ctx.lineWidth = edge.count === 1 ? 1.15 : 1;
                ctx.beginPath();
                ctx.moveTo(edge.x0, edge.y0);
                ctx.lineTo(edge.x1, edge.y1);
                ctx.stroke();
            });
            ctx.restore();
        }

        function edgeOnTop(frame, edge) {
            const steps = 5;
            let visible = 0;
            let tested = 0;
            for (let i = 0; i < steps; i++) {
                const t = (i + 0.5) / steps;
                const x = Math.round(edge.x0 + (edge.x1 - edge.x0) * t);
                const y = Math.round(edge.y0 + (edge.y1 - edge.y0) * t);
                const z = edge.z0 + (edge.z1 - edge.z0) * t;
                if (x < 1 || y < 1 || x >= frame.w - 1 || y >= frame.h - 1) continue;
                tested++;
                const buf = frame.depth[y * frame.w + x];
                if (z >= buf - 0.0015) visible++;
            }
            return tested > 0 && visible >= Math.ceil(tested * 0.6);
        }

        function rememberEdge(edges, v0, v1, s0, s1, normal) {
            const k0 = `${Math.round(v0.x * 400)},${Math.round(v0.y * 400)},${Math.round(v0.z * 400)}`;
            const k1 = `${Math.round(v1.x * 400)},${Math.round(v1.y * 400)},${Math.round(v1.z * 400)}`;
            const key = k0 < k1 ? k0 + '|' + k1 : k1 + '|' + k0;
            const existing = edges.get(key);
            if (!existing) {
                edges.set(key, { count: 1, n0: normal.clone(), x0: s0.x, y0: s0.y, z0: s0.z, x1: s1.x, y1: s1.y, z1: s1.z });
            } else if (existing.count === 1) {
                existing.count = 2;
                existing.n1 = normal.clone();
            }
        }

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[char]));
        }

        function modelKey(model) {
            return String(model.name || '').toLowerCase();
        }

        function isFixedEnd(model) {
            const key = modelKey(model);
            return key === 'endwall_1' || key === 'endwall_2';
        }

        function isGround(model) {
            return modelKey(model) === 'ground';
        }

        function loadGround(model) {
            return loadTemplate(model.path, true).then((template) => {
                const ground = template.clone(true);
                const box = new THREE.Box3().setFromObject(ground);
                // Vrch zeme presne na Z = 0, kde stojí dom.
                ground.position.z -= box.max.z;
                scene.add(ground);
                const center = new THREE.Box3().setFromObject(ground).getCenter(new THREE.Vector3());
                groundCenter.set(center.x, center.y);
            });
        }
        function loadTemplate(path, isGround = false) {
            if (templateCache.has(path)) return Promise.resolve(templateCache.get(path));
            return new Promise((resolve, reject) => {
                loader.load(path, (gltf) => {
                    stylizeTemplate(gltf.scene, isGround);
                    templateCache.set(path, gltf.scene);
                    resolve(gltf.scene);
                }, undefined, reject);
            });
        }

        function describe(model) {
            const known = CATALOG[modelKey(model)];
            if (known) return known;
            const label = String(model.name || '').replace(/[_-]+/g, ' ').trim();
            return { label: label.charAt(0).toUpperCase() + label.slice(1), desc: 'Segment steny 1,2 m', icon: 'plain', order: 99 };
        }

        function wallIcon(type) {
            if (type === 'end') {
                return '<svg viewBox="0 0 64 48" aria-hidden="true"><path class="wall" d="M8 44V20L32 5l24 15v24z"/><rect class="glass" x="27" y="17" width="10" height="9" rx="1.5"/></svg>';
            }
            const openings = {
                plain: '<line class="seam" x1="32" y1="10" x2="32" y2="38"/>',
                small: '<rect class="glass" x="26" y="13" width="12" height="11" rx="1.5"/>',
                narrow: '<rect class="glass" x="28" y="11" width="8" height="26" rx="1.5"/>',
                standard: '<rect class="glass" x="21" y="13" width="22" height="18" rx="1.5"/><line class="frame" x1="32" y1="13" x2="32" y2="31"/>',
                big: '<rect class="glass" x="12" y="12" width="40" height="22" rx="1.5"/><line class="frame" x1="25.3" y1="12" x2="25.3" y2="34"/><line class="frame" x1="38.7" y1="12" x2="38.7" y2="34"/>'
            };
            return `<svg viewBox="0 0 64 48" aria-hidden="true"><rect class="wall" x="6" y="6" width="52" height="36" rx="3"/>${openings[type] || openings.plain}</svg>`;
        }

        function renderCatalog() {
            if (!models.length) {
                catalogEl.innerHTML = '<p class="muted">Zatiaľ nie sú k dispozícii žiadne segmenty.</p>';
                return;
            }
            const price = money.format(PRICING.segment);
            catalogEl.innerHTML = models.map((model, index) => {
                const info = describe(model);
                const label = escapeHtml(info.label);
                return `<button type="button" class="card" data-index="${index}" onclick="addSegment(${index})" aria-label="Pridať segment ${label}, ${price}">
                    <span class="art card-art">${wallIcon(info.icon)}</span>
                    <span class="card-body">
                        <span class="card-title">${label}</span>
                        <span class="card-desc">${escapeHtml(info.desc)}</span>
                    </span>
                    <span class="card-side">
                        <span class="card-price">+${price}</span>
                        <span class="card-add" aria-hidden="true"><span class="add-idle">+</span><span class="add-done">✓</span></span>
                    </span>
                </button>`;
            }).join('');
        }

        function sequenceRow(number, info, meta, removeIndex) {
            const removable = removeIndex !== undefined;
            const classes = ['seq-item', removable ? '' : 'is-locked', removable && removeIndex === lastAdded ? 'is-new' : ''].filter(Boolean).join(' ');
            const label = escapeHtml(info.label);
            const action = removable
                ? `<button type="button" class="seq-remove" onclick="removeSegment(${removeIndex})" aria-label="Odstrániť ${label}" title="Odstrániť">${ICON_CLOSE}</button>`
                : `<span class="seq-lock" title="Pevná súčasť domu">${ICON_LOCK}</span>`;
            return `<li class="${classes}"><span class="seq-num">${number}</span><span class="art seq-art">${wallIcon(info.icon)}</span><span class="seq-text"><strong>${label}</strong><small>${meta}</small></span>${action}</li>`;
        }

        function renderSequence() {
            const endInfo = (model) => (model ? describe(model) : { label: 'Čelná stena', icon: 'end' });
            const endPrice = money.format(PRICING.endwall);
            const segmentMeta = '1,2 m · ' + money.format(PRICING.segment);
            const rows = [sequenceRow(1, endInfo(endwallStart), 'Predná · ' + endPrice)];
            if (!sequence.length) {
                rows.push('<li class="seq-empty">Zatiaľ žiadny segment. Vyberte ho v kroku 1.</li>');
            }
            sequence.forEach((model, index) => rows.push(sequenceRow(index + 2, describe(model), segmentMeta, index)));
            rows.push(sequenceRow(sequence.length + 2, endInfo(endwallEnd), 'Zadná · ' + endPrice));
            sequenceList.innerHTML = rows.join('');
            lastAdded = -1;
        }

        function updateSummary() {
            const length = (sequence.length + 2) * SEGMENT_WIDTH;
            const price = 2 * PRICING.endwall + sequence.length * PRICING.segment;
            document.getElementById('sumLength').textContent = decimal.format(length) + ' m';
            document.getElementById('sumArea').textContent = '≈ ' + decimal.format(length * HOUSE_WIDTH) + ' m²';
            document.getElementById('sumCount').textContent = String(sequence.length);
            document.getElementById('sumPrice').textContent = money.format(price);
            document.getElementById('modelName').textContent = 'Váš dom · ' + decimal.format(length) + ' × ' + decimal.format(HOUSE_WIDTH) + ' m';
        }

        function notify(message) {
            noticeEl.textContent = message;
            noticeEl.classList.add('is-visible');
            clearTimeout(noticeTimer);
            noticeTimer = setTimeout(() => noticeEl.classList.remove('is-visible'), 4000);
        }

        function flashCard(index) {
            const card = catalogEl.querySelector(`[data-index="${index}"]`);
            if (!card) return;
            card.classList.remove('is-added');
            void card.offsetWidth; // reštart CSS animácie pri opakovanom kliku
            card.classList.add('is-added');
            setTimeout(() => card.classList.remove('is-added'), 1200);
        }

        function rebuildAssembly() {
            if (!endwallStart || !endwallEnd) return Promise.resolve();
            const parts = [endwallStart, ...sequence, endwallEnd];
            setBusy(true);
            return Promise.all(parts.map((model) => loadTemplate(model.path))).then((templates) => {
                assembly.clear();
                templates.forEach((template, index) => {
                    const segment = template.clone(true);
                    segment.rotation.set(0, 0, 0);
                    segment.scale.set(1, 1, 1);
                    segment.position.set(0, -index * SEGMENT_WIDTH, 0);
                    assembly.add(segment);
                });
                assembly.position.set(0, 0, 0);
                assembly.updateMatrixWorld(true);
                const houseCenter = new THREE.Box3().setFromObject(assembly).getCenter(new THREE.Vector3());
                assembly.position.set(groundCenter.x - houseCenter.x, groundCenter.y - houseCenter.y, 0);
                assembly.updateMatrixWorld(true);
                const houseBox = new THREE.Box3().setFromObject(assembly);
                if (!houseBox.isEmpty()) {
                    const houseSize = houseBox.getSize(new THREE.Vector3());
                    const houseMid = houseBox.getCenter(new THREE.Vector3());
                    shadow.position.set(houseMid.x, houseMid.y, 0.012);
                    shadow.scale.set(Math.max(2.2, houseSize.x * 0.62), Math.max(1.5, houseSize.y * 0.78), 1);
                }
                if (!hasFramed) {
                    frameObject(assembly);
                    hasFramed = true;
                } else {
                    fitObjectKeepingAngle(assembly);
                }
                emptyState.classList.add('hidden');
                updateSummary();
                renderSequence();
                setBusy(false);
                if (!webglAvailable) renderSoftware();
            });
        }

        function handleRebuildError(error, message) {
            console.error(error);
            setBusy(false);
            renderSequence();
            updateSummary();
            notify(message);
        }

        window.addSegment = function (index) {
            const model = models[index];
            if (!model || !endwallStart || !endwallEnd) return;
            sequence.push(model);
            lastAdded = sequence.length - 1;
            flashCard(index);
            rebuildAssembly().catch((error) => {
                sequence.pop();
                handleRebuildError(error, 'Segment sa nepodarilo pridať. Skúste to znova.');
            });
        };

        window.removeSegment = function (index) {
            if (index < 0 || index >= sequence.length) return;
            sequence.splice(index, 1);
            rebuildAssembly().catch((error) => handleRebuildError(error, 'Dom sa nepodarilo prekresliť.'));
        };

        document.getElementById('clearAssembly').addEventListener('click', () => {
            if (!sequence.length) return;
            sequence.splice(0, sequence.length);
            rebuildAssembly().catch((error) => handleRebuildError(error, 'Dom sa nepodarilo prekresliť.'));
        });

        function hideHint() {
            hintEl.classList.add('hidden');
        }
        canvas.addEventListener('pointerdown', hideHint, { once: true });
        canvas.addEventListener('wheel', hideHint, { once: true, passive: true });
        canvas2d.addEventListener('pointerdown', hideHint, { once: true });
        canvas2d.addEventListener('wheel', hideHint, { once: true, passive: true });

        fetch('/api/models')
            .then((response) => {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
            .then((data) => {
                endwallStart = data.find((model) => modelKey(model) === 'endwall_1') || null;
                endwallEnd = data.find((model) => modelKey(model) === 'endwall_2') || null;
                models.splice(0, models.length, ...data.filter((model) => !isFixedEnd(model) && !isGround(model)));
                models.sort((left, right) => {
                    const a = describe(left);
                    const b = describe(right);
                    return (a.order - b.order) || a.label.localeCompare(b.label, 'sk');
                });
                renderCatalog();
                renderSequence();
                updateSummary();
                const groundModel = data.find(isGround);
                const groundReady = groundModel
                    ? loadGround(groundModel).catch((error) => console.error('ground.glb', error))
                    : Promise.resolve();
                if (!endwallStart || !endwallEnd) {
                    const missing = [!endwallStart ? 'endwall_1.glb' : null, !endwallEnd ? 'endwall_2.glb' : null].filter(Boolean).join(' a ');
                    console.error('Chýba ' + missing + ' v priečinku 3d_objects');
                    showFatal('Konfigurátor je dočasne nedostupný.');
                    return;
                }
                return groundReady.then(rebuildAssembly);
            })
            .catch((error) => {
                console.error(error);
                showFatal('Konfigurátor sa nepodarilo načítať. Obnovte stránku.');
            });

        resize();
        window.addEventListener('resize', resize);
        if (webglAvailable) {
            renderer.autoClear = false;
            function animate() {
                requestAnimationFrame(animate);
                controls.update();
                renderer.setScissorTest(false);
                renderer.setViewport(0, 0, Math.max(1, viewer.clientWidth), Math.max(1, viewer.clientHeight));
                renderer.clear();
                renderer.render(scene, camera);
            }
            animate();
        }
    </script>
</body>
</html>
