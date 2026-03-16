<?php
require_once __DIR__ . '/auth/auth.php';
iniciarSessao();
if (estaLogado()) { header('Location: ' . getDashboardUrl()); exit; }

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail     = trim($_POST['mail']     ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($mail === '' || $password === '') {
        $erro = 'Preenche todos os campos.';
    } else {
        $user = fazerLogin($mail, $password);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['nome']    = $user['nome'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['mail']    = $user['mail'];
            header('Location: ' . getDashboardUrl());
            exit;
        } else {
            $erro = 'Email ou password incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistema Académico</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Sora',sans-serif; background:#f8fafc; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .container { display:flex; width:900px; max-width:95vw; min-height:520px; border-radius:20px; overflow:hidden; box-shadow:0 20px 60px rgba(15,23,42,.12); }
        .left { flex:1; background:#0f172a; padding:3rem; display:flex; flex-direction:column; justify-content:space-between; }
        .left-logo { display:flex; align-items:center; gap:.6rem; margin-bottom:2rem; }
        .left-logo .dot { width:10px; height:10px; border-radius:50%; background:#6366f1; }
        .left-logo span { color:#fff; font-weight:700; font-size:1.1rem; }
        .left h1 { font-size:2rem; font-weight:700; color:#fff; line-height:1.2; margin-bottom:.6rem; }
        .left p  { color:rgba(255,255,255,.5); font-size:.88rem; line-height:1.6; }
        .perfis { display:flex; flex-direction:column; gap:.6rem; margin-top:2rem; }
        .perfil-item { display:flex; align-items:center; gap:.8rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.09); border-radius:10px; padding:.65rem 1rem; }
        .perfil-item .pi-icon { font-size:1.1rem; }
        .perfil-item .pi-info strong { display:block; color:#fff; font-size:.83rem; }
        .perfil-item .pi-info span  { color:rgba(255,255,255,.45); font-size:.75rem; }
        .left-foot { color:rgba(255,255,255,.25); font-size:.75rem; margin-top:2rem; }
        .right { flex:1; background:#fff; padding:3rem; display:flex; flex-direction:column; justify-content:center; }
        .right h2 { font-size:1.8rem; font-weight:700; color:#0f172a; margin-bottom:.3rem; }
        .right .sub { color:#64748b; font-size:.88rem; margin-bottom:2rem; }
        .alerta-erro { background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; border-radius:9px; padding:.75rem 1rem; font-size:.85rem; margin-bottom:1.2rem; }
        .campo { margin-bottom:1rem; }
        .campo label { display:block; font-size:.8rem; font-weight:600; color:#0f172a; margin-bottom:.35rem; }
        .campo input { width:100%; padding:.7rem 1rem; border:1.5px solid #e2e8f0; border-radius:9px; font-size:.9rem; font-family:'Sora',sans-serif; outline:none; background:#f8fafc; transition:border-color .15s, box-shadow .15s; }
        .campo input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); background:#fff; }
        .btn-login { width:100%; padding:.8rem; background:#6366f1; color:#fff; border:none; border-radius:9px; font-size:.95rem; font-weight:600; font-family:'Sora',sans-serif; cursor:pointer; margin-top:.5rem; transition:opacity .15s, transform .12s; }
        .btn-login:hover { opacity:.9; transform:translateY(-1px); }
        @media(max-width:600px) { .left { display:none; } }
    </style>
</head>
<body>
<div class="container">
    <div class="left">
        <div>
            <div class="left-logo"><div class="dot"></div><span>Sistema Académico</span></div>
            <h1>Gestão<br>Académica</h1>
            <p>Plataforma de gestão de cursos, alunos, matrículas e avaliações.</p>
            <div class="perfis">
                <div class="perfil-item">
                    <div class="pi-icon">🎓</div>
                    <div class="pi-info"><strong>Aluno</strong><span>Ficha, matrícula e notas</span></div>
                </div>
                <div class="perfil-item">
                    <div class="pi-icon">🏛️</div>
                    <div class="pi-info"><strong>Funcionário</strong><span>Matrículas e pautas</span></div>
                </div>
                <div class="perfil-item">
                    <div class="pi-icon">📋</div>
                    <div class="pi-info"><strong>Gestor</strong><span>Cursos e planos de estudo</span></div>
                </div>
            </div>
        </div>
        <p class="left-foot">© 2025 Serviços Académicos</p>
    </div>
    <div class="right">
        <h2>Bem-vindo 👋</h2>
        <p class="sub">Inicia sessão para continuar</p>
        <?php if ($erro): ?><div class="alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <form method="POST" novalidate>
            <div class="campo">
                <label>Email</label>
                <input type="email" name="mail" placeholder="o_teu@email.com" value="<?= htmlspecialchars($_POST['mail'] ?? '') ?>" required autocomplete="email">
            </div>
            <div class="campo">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-login">Entrar →</button>
        </form>
    </div>
</div>
</body>
</html>
