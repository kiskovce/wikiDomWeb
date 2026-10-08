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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        
        .preview-box {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #16213e, #0f3460);
            border-radius: 10px;
            border: 2px solid #0f3460;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .preview-box.loading {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .model-info {
            text-align: center;
            padding: 30px;
        }
        
        .model-info h2 {
            color: #00d4ff;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .model-info p {
            color: #aaa;
            margin: 10px 0;
            font-size: 14px;
        }
        
        .model-preview-img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            margin: 20px 0;
            opacity: 0.7;
        }
        
        .controls {
            padding: 15px 20px;
            background: #16213e;
            border-top: 2px solid #0f3460;
            font-size: 12px;
            color: #888;
        }
        
        .empty-state {
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
                <div class="preview-box">
                    <div class="empty-state">
                        ✨ Vyberte model z menu →
                    </div>
                </div>
            </div>
            <div class="controls">
                💡 Detailný náhľad jednotlivých 3D segmentov stavebnice
            </div>
        </div>
    </div>

    <script>
        let models = [];
        
        // NAČÍTANIE ZOZNAMU MODELOV
        async function loadModelsList() {
            try {
                console.time('Load models');
                const response = await fetch('/api/models');
                if (!response.ok) throw new Error('HTTP ' + response.status);
                
                models = await response.json();
                console.timeEnd('Load models');
                console.log('Models loaded:', models.length);
                
                renderModelsList();
                document.getElementById('status').textContent = '✅ ' + models.length + ' modelov načítaných';
            } catch (error) {
                console.error('Error loading models:', error);
                document.getElementById('objectsList').innerHTML = '<li><em style="color: red;">❌ ' + error.message + '</em></li>';
                document.getElementById('status').textContent = '❌ Chyba: ' + error.message;
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
                    <button onclick="selectModel(${index})" id="btn-${index}" class="model-btn">
                        📦 ${model.name}
                    </button>
                </li>
            `).join('');
        }
        
        // VÝBER MODELU
        function selectModel(index) {
            const model = models[index];
            console.log('Selected model:', model);
            
            document.getElementById('modelName').textContent = '📦 ' + model.name;
            document.getElementById('status').textContent = '📦 Model: ' + model.name + ' | Veľkosť: ' + formatFileSize(getFileSizeSync(model.path));
            
            // Highlight active button
            document.querySelectorAll('.model-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${index}`).classList.add('active');
            
            // Show model info with loading
            const viewer = document.getElementById('viewerContent');
            viewer.innerHTML = `
                <div class="preview-box loading">
                    <div class="model-info">
                        <h2>📦 ${model.name}</h2>
                        <p style="margin-top: 20px;">⏳ Načítavam model...</p>
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">Cesta: ${model.path}</p>
                    </div>
                </div>
            `;
            
            // Simulate loading complete after 1 second
            setTimeout(() => {
                viewer.innerHTML = `
                    <div class="preview-box">
                        <div class="model-info">
                            <h2>✅ ${model.name}</h2>
                            <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #00d4ff, #0f3460); border-radius: 10px; margin: 20px auto; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 60px;">📦</span>
                            </div>
                            <p style="margin-top: 20px;">Súbor: <strong>${model.file}</strong></p>
                            <p style="font-size: 12px; color: #666;">Formát: GLB (Khronos Group Binary)</p>
                            <p style="font-size: 12px; color: #00d4ff; margin-top: 15px;">Model je pripravený na konfiguráciu</p>
                        </div>
                    </div>
                `;
                document.getElementById('status').textContent = '✅ Model je vybraný a pripravený';
            }, 1000);
        }
        
        // Pomocné funkcie
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
        
        function getFileSizeSync(path) {
            // Simulácia - reálna veľkosť bude v API
            const sizes = {
                '/3d_objects/default.glb': 15840,
                '/3d_objects/endwall.glb': 16936,
                '/3d_objects/window.glb': 21136,
                '/3d_objects/small_window.glb': 21136,
                '/3d_objects/narrow window.glb': 21144,
                '/3d_objects/big window.glb': 21180,
                '/3d_objects/2f_window.glb': 26504,
                '/3d_objects/2f_small_window.glb': 26544,
                '/3d_objects/2F_narrow_window.glb': 26544,
                '/3d_objects/2f_endwall.glb': 23352,
            };
            return sizes[path] || 0;
        }
        
        // SPUSTENIE
        console.log('App starting...');
        loadModelsList();
    </script>
</body>
</html>
