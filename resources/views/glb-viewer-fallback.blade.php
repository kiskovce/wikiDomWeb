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
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .info-box {
            background: #16213e;
            border: 2px solid #0f3460;
            padding: 40px;
            border-radius: 10px;
            max-width: 600px;
            margin-bottom: 30px;
        }
        
        .info-box h2 {
            color: #00d4ff;
            margin-bottom: 15px;
        }
        
        .info-box p {
            color: #aaa;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .info-box a {
            color: #00d4ff;
            text-decoration: none;
            font-weight: bold;
        }
        
        .info-box a:hover {
            text-decoration: underline;
        }
        
        .model-info {
            background: #0f3460;
            padding: 20px;
            border-radius: 5px;
            text-align: left;
            width: 100%;
            max-width: 400px;
            margin-top: 20px;
        }
        
        .model-info p {
            margin: 8px 0;
            font-size: 13px;
        }
        
        .model-info strong {
            color: #00d4ff;
        }
        
        .controls {
            padding: 15px 20px;
            background: #16213e;
            border-top: 2px solid #0f3460;
            font-size: 12px;
            color: #888;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-group a, .btn-group button {
            padding: 10px 20px;
            border: 2px solid #00d4ff;
            background: transparent;
            color: #00d4ff;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-group a:hover, .btn-group button:hover {
            background: #00d4ff;
            color: #1a1a2e;
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
                <h1 id="modelName">GLB Konfigurátor</h1>
                <div class="status" id="status">Vyberte model z menu</div>
            </div>
            <div class="viewer-content" id="viewerContent">
                <div class="info-box">
                    <h2>⚠️ WebGL nie je dostupný</h2>
                    <p>VS Code integrovaný prehliadač nepodporuje WebGL (potrebný pre 3D grafiku).</p>
                    <p><strong>Riešenie:</strong> Otvorte si Chrome alebo Firefox a prejdite na:</p>
                    <p style="color: #00d4ff; font-size: 14px; font-family: monospace; margin-top: 15px;">
                        http://localhost:8000/glb-viewer
                    </p>
                </div>
            </div>
            <div class="controls">
                💡 Menu s modelmi je plne funkčný - len viewer potrebuje natívny prehliadač
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
            } catch (error) {
                console.error('Error loading models:', error);
                document.getElementById('objectsList').innerHTML = '<li><em style="color: red;">Chyba</em></li>';
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
                    <button onclick="selectModel('${model.path}', '${model.name}', '${model.file}', ${index})" id="btn-${index}">
                        📦 ${model.name}
                    </button>
                </li>
            `).join('');
        }
        
        // VÝBER MODELU
        function selectModel(modelPath, modelName, fileName, index) {
            document.getElementById('modelName').textContent = '📦 ' + modelName;
            document.getElementById('status').textContent = 'Vybraný model: ' + modelName;
            
            // Highlight active button
            document.querySelectorAll('.objects-list button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${index}`).classList.add('active');
            
            // Show model info
            const viewer = document.getElementById('viewerContent');
            viewer.innerHTML = `
                <div class="info-box">
                    <h2>📦 ${modelName}</h2>
                    <p>Model je pripravený na zobrazenie. Pre 3D náhľad je potrebný natívny prehliadač s WebGL podporou.</p>
                    
                    <div class="model-info">
                        <p><strong>Názov:</strong> ${modelName}</p>
                        <p><strong>Súbor:</strong> ${fileName}</p>
                        <p><strong>Cesta:</strong> ${modelPath}</p>
                    </div>
                    
                    <div class="btn-group">
                        <a href="${modelPath}" download>📥 Stiahnuť GLB</a>
                        <button onclick="alert('Otvorte http://localhost:8000/glb-viewer\\nv Chrome alebo Firefox')">🔗 Otvoriť 3D Viewer</button>
                    </div>
                </div>
            `;
        }
        
        // SPUSTENIE
        loadModelsList();
    </script>
</body>
</html>
