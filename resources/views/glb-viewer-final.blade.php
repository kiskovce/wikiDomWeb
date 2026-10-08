<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D GLB Konfigurátor</title>
    <script src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
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
        
        model-viewer {
            width: 100%;
            height: calc(100% - 60px);
            background: linear-gradient(135deg, #0f0f1e 0%, #16213e 100%);
        }
        
        .controls {
            padding: 15px 20px;
            background: #16213e;
            border-top: 2px solid #0f3460;
            font-size: 12px;
            color: #888;
        }
        
        .no-model {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
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
                <li><em style="color: #666;">Načítavam...</em></li>
            </ul>
        </div>
        
        <!-- VIEWER -->
        <div class="viewer-container">
            <div class="viewer-header">
                <h1 id="modelName">GLB Viewer</h1>
                <div class="status" id="status">Vyberte model z menu</div>
            </div>
            <div id="viewerContent" style="flex: 1; position: relative;">
                <div class="no-model">
                    ✨ Vyberte model z menu →
                </div>
            </div>
            <div class="controls">
                💡 Rotujte: ľavé tlačidlo myšou | Zoom: Koliesko myšou | Pan: Pravé tlačidlo
            </div>
        </div>
    </div>

    <script>
        let models = [];
        
        // NAČÍTANIE ZOZNAMU MODELOV
        async function loadModelsList() {
            try {
                const response = await fetch('/api/models');
                models = await response.json();
                renderModelsList();
                document.getElementById('status').textContent = '✅ Aplikácia je pripravená';
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('objectsList').innerHTML = '<li><em style="color: red;">❌ Chyba</em></li>';
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
                    <button onclick="loadModel('${model.path}', '${model.name}', ${index})" id="btn-${index}" class="model-btn">
                        📦 ${model.name}
                    </button>
                </li>
            `).join('');
        }
        
        // NAČÍTANIE MODELU
        function loadModel(modelPath, modelName, index) {
            console.log('Loading model:', modelPath);
            document.getElementById('modelName').textContent = '📦 ' + modelName;
            document.getElementById('status').textContent = '⏳ Načítavam...';
            
            // Highlight active button
            document.querySelectorAll('.model-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${index}`).classList.add('active');
            
            // Create model-viewer
            const viewer = document.getElementById('viewerContent');
            viewer.innerHTML = `
                <model-viewer 
                    src="${modelPath}"
                    alt="3D Model - ${modelName}"
                    auto-rotate
                    camera-controls
                    style="width: 100%; height: 100%;">
                </model-viewer>
            `;
            
            // Update status when model loads
            const modelViewer = viewer.querySelector('model-viewer');
            modelViewer.addEventListener('load', () => {
                document.getElementById('status').textContent = '✅ Model načítaný | Rotujte myšou';
                console.log('✅ Model loaded');
            });
            
            modelViewer.addEventListener('error', (e) => {
                document.getElementById('status').textContent = '❌ Chyba pri načítaní';
                console.error('Model loading error:', e);
            });
        }
        
        // SPUSTENIE
        loadModelsList();
    </script>
</body>
</html>
