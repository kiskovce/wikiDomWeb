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
            letter-spacing: 1px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .viewer-header h1 {
            color: #00d4ff;
            font-size: 24px;
        }
        
        .status {
            font-size: 12px;
            color: #888;
        }
        
        #canvas {
            flex: 1;
            display: block;
            width: 100%;
            height: 100%;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            color: #00d4ff;
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
        
        .no-models {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-size: 18px;
            color: #666;
            flex-direction: column;
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- SIDEBAR - MENU -->
        <div class="sidebar">
            <h2>🏠 3D Modely</h2>
            <ul class="objects-list" id="objectsList">
                <li><em style="color: #666;">Načítavam modely...</em></li>
            </ul>
        </div>
        
        <!-- VIEWER - OKNO S NÁHĽADOM -->
        <div class="viewer-container">
            <div class="viewer-header">
                <div>
                    <h1 id="modelName">GLB Viewer</h1>
                    <div class="status" id="status">Vyberte model z menu</div>
                </div>
            </div>
            <div id="canvasContainer" style="position: relative; flex: 1; width: 100%;">
                <div class="loading" id="loading">⏳ Načítavam model...</div>
                <canvas id="canvas"></canvas>
                <div class="no-models" id="noModels">
                    ✨ Vyberte model z menu →
                </div>
            </div>
            <div class="controls">
                💡 Tip: Kliknite na model v menu a poďte ho rotáciou myšou
            </div>
        </div>
    </div>

    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- GLB Loader -->
    <script src="https://cdn.jsdelivr.net/npm/three@r128/examples/js/loaders/GLTFLoader.js"></script>

    <script>
        let scene, camera, renderer, currentModel;
        let models = [];
        const canvas = document.getElementById('canvas');
        const container = document.getElementById('canvasContainer');
        let animationId;
        
        // INICIALIZÁCIA THREE.JS
        function initThreeJs() {
            try {
                console.log('Initializing Three.js...');
                
                const width = container.clientWidth;
                const height = container.clientHeight;
                
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0x0f0f1e);
                scene.fog = new THREE.Fog(0x0f0f1e, 20, 100);
                
                camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
                camera.position.set(0, 0, 5);
                
                renderer = new THREE.WebGLRenderer({ 
                    canvas: canvas,
                    antialias: true, 
                    alpha: true,
                    powerPreference: 'high-performance'
                });
                renderer.setSize(width, height);
                renderer.setPixelRatio(window.devicePixelRatio);
                renderer.shadowMap.enabled = true;
                renderer.shadowMap.type = THREE.PCFShadowShadowMap;
                
                console.log('✅ WebGL Renderer created');
                
                // Osvětlení
                const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
                scene.add(ambientLight);
                
                const directionalLight = new THREE.DirectionalLight(0xffffff, 1.2);
                directionalLight.position.set(10, 15, 10);
                directionalLight.castShadow = true;
                directionalLight.shadow.camera.left = -20;
                directionalLight.shadow.camera.right = 20;
                directionalLight.shadow.camera.top = 20;
                directionalLight.shadow.camera.bottom = -20;
                scene.add(directionalLight);
                
                // Animační smyčka
                function animate() {
                    animationId = requestAnimationFrame(animate);
                    
                    // Automatická rotace modelu
                    if (currentModel) {
                        currentModel.rotation.x += 0.005;
                        currentModel.rotation.y += 0.008;
                    }
                    
                    renderer.render(scene, camera);
                }
                animate();
                
                // Resize handler
                window.addEventListener('resize', () => {
                    const w = container.clientWidth;
                    const h = container.clientHeight;
                    camera.aspect = w / h;
                    camera.updateProjectionMatrix();
                    renderer.setSize(w, h);
                });
                
                // Mouse events pro manuální rotaci
                let isDragging = false;
                let previousMousePosition = { x: 0, y: 0 };
                
                canvas.addEventListener('mousedown', (e) => {
                    isDragging = true;
                    previousMousePosition = { x: e.clientX, y: e.clientY };
                });
                
                canvas.addEventListener('mousemove', (e) => {
                    if (isDragging && currentModel) {
                        const deltaX = e.clientX - previousMousePosition.x;
                        const deltaY = e.clientY - previousMousePosition.y;
                        
                        currentModel.rotation.y += deltaX * 0.005;
                        currentModel.rotation.x += deltaY * 0.005;
                        
                        previousMousePosition = { x: e.clientX, y: e.clientY };
                    }
                });
                
                canvas.addEventListener('mouseup', () => {
                    isDragging = false;
                });
                
                return true;
            } catch (error) {
                console.error('Three.js initialization failed:', error);
                document.getElementById('noModels').innerHTML = `
                    <div>❌ WebGL Error</div>
                    <div style="font-size: 12px; color: #999;">${error.message}</div>
                `;
                document.getElementById('noModels').style.display = 'flex';
                return false;
            }
        }
        
        // NAČÍTANIE ZOZNAMU MODELOV
        async function loadModelsList() {
            try {
                console.log('Fetching models from /api/models...');
                const response = await fetch('/api/models');
                models = await response.json();
                console.log('Models loaded:', models);
                renderModelsList();
            } catch (error) {
                console.error('Error loading models:', error);
                document.getElementById('objectsList').innerHTML = '<li><em style="color: red;">Chyba pri načítaní</em></li>';
            }
        }
        
        // VYKRESLENIE MENU
        function renderModelsList() {
            const list = document.getElementById('objectsList');
            if (models.length === 0) {
                list.innerHTML = '<li><em style="color: #666;">Žiadne modely</em></li>';
                return;
            }
            
            list.innerHTML = models.map((model, index) => `
                <li>
                    <button onclick="loadModel('${model.path}', '${model.name}', ${index})" id="btn-${index}">
                        📦 ${model.name}
                    </button>
                </li>
            `).join('');
        }
        
        // NAČÍTANIE MODELU
        function loadModel(modelPath, modelName, index) {
            if (!renderer) {
                alert('Renderer not initialized');
                return;
            }
            
            console.log('Loading model:', modelPath);
            document.getElementById('loading').classList.add('active');
            document.getElementById('noModels').style.display = 'none';
            document.getElementById('modelName').textContent = '📦 ' + modelName;
            document.getElementById('status').textContent = '⏳ Načítavam...';
            
            // Highlight active button
            document.querySelectorAll('.objects-list button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${index}`).classList.add('active');
            
            // Remove old model
            if (currentModel) {
                scene.remove(currentModel);
                currentModel = null;
            }
            
            const loader = new THREE.GLTFLoader();
            loader.load(
                modelPath,
                (gltf) => {
                    console.log('Model loaded successfully:', gltf);
                    currentModel = gltf.scene;
                    scene.add(currentModel);
                    
                    // Auto-fit camera to model
                    const box = new THREE.Box3().setFromObject(currentModel);
                    const size = box.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    const scale = 4 / maxDim;
                    currentModel.scale.multiplyScalar(scale);
                    
                    const center = box.getCenter(new THREE.Vector3());
                    currentModel.position.sub(center.multiplyScalar(scale));
                    
                    document.getElementById('loading').classList.remove('active');
                    document.getElementById('status').textContent = '✅ Načítané | Rotujte: myš';
                },
                (progress) => {
                    const percent = Math.round((progress.loaded / progress.total) * 100);
                    document.getElementById('status').textContent = `⏳ Načítavam: ${percent}%`;
                },
                (error) => {
                    console.error('Error loading model:', error);
                    document.getElementById('loading').classList.remove('active');
                    document.getElementById('status').textContent = '❌ Chyba pri načítaní';
                }
            );
        }
        
        // SPUSTENIE
        console.log('Starting application...');
        if (initThreeJs()) {
            loadModelsList();
        } else {
            console.error('Failed to initialize Three.js');
            loadModelsList(); // Still load models list even if Three.js fails
        }
    </script>
</body>
</html>
