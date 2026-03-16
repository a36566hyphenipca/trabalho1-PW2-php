<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('gestor');

$pdo  = getDB();
$msg  = '';
$erro = '';

// CRIAR utilizador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar'])) {
    $nome     = trim($_POST['nome']     ?? '');
    $mail     = trim($_POST['mail']     ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = $_POST['role'] ?? '';

    if (!$nome || !$mail || !$password || !$role) {
        $erro = 'Preenche todos os campos.';
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (strlen($password) < 6) {
        $erro = 'A password deve ter pelo menos 6 caracteres.';
    } elseif (!in_array($role, ['aluno', 'funcionario', 'gestor'])) {
        $erro = 'Perfil inválido.';
    } else {
        $chk = $pdo->prepare('SELECT ID FROM `users` WHERE `mail` = ? LIMIT 1');
        $chk->execute([$mail]);
        if ($chk->fetch()) {
            $erro = 'Este email já está registado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO `users` (`nome`, `mail`, `password`, `role`) VALUES (?,?,?,?)')->execute([$nome, $mail, $hash, $role]);

            if ($role === 'aluno') {
                $pdo->prepare('INSERT INTO `alunos` (`nome`, `mail`) VALUES (?,?)')->execute([$nome, $mail]);
            } elseif ($role === 'funcionario') {
                $pdo->prepare('INSERT INTO `foncionario` (`nome`, `mail`) VALUES (?,?)')->execute([$nome, $mail]);
            } elseif ($role === 'gestor') {
                $pdo->prepare('INSERT INTO `gestor pedagogico` (`nome`, `mail`) VALUES (?,?)')->execute([$nome, $mail]);
            }

            $msg = ucfirst($role) . ' "' . htmlspecialchars($nome) . '" criado com sucesso!';
        }
    }
}

// REMOVER utilizador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover'])) {
    $id = (int)$_POST['user_id'];

    // Não deixar apagar o próprio gestor logado
    if ($id === (int)$_SESSION['user_id']) {
        $erro = 'Não podes remover a tua própria conta!';
    } else {
        // Buscar o utilizador para saber o role e o mail
        $su = $pdo->prepare('SELECT * FROM `users` WHERE `ID`=? LIMIT 1');
        $su->execute([$id]);
        $u = $su->fetch();

        if ($u) {
            // Remover da tabela do perfil
            if ($u['role'] === 'aluno') {
                $pdo->prepare('DELETE FROM `alunos` WHERE `mail`=?')->execute([$u['mail']]);
            } elseif ($u['role'] === 'funcionario') {
                $pdo->prepare('DELETE FROM `foncionario` WHERE `mail`=?')->execute([$u['mail']]);
            } elseif ($u['role'] === 'gestor') {
                $pdo->prepare('DELETE FROM `gestor pedagogico` WHERE `mail`=?')->execute([$u['mail']]);
            }
            // Remover da tabela users
            $pdo->prepare('DELETE FROM `users` WHERE `ID`=?')->execute([$id]);
            $msg = 'Utilizador "' . htmlspecialchars($u['nome']) . '" removido com sucesso!';
        }
    }
}

// Listar todos os utilizadores
$users = $pdo->query("SELECT * FROM `users` ORDER BY `role`, `nome`")->fetchAll();

layout_head('Gerir Utilizadores');
layout_nav('Utilizadores', '/trabalho1-PHP/dashboard/gestor.php');
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>

<h1>Gerir Utilizadores</h1>
<p class="subtitulo">Cria e remove utilizadores do sistema.</p>

<!-- Formulário criar -->
<div class="card">
    <h2>➕ Novo Utilizador</h2>
    <form method="POST">
        <div class="grid-2">
            <div class="campo">
                <label>Nome completo</label>
                <input type="text" name="nome" required placeholder="Ex: João Silva"
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
            </div>
            <div class="campo">
                <label>Email</label>
                <input type="email" name="mail" required placeholder="email@exemplo.pt"
                       value="<?= htmlspecialchars($_POST['mail'] ?? '') ?>">
            </div>
            <div class="campo">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Mín. 6 caracteres">
            </div>
            <div class="campo">
                <label>Perfil</label>
                <select name="role" required>
                    <option value="">-- Seleciona --</option>
                    <option value="aluno"       <?= ($_POST['role']??'')==='aluno'       ?'selected':'' ?>>🎓 Aluno</option>
                    <option value="funcionario" <?= ($_POST['role']??'')==='funcionario' ?'selected':'' ?>>🏛️ Funcionário</option>
                    <option value="gestor"      <?= ($_POST['role']??'')==='gestor'      ?'selected':'' ?>>📋 Gestor</option>
                </select>
            </div>
        </div>
        <div class="btn-group">
            <button type="submit" name="criar" class="btn btn-primary">➕ Criar Utilizador</button>
        </div>
    </form>
</div>

<!-- Lista utilizadores -->
<div class="card">
    <h2>👥 Todos os Utilizadores (<?= count($users) ?>)</h2>
    <div class="search-wrap">
        <input type="text" id="srch" placeholder="🔍 Pesquisar por nome, email ou perfil..." oninput="filtrar()">
    </div>
    <?php if (empty($users)): ?>
        <p style="color:var(--texto2)">Nenhum utilizador.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table id="tbl">
            <thead>
                <tr><th>#</th><th>Nome</th><th>Email</th><th>Perfil</th><th>Ação</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u):
                $badge = match($u['role']) { 'aluno'=>'b-indigo', 'funcionario'=>'b-amber', 'gestor'=>'b-emerald', default=>'b-slate' };
                $icon  = match($u['role']) { 'aluno'=>'🎓', 'funcionario'=>'🏛️', 'gestor'=>'📋', default=>'👤' };
                $isMe  = ($u['ID'] == $_SESSION['user_id']);
            ?>
                <tr>
                    <td><?= $u['ID'] ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?> <?= $isMe ? '<span class="badge b-slate" style="font-size:.68rem">Tu</span>' : '' ?></td>
                    <td><?= htmlspecialchars($u['mail']) ?></td>
                    <td><span class="badge <?= $badge ?>"><?= $icon ?> <?= ucfirst($u['role']) ?></span></td>
                    <td>
                        <?php if (!$isMe): ?>
                            <form method="POST" onsubmit="return confirm('Tens a certeza que queres remover <?= htmlspecialchars($u['nome']) ?>?')">
                                <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                <button type="submit" name="remover" class="btn btn-danger" style="font-size:.78rem;padding:.35rem .8rem">🗑️ Remover</button>
                            </form>
                        <?php else: ?>
                            <span style="color:var(--texto2);font-size:.8rem">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
</div>

<script>
function filtrar() {
    const v = document.getElementById('srch').value.toLowerCase();
    document.querySelectorAll('#tbl tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(v) ? '' : 'none';
    });
}
</script>
<?php layout_foot(); ?>
