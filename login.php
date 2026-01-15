<?php 
session_start(); 

// --- Sistema de Alertas ---
$mensagem = "";
$status = ""; 
$face = "login"; 

if (isset($_GET['error'])) {
    $status = "error";
    $face = $_GET['form'] ?? "login";
    $errors = [
        'empty' => 'Preencha todos os campos.',
        'invalid' => 'Utilizador ou senha incorretos.',
        'exists' => 'Este registo já existe.',
        'short_pass' => 'Senha muito curta (mín. 6).',
        'db' => 'Erro de conexão.',
        'email' => 'Email inválido.'
    ];
    $mensagem = $errors[$_GET['error']] ?? "Erro desconhecido.";
}

if (isset($_GET['success'])) {
    $status = "success";
    $mensagem = "Conta criada com sucesso!";
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAZADA • Autenticação</title>
     <link rel="icon" type="image/png" href="images/BANNER_GAZADA_RP.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #ff0000;
            --primary-glow: rgba(255, 0, 0, 0.4);
            --card-bg: rgba(13, 13, 13, 0.98);
            --input-bg: #0a0a0a;
            --border: rgba(255, 255, 255, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body {
            background: radial-gradient(circle at center, #1a0000 0%, #050000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }

        /* --- NAVBAR --- */
        .navbar {
            position: fixed; top: 20px;
            width: 90%; max-width: 1100px; height: 65px;
            background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(10px);
            border: 1px solid var(--border); border-radius: 15px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 30px; z-index: 1000;
        }
        .nav-brand img { height: 80px; filter: drop-shadow(0 0 10px var(--primary)); }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 700; font-size: 13px; text-transform: uppercase; transition: 0.3s; }
        .nav-links a:hover, .nav-links .active { color: var(--primary); }

        /* Botão Discord Animado e Vermelho */
.btn-discord {
    background: var(--primary);
    color: white;
    text-decoration: none;
    padding: 12px 25px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
    transition: 0.4s;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 15px var(--primary-glow);
    animation: pulseRed 2s infinite; /* Animação de pulsação */
}

.btn-discord:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 0 25px var(--primary);
    filter: brightness(1.2);
}

/* Efeito de brilho que passa pelo botão */
.btn-discord::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -60%;
    width: 20%;
    height: 200%;
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(30deg);
    transition: 0.5s;
    animation: shine 3s infinite;
}

/* Keyframes para a animação */
@keyframes pulseRed {
    0% { box-shadow: 0 0 10px var(--primary-glow); }
    50% { box-shadow: 0 0 20px var(--primary); }
    100% { box-shadow: 0 0 10px var(--primary-glow); }
}

@keyframes shine {
    0% { left: -60%; }
    20% { left: 120%; }
    100% { left: 120%; }
}
        /* --- CARD ORGANIZADO --- */
        .perspective { perspective: 2000px; width: 400px; }
        .card-inner {
            position: relative; width: 100%; min-height: 580px;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        .is-flipped { transform: rotateY(180deg); }

        .card-face {
            position: absolute; inset: 0; backface-visibility: hidden;
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 30px; padding: 40px;
            display: flex; flex-direction: column;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        }
        .card-face-back { transform: rotateY(180deg); }

        /* Alert System */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px; border-radius: 12px; margin-bottom: 20px;
            font-size: 13px; font-weight: 600;
            animation: fadeIn 0.3s ease;
        }
        .alert-error { background: rgba(255, 0, 0, 0.1); border: 1px solid rgba(255, 0, 0, 0.2); color: #ff5555; }
        .alert-success { background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.2); color: #00ff88; }

        .header { margin-bottom: 30px; }
        .header h2 { font-size: 28px; font-weight: 800; }
        .header p { color: #555; font-size: 14px; margin-top: 5px; }

        /* INPUTS AGRUPADOS (CORREÇÃO DA SUA IMAGEM) */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; font-weight: 800; color: #444; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
        
        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-container i {
            position: absolute;
            left: 15px;
            color: #333;
            width: 18px;
            transition: 0.3s;
        }

        .input-container input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid #1a1a1a;
            padding: 15px 15px 15px 45px; /* Espaço para o ícone */
            border-radius: 12px;
            color: white;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .input-container input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.1);
        }

        .input-container input:focus + i {
            color: var(--primary);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(255, 0, 0, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
            box-shadow: 0 15px 30px rgba(255, 0, 0, 0.4);
        }

        .footer-link {
            margin-top: auto;
            text-align: center;
            font-size: 13px;
            color: #444;
        }

        .footer-link span {
            color: #888;
            cursor: pointer;
            font-weight: 700;
            transition: 0.2s;
        }

        .footer-link span:hover { color: var(--primary); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-brand"><img src="images/BANNER_GAZADA_RP.png" alt="Logo"></div>
    <ul class="nav-links">
        <li><a href="index.html" class="active">Início</a></li>
        <li><a href="vips.html">Vips</a></li>
    </ul>
    <div class="nav-right">
        <a href="URL_DO_TEU_DISCORD" class="btn-discord" target="_blank">
            <i data-lucide="message-square"></i> 
            DISCORD
        </a>
    </div>
</nav>

    <div class="perspective">
        <div class="card-inner" id="card">
            
            <div class="card-face">
                <div class="header">
                    <h2>Login</h2>
                    <p>Bem-vindo de volta à cidade.</p>
                </div>

                <?php if ($mensagem && ($face == 'login' || $status == 'success')): ?>
                    <div class="alert alert-<?php echo $status; ?>">
                        <i data-lucide="<?php echo $status == 'error' ? 'alert-circle' : 'check'; ?>"></i>
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <form action="auth.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label>Utilizador</label>
                        <div class="input-container">
                            <input type="text" name="utilizador" placeholder="Teu nome na cidade" required>
                            <i data-lucide="user"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <div class="input-container">
                            <input type="password" name="password" placeholder="••••••••" required>
                            <i data-lucide="key"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Entrar Agora</button>
                </form>

                <div class="footer-link">Novo por aqui? <span onclick="toggleCard()">Cria a tua conta</span></div>
            </div>

            <div class="card-face card-face-back">
                <div class="header">
                    <h2>Registo</h2>
                    <p>Cria a tua nova identidade.</p>
                </div>

                <?php if ($mensagem && $face == 'register' && $status == 'error'): ?>
                    <div class="alert alert-error">
                        <i data-lucide="alert-circle"></i>
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <form action="auth.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label>Nickname</label>
                        <div class="input-container">
                            <input type="text" name="utilizador" placeholder="Ex: Gazada_Player" required>
                            <i data-lucide="user-plus"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-container">
                            <input type="email" name="email" placeholder="teu@email.com" required>
                            <i data-lucide="mail"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <div class="input-container">
                            <input type="password" name="password" placeholder="Mín. 6 caracteres" required>
                            <i data-lucide="shield-check"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Criar conta</button>
                </form>

                <div class="footer-link">Já tens conta? <span onclick="toggleCard()">Voltar ao Login</span></div>
            </div>

        </div>
    </div>

    <script>
        lucide.createIcons();
        function toggleCard() { document.getElementById('card').classList.toggle('is-flipped'); }
        
        // Mantém a face correta se houver erro
        <?php if ($face == 'register' && $status == 'error'): ?>
            document.getElementById('card').classList.add('is-flipped');
        <?php endif; ?>
    </script>
</body>
</html>