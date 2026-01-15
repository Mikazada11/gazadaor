<?php
require_once 'config.php';

$id = $_GET['id'] ?? '';
$produto = null;

// Carregar dados reais do JSON para preencher o formulário
if (!empty($id)) {
    $jsonFile = 'produtos1.json';
    if (file_exists($jsonFile)) {
        $produtos = json_decode(file_get_contents($jsonFile), true);
        foreach ($produtos as $p) {
            if ($p['id'] == $id) {
                $produto = $p;
                break;
            }
        }
    }
}

// Se o produto não for encontrado, volta ao painel
if (!$produto) {
    header('Location: painel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto | GAZADA RP</title>
    <link rel="icon" type="image/png" href="images/BANNER_GAZADA_RP.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --primary: #ff0000; --bg: #050505; --card: #0c0c0c; --border: rgba(255,255,255,0.08); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { background: var(--bg); color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        
        .container { width: 100%; max-width: 800px; background: var(--card); border: 1px solid var(--border); border-radius: 32px; padding: 40px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
        
        .header { display: flex; align-items: center; gap: 20px; margin-bottom: 40px; }
        .header-info h2 { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header-info p { color: #666; font-size: 14px; }
        
        .grid-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }

        .input-group { margin-bottom: 24px; }
        label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #ff0000; margin-bottom: 8px; letter-spacing: 1px; }
        
        input, select, textarea { 
            width: 100%; background: #000; border: 1px solid var(--border); padding: 14px 18px; 
            border-radius: 16px; color: #fff; font-size: 14px; outline: none; transition: 0.3s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(255,0,0,0.1); }
        
        textarea { height: 120px; resize: none; }

        .img-preview-box {
            background: #000; border: 2px dashed var(--border); border-radius: 20px;
            padding: 20px; display: flex; align-items: center; gap: 20px; margin-bottom: 30px;
        }
        .img-preview-box img { width: 80px; height: 80px; object-fit: contain; border-radius: 12px; background: #050505; }
        
        .actions { display: flex; gap: 15px; margin-top: 20px; }
        .btn { flex: 1; padding: 16px; border-radius: 16px; font-weight: 700; cursor: pointer; transition: 0.3s; border: none; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-save { background: var(--primary); color: #fff; }
        .btn-cancel { background: #1a1a1a; color: #888; text-decoration: none; }
        
        .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .btn-save:hover { background: #e60000; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div style="background: var(--primary); padding: 12px; border-radius: 16px;">
            <i data-lucide="package-check" style="color: #fff;"></i>
        </div>
        <div class="header-info">
            <h2>Editar Produto</h2>
            <p>Alterar as informações do item ID: #<?= htmlspecialchars($id) ?></p>
        </div>
    </div>

    <form id="editForm">
        <input type="hidden" name="id" id="pid" value="<?= htmlspecialchars($id) ?>">

        <div class="img-preview-box">
            <img src="<?= $produto['image'] ?>" id="currentImg" onerror="this.src='images/BANNER_GAZADA_RP.png'">
            <div style="flex: 1;">
                <label>Alterar Imagem (Opcional)</label>
                <input type="file" id="pfile" style="border: none; padding: 0; margin: 0;">
            </div>
        </div>

        <div class="grid-inputs">
            <div class="input-group">
                <label>Nome do Produto</label>
                <input type="text" id="pname" value="<?= htmlspecialchars($produto['name']) ?>" required>
            </div>

            <div class="input-group">
                <label>Categoria</label>
                <select id="pcat">
                    <option value="coins" <?= $produto['category'] == 'coins' ? 'selected' : '' ?>>💰 Moedas/Coins</option>
                    <option value="vip_mensal" <?= $produto['category'] == 'vip_mensal' ? 'selected' : '' ?>>📅 VIP Mensal</option>
                    <option value="vip_exclusivo" <?= $produto['category'] == 'vip_exclusivo' ? 'selected' : '' ?>>💎 VIP Exclusivo</option>
                    <option value="extras" <?= $produto['category'] == 'extras' ? 'selected' : '' ?>>➕ Extras</option>
                </select>
            </div>

            <div class="input-group">
                <label>Preço em Moeda Real (€)</label>
                <input type="number" step="0.01" id="pprice" value="<?= $produto['price_coins'] ?>" required>
            </div>

            <div class="input-group full-width">
                <label>Vantagens (Uma por linha)</label>
                <textarea id="pfeatures" placeholder="Ex: Prioridade na Fila&#10;Salário Extra"><?= htmlspecialchars($produto['features']) ?></textarea>
            </div>
        </div>

        <div class="actions">
            <a href="painel.php" class="btn btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-save">
                <i data-lucide="save" size="18"></i> Guardar Alterações
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();

    document.getElementById('editForm').onsubmit = async (e) => {
        e.preventDefault();
        
        const btn = e.target.querySelector('.btn-save');
        btn.innerHTML = "A guardar...";
        btn.style.opacity = "0.7";

        const formData = new FormData();
        formData.append('id', document.getElementById('pid').value);
        formData.append('name', document.getElementById('pname').value);
        formData.append('category', document.getElementById('pcat').value);
        formData.append('price_coins', document.getElementById('pprice').value);
        formData.append('features', document.getElementById('pfeatures').value);
        
        const fileInput = document.getElementById('pfile');
        if (fileInput.files[0]) {
            formData.append('image_file', fileInput.files[0]);
        }

        try {
            const res = await fetch('save_product.php', { method: 'POST', body: formData });
            if (res.ok) {
                alert("Produto atualizado com sucesso!");
                window.location.href = 'painel.php';
            } else {
                alert("Erro ao atualizar.");
                btn.innerHTML = "Guardar Alterações";
                btn.style.opacity = "1";
            }
        } catch (error) {
            alert("Erro de conexão.");
            console.error(error);
        }
    };
</script>

</body>
</html>