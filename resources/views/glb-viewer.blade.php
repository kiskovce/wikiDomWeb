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
            <div id="canvasContainer" style="position: relative; flex: 1;">
                <div class="loading" id="loading">⏳ Načítavam model...</div>
                <div class="no-models" id="noModels">
                    Vyberte model z menu →
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
    <!-- OrbitControls -->
    <script src="https://cdn.jsdelivr.net/npm/three@r128/examples/js/controls/OrbitControls.js"></script>

    <script>
        let scene, camera, renderer, controls, currentModel;
        let models = [];
        const container = document.getElementById('canvasContainer');
        
        // INICIALIZÁCIA THREE.JS
        function initThreeJs() {
            try {
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0x0f0f1e);
                
                camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
                camera.position.z = 3;
                
                renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                renderer.setSize(container.clientWidth, container.clientHeight);
                renderer.shadowMap.enabled = true;
                container.appendChild(renderer.domElement);
            } catch (error) {
                console.error('Three.js initialization failed:', error);
                document.getElementById('noModels').innerHTML = '⚠️ WebGL nie je dostupný. Otvorte v externom prehliadači (Chrome/Firefox)';
                document.getElementById('noModels').style.display = 'flex';
                return false;
            }
            
            // Osvětlení
            const light1 = new THREE.DirectionalLight(0xffffff, 1);
            light1.position.set(5, 5, 5);
            light1.castShadow = true;
            scene.add(light1);
            
            const light2 = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(light2);
            
            // OrbitControls
            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.autoRotate = true;
            controls.autoRotateSpeed = 5;
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            
            // Animation loop
            function animate() {
                requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            animate();
            
            // Resize handling
            window.addEventListener('resize', () => {
                const width = container.clientWidth;
                const height = container.clientHeight;
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                renderer.setSize(width, height);
            });
            
            return true;
        }
        
        // NAČÍTANIE ZOZNAMU MODELOV
        async function loadModelsList() {
            try {
                const response = await fetch('/api/models');
                models = await response.json();
                renderModelsList();
            } catch (error) {
                console.error('Chyba pri načítaní modelov:', error);
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
        async function loadModel(modelPath, modelName, index) {
            if (!renderer) {
                alert('⚠️ WebGL nie je dostupný. Prosím, otvorte túto stránku v externom prehliadači (Chrome/Firefox)');
                return;
            }
            
            document.getElementById('loading').classList.add('active');
            document.getElementById('noModels').style.display = 'none';
            document.getElementById('modelName').textContent = '📦 ' + modelName;
            
            // Aktualizovať aktívne tlačidlo
            document.querySelectorAll('.objects-list button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${index}`).classList.add('active');
            
            try {
                // Vyčistenie staého modelu
                if (currentModel) scene.remove(currentModel);
                
                const loader = new THREE.GLTFLoader();
                loader.load(modelPath, (gltf) => {
                    currentModel = gltf.scene;
                    scene.add(currentModel);
                    
                    // Centralizácia a scaling
                    const box = new THREE.Box3().setFromObject(currentModel);
                    const size = box.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    const scale = 2.5 / maxDim;
                    currentModel.scale.multiplyScalar(scale);
                    
                    const center = box.getCenter(new THREE.Vector3());
                    currentModel.position.x = -center.x * scale;
                    currentModel.position.y = -center.y * scale;
                    currentModel.position.z = -center.z * scale;
                    
                    camera.position.z = 3;
                    
                    document.getElementById('loading').classList.remove('active');
                    document.getElementById('status').textContent = '✅ Model načítaný | Rotujte myšou';
                }, undefined, (error) => {
                    console.error('Chyba pri načítaní GLB:', error);
                    document.getElementById('loading').classList.remove('active');
                    document.getElementById('status').textContent = '❌ Chyba pri načítaní';
                });
            } catch (error) {
                console.error('Chyba:', error);
                document.getElementById('loading').classList.remove('active');
            }
        }
        
        // SPUSTENIE
        loadModelsList();
        if (!initThreeJs()) {
            console.warn('Three.js initialization failed, but models menu should still work');
        }
    </script>
</body>
</html>
