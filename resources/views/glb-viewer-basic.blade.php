<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D GLB Konfigurátor</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            color: #fff;
            height: 100vh;
            overflow: hidden;
        }
        
        .container {
            display: flex;
            height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: #16213e;
            border-right: 2px solid #0f3460;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.5);
            flex-shrink: 0;
        }

        .size-panel {
            width: 260px;
            background: #16213e;
            border-left: 2px solid #0f3460;
            padding: 20px;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .size-panel h2 {
            color: #00d4ff;
            margin-bottom: 8px;
            font-size: 18px;
            text-transform: uppercase;
        }

        .size-panel p {
            color: #888;
            font-size: 12px;
            margin-bottom: 18px;
        }

        .size-option {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            margin-bottom: 10px;
            padding: 12px;
            background: #0f3460;
            border: 2px solid #00d4ff;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .size-option:has(input:checked) {
            background: #00d4ff;
            color: #1a1a2e;
            font-weight: bold;
        }

        .size-option input {
            accent-color: #1a1a2e;
        }

        .custom-size {
            display: none;
            margin: -2px 0 12px;
            padding: 12px;
            border: 2px solid #0f3460;
            border-top: none;
            border-radius: 0 0 5px 5px;
            background: #0f0f1e;
        }

        .custom-size.visible {
            display: block;
        }

        .custom-size label {
            display: block;
            margin-bottom: 8px;
            color: #aaa;
            font-size: 12px;
        }

        .custom-size input {
            width: 100%;
            padding: 10px;
            border: 1px solid #00d4ff;
            border-radius: 4px;
            background: #16213e;
            color: #fff;
            font-size: 16px;
        }

        .size-summary {
            margin-top: 16px;
            padding: 12px;
            border-radius: 5px;
            background: #0f3460;
            color: #00d4ff;
            font-size: 14px;
        }
        
        .sidebar h2 {
            color: #00d4ff;
            margin-bottom: 20px;
            font-size: 18px;
            text-transform: uppercase;
        }
        
        .objects-list {
            list-style: none;
        }
        
        .objects-list li {
            margin-bottom: 10px;
        }
        
        .objects-list button {
            width: 100%;
            padding: 12px;
            background: #0f3460;
            border: 2px solid #00d4ff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-align: left;
        }
        
        .objects-list button:hover {
            background: #00d4ff;
            color: #1a1a2e;
            transform: translateX(5px);
        }
        
        .objects-list button.active {
            background: #00d4ff;
            color: #1a1a2e;
            font-weight: bold;
        }
        
        .viewer-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #0f0f1e;
        }
        
        .viewer-header {
            padding: 20px;
            background: #16213e;
            border-bottom: 2px solid #0f3460;
        }
        
        .viewer-header h1 {
            color: #00d4ff;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .status {
            font-size: 12px;
            color: #888;
        }
        
        .viewer-content {
            flex: 1;
            position: relative;
            background: linear-gradient(135deg, #0f0f1e 0%, #16213e 100%);
            min-height: 0;
        }
        
        #canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        
        .model-preview {
            text-align: center;
            padding: 40px;
        }
        
        .model-preview h2 {
            color: #00d4ff;
            margin-bottom: 20px;
            font-size: 22px;
        }
        
        .model-preview p {
            color: #aaa;
            margin: 8px 0;
        }
        
        .model-icon {
            font-size: 120px;
            margin: 30px 0;
            opacity: 0.8;
        }
        
        .empty-state {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #666;
            font-size: 18px;
            pointer-events: none;
        }

        .empty-state.hidden {
            display: none;
        }
        
        .controls {
            padding: 15px 20px;
            background: #16213e;
            border-top: 2px solid #0f3460;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- SIDEBAR - MENU -->
        <div class="sidebar">
            <h2>🏠 3D Modely</h2>
            <ul class="objects-list" id="objectsList">
                <li><em style="color: #666;">Načítavam...</em></li>
            </ul>
        </div>
        
        <!-- VIEWER -->
        <div class="viewer-container">
            <div class="viewer-header">
                <h1 id="modelName">GLB Konfigurátor</h1>
                <div class="status" id="status">Vyberte model z menu</div>
            </div>
            <div class="viewer-content" id="viewerContent">
                <canvas id="canvas"></canvas>
                <div class="empty-state" id="emptyState">
                    ✨ Vyberte model z menu →
                </div>
            </div>
            <div class="controls">
                💡 Aplikácia na konfiguráciu stavebnice zahradného domčeka
            </div>
        </div>

        <aside class="size-panel">
            <h2>Veľkosť domu</h2>
            <p>Vyberte práve jednu možnosť.</p>
            <label class="size-option">
                <input type="radio" name="houseSize" value="4" checked>
                <span>4 segmenty</span>
            </label>
            <label class="size-option">
                <input type="radio" name="houseSize" value="5">
                <span>5 segmentov</span>
            </label>
            <label class="size-option">
                <input type="radio" name="houseSize" value="6">
                <span>6 segmentov</span>
            </label>
            <label class="size-option">
                <input type="radio" name="houseSize" value="7">
                <span>7 segmentov</span>
            </label>
            <label class="size-option">
                <input type="radio" name="houseSize" value="custom">
                <span>Viac segmentov</span>
            </label>
            <div class="custom-size" id="customSize">
                <label for="customSegmentCount">Počet segmentov (8–99)</label>
                <input id="customSegmentCount" type="number" min="8" max="99" value="8">
            </div>
            <div class="size-summary" id="sizeSummary">Vybrané: 4 segmenty</div>
            <p style="margin-top:14px;">Klik na model vloží ďalší segment vedľa predchádzajúceho. Otočenie ostáva z CAD, posun je 1200 mm.</p>
            <button type="button" id="clearAssembly" class="size-option" style="justify-content:center; margin-top:12px;">Vymazať skladbu</button>
        </aside>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

        const SEGMENT_WIDTH = 1.2;
        const models = [];
        const statusEl = document.getElementById('status');
        const emptyState = document.getElementById('emptyState');
        const viewer = document.getElementById('viewerContent');
        const canvas = document.getElementById('canvas');
        const sizeSummary = document.getElementById('sizeSummary');
        const customSize = document.getElementById('customSize');
        const customCount = document.getElementById('customSegmentCount');

        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x0f0f1e);

        const camera = new THREE.PerspectiveCamera(50, 1, 0.01, 1000);
        camera.position.set(3, -1, 8);

        const renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
        renderer.outputColorSpace = THREE.SRGBColorSpace;

        scene.add(new THREE.AmbientLight(0xffffff, 1.1));
        const keyLight = new THREE.DirectionalLight(0xffffff, 1.6);
        keyLight.position.set(4, 6, 5);
        scene.add(keyLight);
        const fillLight = new THREE.DirectionalLight(0x88cfff, 0.6);
        fillLight.position.set(-4, 2, -3);
        scene.add(fillLight);

        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.08;

        const assembly = new THREE.Group();
        scene.add(assembly);
        const loader = new GLTFLoader();
        const templateCache = new Map();

        function resize() {
            const width = viewer.clientWidth || 1;
            const height = viewer.clientHeight || 1;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height, false);
        }

        function frameObject(object) {
            const box = new THREE.Box3().setFromObject(object);
            if (box.isEmpty()) return;
            const size = box.getSize(new THREE.Vector3());
            const center = box.getCenter(new THREE.Vector3());
            const maxDim = Math.max(size.x, size.y, size.z) || 1;
            const distance = maxDim / (2 * Math.tan((camera.fov * Math.PI) / 360));

            controls.target.copy(center);
            camera.position.set(center.x + distance * 0.85, center.y + distance * 0.15, center.z + distance * 1.15);
            camera.near = Math.max(distance / 200, 0.01);
            camera.far = Math.max(distance * 40, 100);
            camera.updateProjectionMatrix();
            controls.update();
        }

        function selectedSegmentCount() {
            const selected = document.querySelector('input[name="houseSize"]:checked');
            if (!selected || selected.value === 'custom') {
                const count = Math.min(99, Math.max(8, Number(customCount.value) || 8));
                customCount.value = String(count);
                return count;
            }
            return Number(selected.value);
        }

        function segmentLabel(count) {
            if (count === 1) return 'segment';
            if (count >= 2 && count <= 4) return 'segmenty';
            return 'segmentov';
        }

        function loadTemplate(path) {
            if (templateCache.has(path)) {
                return Promise.resolve(templateCache.get(path));
            }
            return new Promise((resolve, reject) => {
                loader.load(path, (gltf) => {
                    templateCache.set(path, gltf.scene);
                    resolve(gltf.scene);
                }, undefined, reject);
            });
        }

        function placeSegment(template, index) {
            const segment = template.clone(true);
            segment.rotation.set(0, 0, 0);
            segment.scale.set(1, 1, 1);
            segment.position.set(0, -index * SEGMENT_WIDTH, 0);
            assembly.add(segment);
        }

        function rebuildAssembly(model) {
            const count = selectedSegmentCount();
            statusEl.textContent = 'Skladám ' + count + ' ' + segmentLabel(count) + '...';
            return loadTemplate(model.path).then((template) => {
                assembly.clear();
                for (let index = 0; index < count; index += 1) {
                    placeSegment(template, index);
                }
                frameObject(assembly);
                emptyState.classList.add('hidden');
                document.getElementById('modelName').textContent = model.name + ' × ' + count;
                statusEl.textContent = count + ' ' + segmentLabel(count) + ' vedľa seba, posun 1200 mm, bez otočenia.';
            });
        }

        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }

        window.selectModel = function (index) {
            const model = models[index];
            if (!model) return;

            document.querySelectorAll('#objectsList button').forEach((button) => button.classList.remove('active'));
            document.getElementById('btn-' + index).classList.add('active');
            window.selectedModel = model;
            rebuildAssembly(model).catch((error) => {
                console.error(error);
                statusEl.textContent = 'Chyba pri skladaní modelu: ' + (error && error.message ? error.message : 'neznáma chyba');
            });
        };

        function renderModels() {
            const list = document.getElementById('objectsList');
            list.innerHTML = models.map((model, index) =>
                `<li><button type="button" id="btn-${index}" onclick="selectModel(${index})">${model.name}</button></li>`
            ).join('');
        }

        function updateHouseSize() {
            const selected = document.querySelector('input[name="houseSize"]:checked');
            const isCustom = selected && selected.value === 'custom';
            customSize.classList.toggle('visible', Boolean(isCustom));
            const count = selectedSegmentCount();
            sizeSummary.textContent = 'Vybrané: ' + count + ' ' + segmentLabel(count);
            window.houseConfig = { segments: count, pitchMm: 1200 };
            if (window.selectedModel) {
                rebuildAssembly(window.selectedModel).catch((error) => {
                    console.error(error);
                    statusEl.textContent = 'Chyba pri skladaní modelu';
                });
            }
        }

        document.querySelectorAll('input[name="houseSize"]').forEach((input) => {
            input.addEventListener('change', updateHouseSize);
        });
        customCount.addEventListener('change', updateHouseSize);
        document.getElementById('clearAssembly').addEventListener('click', () => {
            assembly.clear();
            window.selectedModel = null;
            document.querySelectorAll('#objectsList button').forEach((button) => button.classList.remove('active'));
            document.getElementById('modelName').textContent = 'GLB Konfigurátor';
            emptyState.classList.remove('hidden');
            statusEl.textContent = 'Skladba vymazaná';
        });

        fetch('/api/models')
            .then((response) => {
                if (!response.ok) throw new Error('API ' + response.status);
                return response.json();
            })
            .then((data) => {
                models.splice(0, models.length, ...data);
                renderModels();
                statusEl.textContent = models.length + ' modelov pripravených';
            })
            .catch((error) => {
                console.error(error);
                document.getElementById('objectsList').innerHTML = '<li><em style="color:#ff8080;">Chyba pri načítaní menu</em></li>';
                statusEl.textContent = 'Menu sa nepodarilo načítať';
            });

        updateHouseSize();
        resize();
        window.addEventListener('resize', resize);
        animate();
    </script>
</html>
