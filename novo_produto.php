<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Produto | GAZADA RP</title>
    <link rel="icon" type="image/png" href="images/BANNER_GAZADA_RP.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --primary: #ff0000; --bg: #050505; --card: #0c0c0c; --border: rgba(255,255,255,0.08); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { background: var(--bg); color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        
        .container { width: 100%; max-width: 800px; background: var(--card); border: 1px solid var(--border); border-radius: 32px; padding: 40px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
        
        .header { display: flex; align-items: center; gap: 20px; margin-bottom: 40px; }
        .header-info h2 { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; text-transform: uppercase; }
        .header-info p { color: #666; font-size: 14px; }
        
        .grid-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }

        .input-group { margin-bottom: 24px; }
        label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); margin-bottom: 8px; letter-spacing: 1px; }
        
        input, select, textarea { 
            width: 100%; background: #000; border: 1px solid var(--border); padding: 14px 18px; 
            border-radius: 16px; color: #fff; font-size: 14px; outline: none; transition: 0.3s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(255,0,0,0.1); }
        
        textarea { height: 100px; resize: none; }

        /* Estilo da Drop Zone Profissional */
        .drop-zone {
            background: #000; border: 2px dashed var(--border); border-radius: 20px;
            padding: 30px; text-align: center; cursor: pointer; transition: 0.3s;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 10px; margin-bottom: 30px; position: relative; overflow: hidden;
        }
        .drop-zone:hover { border-color: var(--primary); background: rgba(255, 0, 0, 0.02); }
        .drop-zone i { color: #444; }
        .drop-zone p { font-size: 13px; color: #666; font-weight: 500; }
        
        #preview { 
            position: absolute; inset: 0; width: 100%; height: 100%; 
            object-fit: contain; background: #000; display: none; z-index: 5;
        }

        .actions { display: flex; gap: 15px; margin-top: 20px; }
        .btn { flex: 1; padding: 16px; border-radius: 16px; font-weight: 700; cursor: pointer; transition: 0.3s; border: none; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 10px; text-transform: uppercase; }
        .btn-save { background: var(--primary); color: #fff; }
        .btn-cancel { background: #1a1a1a; color: #888; text-decoration: none; }
        
        .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .btn-save:hover { background: #e60000; box-shadow: 0 10px 20px rgba(255,0,0,0.2); }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div style="background: var(--primary); padding: 12px; border-radius: 16px;">
            <i data-lucide="plus-circle" style="color: #fff;"></i>
        </div>
        <div class="header-info">
            <h2>Novo Item Global</h2>
            <p>Crie um produto que será listado em todas as páginas automaticamente.</p>
        </div>
    </div>

    <form id="productForm">
        <label>Imagem do Produto</label>
        <div class="drop-zone" id="dropZone">
            <i data-lucide="image-plus" size="32"></i>
            <p id="dropText">Arraste a imagem ou clique para carregar</p>
            <img id="preview" src="">
            <input type="file" id="fileInput" hidden accept="image/*">
        </div>

        <div class="grid-inputs">
            <div class="input-group">
                <label>ID Único (ex: vip_ouro_01)</label>
                <input type="text" id="pid" placeholder="Use letras e números sem espaços" required>
            </div>

            <div class="input-group">
                <label>Preço em Moeda Real (€)</label>
                <input type="number" step="0.01" id="pprice" placeholder="0.00" required>
            </div>

            <div class="input-group">
                <label>Nome Exibido</label>
                <input type="text" id="pname" placeholder="Ex: VIP OURO MENSAL" required>
            </div>

            <div class="input-group">
                <label>Categoria</label>
                <select id="pcat">
                    <option value="coins">💰 Moedas / Coins</option>
                    <option value="vip_mensal">📅 VIP Mensal</option>
                    <option value="vip_exclusivo">💎 VIP Exclusivo</option>
                    <option value="extras">➕ Extras</option>
                </select>
            </div>

            <div class="input-group full-width">
                <label>Vantagens (uma por linha)</label>
                <textarea id="pfeatures" placeholder="Prioridade na Fila&#10;Salário +20%&#10;Skin Exclusiva"></textarea>
            </div>
        </div>

        <div class="actions">
            <a href="painel.php" class="btn btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-save" id="submitBtn">
                <i data-lucide="upload-cloud" size="18"></i> Publicar Produto
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();

    // Lógica de Upload e Preview
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const dropText = document.getElementById('dropText');
    
    dropZone.onclick = () => fileInput.click();
    
    fileInput.onchange = (e) => handleFile(e.target.files[0]);
    
    dropZone.ondragover = (e) => { 
        e.preventDefault(); 
        dropZone.style.borderColor = "#ff0000"; 
    };
    
    dropZone.ondragleave = () => { 
        dropZone.style.borderColor = "rgba(255,255,255,0.08)"; 
    };

    dropZone.ondrop = (e) => { 
        e.preventDefault(); 
        dropZone.style.borderColor = "rgba(255,255,255,0.08)";
        handleFile(e.dataTransfer.files[0]); 
    };

    function handleFile(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => { 
                preview.src = e.target.result; 
                preview.style.display = 'block'; 
                dropText.style.display = 'none';
            };
            reader.readAsDataURL(file);
            
            // Atribuir o ficheiro ao input para o FormData apanhar
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        }
    }

    // Submissão do Formulário
    document.getElementById('productForm').onsubmit = async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = "A publicar...";
        btn.style.opacity = "0.7";
        btn.disabled = true;

        const formData = new FormData();
        formData.append('id', document.getElementById('pid').value);
        formData.append('name', document.getElementById('pname').value);
        formData.append('category', document.getElementById('pcat').value);
        formData.append('price_coins', document.getElementById('pprice').value);
        formData.append('features', document.getElementById('pfeatures').value);
        
        if (fileInput.files[0]) {
            formData.append('image_file', fileInput.files[0]);
        }

        try {
            const res = await fetch('save_product.php', { method: 'POST', body: formData });
            
            if(res.ok) {
                alert("Sucesso! O produto foi adicionado ao catálogo.");
                window.location.href = 'painel.php'; 
            } else {
                alert("Erro ao salvar o produto.");
                btn.innerHTML = '<i data-lucide="upload-cloud" size="18"></i> Publicar Produto';
                btn.style.opacity = "1";
                btn.disabled = false;
            }
        } catch (error) {
            console.error("Erro:", error);
            alert("Ocorreu um erro de conexão.");
            btn.disabled = false;
        }
    };
</script>

</body>
</html>