<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D GLB Konfigurátor</title>
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
        }
        
        #canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(22, 33, 62, 0.95);
            padding: 30px 50px;
            border-radius: 10px;
            border: 2px solid #0f3460;
            display: none;
            z-index: 100;
        }
        
        .loading.active {
            display: block;
        }
        
        .controls {
            padding: 15px 20px;
            background: #16213e;
            border-top: 2px solid #0f3460;
            font-size: 12px;
            color: #888;
        }
        
        .empty-state {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #666;
            font-size: 18px;
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
                <h1 id="modelName">GLB Viewer</h1>
                <div class="status" id="status">Vyberte model z menu</div>
            </div>
            <div class="viewer-content" id="viewerContent">
                <canvas id="canvas"></canvas>
                <div class="loading" id="loading">⏳ Načítavam model...</div>
                <div class="empty-state" id="emptyState">✨ Vyberte model z menu →</div>
            </div>
            <div class="controls">
                💡 Rotujte: ľavé tlačidlo myšou | Zoom: Koliesko | Pan: Pravé tlačidlo
            </div>
        </div>
    </div>

    <!-- Three.js local -->
    <script type="importmap">
    {
        "imports": {
            "three": "/libs/three.module.js",
            "three/addons/controls/OrbitControls.js": "/libs/OrbitControls.js",
            "three/addons/loaders/GLTFLoader.js": "/libs/GLTFLoader.js"
        }
    }
    </script>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
        
        let scene, camera, renderer, controls, currentModel;
        let models = [];
        const canvas = document.getElementById('canvas');
        const container = document.getElementById('viewerContent');
        
        // INICIALIZÁCIA THREE.JS
        function initThreeJs() {
            try {
                const width = container.clientWidth;
                const height = container.clientHeight;
                
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0x0f0f1e);
                
                camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
                camera.position.z = 5;
                
                renderer = new THREE.WebGLRenderer({ 
                    canvas: canvas,
                    antialias: true,
                    alpha: true
                });
                renderer.setSize(width, height);
                renderer.setPixelRatio(window.devicePixelRatio);
                renderer.shadowMap.enabled = true;
                
                // Lights
                const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
                scene.add(ambientLight);
                
                const directionalLight = new THREE.DirectionalLight(0xffffff, 1.2);
                directionalLight.position.set(10, 15, 10);
                directionalLight.castShadow = true;
                scene.add(directionalLight);
                
                // Controls
                controls = new OrbitControls(camera, renderer.domElement);
                controls.autoRotate = true;
                controls.autoRotateSpeed = 5;
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;
                
                // Render loop
                function animate() {
                    requestAnimationFrame(animate);
                    controls.update();
                    renderer.render(scene, camera);
                }
                animate();
                
                // Resize
                window.addEventListener('resize', () => {
                    const w = container.clientWidth;
                    const h = container.clientHeight;
                    camera.aspect = w / h;
                    camera.updateProjectionMatrix();
                    renderer.setSize(w, h);
                });
                
                console.log('✅ Three.js initialized');
                return true;
            } catch (error) {
                console.error('Three.js init error:', error);
                return false;
            }
        }
        
        // NAČÍTANIE ZOZNAMU MODELOV
        async function loadModelsList() {
            try {
                const response = await fetch('/api/models');
                models = await response.json();
                renderModelsList();
                document.getElementById('status').textContent = '✅ ' + models.length + ' modelov';
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('objectsList').innerHTML = '<li><em style="color: red;">❌ Chyba</em></li>';
            }
        }
        
        // VYKRESLENIE MENU
        function renderModelsList() {
            const list = document.getElementById('objectsList');
            list.innerHTML = models.map((model, index) => `
                <li>
                    <button onclick="window.loadModel('${model.path}', '${model.name}', ${index})" id="btn-${index}" class="model-btn">
                        📦 ${model.name}
                    </button>
                </li>
            `).join('');
        }
        
        // NAČÍTANIE MODELU
        window.loadModel = function(modelPath, modelName, index) {
            if (!renderer) {
                document.getElementById('status').textContent = '❌ Three.js nie je dostupný';
                return;
            }
            
            console.log('Loading:', modelPath);
            document.getElementById('loading').classList.add('active');
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('modelName').textContent = '📦 ' + modelName;
            document.getElementById('status').textContent = '⏳ Načítavam...';
            
            // Highlight
            document.querySelectorAll('.model-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${index}`).classList.add('active');
            
            // Remove old model
            if (currentModel) scene.remove(currentModel);
            
            const loader = new GLTFLoader();
            loader.load(
                modelPath,
                (gltf) => {
                    currentModel = gltf.scene;
                    scene.add(currentModel);
                    
                    // Fit to view
                    const box = new THREE.Box3().setFromObject(currentModel);
                    const size = box.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    const scale = 4 / maxDim;
                    currentModel.scale.multiplyScalar(scale);
                    
                    const center = box.getCenter(new THREE.Vector3());
                    currentModel.position.sub(center.multiplyScalar(scale));
                    
                    camera.position.z = 5;
                    controls.reset();
                    
                    document.getElementById('loading').classList.remove('active');
                    document.getElementById('status').textContent = '✅ Načítané | Rotujte myšou';
                    console.log('✅ Model loaded');
                },
                undefined,
                (error) => {
                    console.error('Load error:', error);
                    document.getElementById('loading').classList.remove('active');
                    document.getElementById('status').textContent = '❌ Chyba pri načítaní';
                }
            );
        };
        
        // SPUSTENIE
        console.log('Starting app...');
        if (initThreeJs()) {
            loadModelsList();
        } else {
            document.getElementById('status').textContent = '❌ WebGL nie je dostupný';
            loadModelsList();
        }
    </script>
</body>
</html>
