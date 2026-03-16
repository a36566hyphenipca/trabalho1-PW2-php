<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao(); requireLogin();
$pdo=getDB(); $msg=$erro='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $atual=$_POST['p_atual']??''; $nova=$_POST['p_nova']??''; $conf=$_POST['p_conf']??'';
    $s=$pdo->prepare('SELECT * FROM `users` WHERE `ID`=? LIMIT 1'); $s->execute([$_SESSION['user_id']]); $u=$s->fetch();
    if (!password_verify($atual,$u['password'])) $erro='Password atual incorreta.';
    elseif (strlen($nova)<6) $erro='Mínimo 6 caracteres.';
    elseif ($nova!==$conf) $erro='Passwords não coincidem.';
    else { $pdo->prepare('UPDATE `users` SET `password`=? WHERE `ID`=?')->execute([password_hash($nova,PASSWORD_DEFAULT),$_SESSION['user_id']]); $msg='Password alterada!'; }
}

layout_head('Mudar Password');
layout_nav('Mudar Password', getDashboardUrl());
?>
<div class="page" style="max-width:500px">
<h1>Mudar Password</h1><p class="subtitulo">Altera a tua password de acesso.</p>
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
<div class="card"><form method="POST">
    <div class="campo"><label>Password atual</label><input type="password" name="p_atual" required placeholder="••••••••"></div>
    <div class="campo"><label>Nova password</label><input type="password" name="p_nova" required placeholder="Mín. 6 caracteres"></div>
    <div class="campo"><label>Confirmar nova password</label><input type="password" name="p_conf" required placeholder="Repete a nova password"></div>
    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:.5rem">🔒 Alterar Password</button>
</form></div>
</div>
<?php layout_foot(); ?>
