
<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('aluno', 'gestor');
 
$pdo    = getDB();
$perfil = obterPerfil();
$msg    = '';
$erro   = '';
 
if ($perfil === 'aluno') {
    $stmtA = $pdo->prepare('SELECT * FROM `alunos` WHERE `mail`=? LIMIT 1');
    $stmtA->execute([$_SESSION['mail']]);
    $aluno = $stmtA->fetch();
 
    if (!$aluno) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_aluno'])) {
            $pdo->prepare('INSERT INTO `alunos` (`nome`, `mail`) VALUES (?,?)')->execute([$_SESSION['nome'], $_SESSION['mail']]);
            header('Location: fichaaluno.php'); exit;
        }
    } else {
        $stmtF = $pdo->prepare('SELECT * FROM `ficha aluno` WHERE `aluno ID`=? ORDER BY ID DESC LIMIT 1');
        $stmtF->execute([$aluno['ID']]);
        $ficha = $stmtF->fetch();
 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submeter'])) {
            if ($ficha) {
                $pdo->prepare("UPDATE `ficha aluno` SET `estado`='submetida', `data submissão`=NOW() WHERE ID=?")->execute([$ficha['ID']]);
            }
            header('Location: fichaaluno.php'); exit;
        }
 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
            $morada = trim($_POST['morada'] ?? '');
            $dataN  = trim($_POST['data_nascimento'] ?? '');
            $tel    = trim($_POST['telefone'] ?? '');
            $foto = $ficha['foto'] ?? '';
 
            if (!empty($_FILES['foto']['name'])) {
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png'])) {
                    $erro = 'Apenas JPG e PNG são aceites.';
                } elseif ($_FILES['foto']['size'] > 2*1024*1024) {
                    $erro = 'A foto não pode ter mais de 2MB.';
                } else {
                    $dir = __DIR__ . '/uploads/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $foto = 'aluno_' . $aluno['ID'] . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $foto);
                    $foto = 'uploads/' . $foto;
                }
            }
 
            if (!$erro) {
                $pdo->prepare('UPDATE `alunos` SET `morada`=?, `data nascimento`=?, `telefone`=? WHERE ID=?')
                    ->execute([$morada, $dataN ?: null, $tel, $aluno['ID']]);
                if ($ficha) {
                    $pdo->prepare("UPDATE `ficha aluno` SET `foto`=?, `estado`='rascunho' WHERE ID=?")->execute([$foto, $ficha['ID']]);
                } else {
                    $pdo->prepare("INSERT INTO `ficha aluno` (`aluno ID`, `foto`, `estado`) VALUES (?,?,'rascunho')")->execute([$aluno['ID'], $foto]);
                }
                header('Location: fichaaluno.php'); exit;
            }
        }
 
        $stmtA->execute([$_SESSION['mail']]); $aluno = $stmtA->fetch();
        $stmtF->execute([$aluno['ID']]); $ficha = $stmtF->fetch();
    }
}
 
if ($perfil === 'gestor') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decisao'])) {
        $ficha_id    = (int)$_POST['ficha_id'];
        $decisao     = $_POST['decisao'] === 'aprovar' ? 'aprovada' : 'rejeitada';
        $observações = trim($_POST[''] ?? '');
        $pdo->prepare("UPDATE `ficha aluno` SET `estado`=?, `observações`=?, `data validacao`=NOW() WHERE ID=?")
            ->execute([$decisao, $observações, $ficha_id]);
        $msg = 'Ficha ' . ($decisao === 'aprovada' ? 'aprovada' : 'rejeitada') . ' com sucesso!';
    }
    $fichas = $pdo->query("
        SELECT fa.*, a.nome AS aluno_nome, a.mail AS aluno_mail
        FROM `ficha aluno` fa
        JOIN `alunos` a ON a.ID = fa.`aluno ID`
        ORDER BY FIELD(fa.estado,'submetida','rascunho','aprovada','rejeitada'), fa.ID DESC
    ")->fetchAll();
}
 
layout_head('Ficha de Aluno');
layout_nav('Ficha de Aluno', getDashboardUrl());
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
 
<?php if ($perfil === 'aluno'): ?>
    <h1>A Minha Ficha</h1>
    <p class="subtitulo">Preenche os teus dados e submete para validação.</p>
 
    <?php if (!$aluno): ?>
        <div class="card">
            <h2>Primeiro acesso</h2>
            <p style="color:var(--texto2);margin-bottom:1rem">Ainda não tens ficha criada.</p>
            <form method="POST">
                <button name="criar_aluno" class="btn btn-primary">Criar ficha</button>
            </form>
        </div>
    <?php else:
        $estado    = $ficha['estado'] ?? 'rascunho';
        $bloqueado = in_array($estado, ['submetida', 'aprovada']);
        $badgeClass = match($estado) { 'aprovada'=>'b-emerald', 'rejeitada'=>'b-rose', 'submetida'=>'b-amber', default=>'b-slate' };
        $badgeText  = match($estado) { 'rascunho'=>'✏️ Rascunho', 'submetida'=>'⏳ Aguarda validação', 'aprovada'=>'✅ Aprovada', 'rejeitada'=>'❌ Rejeitada', default=>$estado };
    ?>
        <span class="badge <?= $badgeClass ?>" style="margin-bottom:1.2rem;display:inline-block"><?= $badgeText ?></span>
 
        <?php if ($estado === 'rejeitada' && !empty($ficha['observações'])): ?>
            <div class="alerta alerta-erro">💬 Motivo: <?= htmlspecialchars($ficha['observacoes']) ?></div>
        <?php endif; ?>
 
        <form method="POST" enctype="multipart/form-data">
            <div class="card">
                <h2>👤 Dados Pessoais</h2>
                <?php if (!empty($ficha['foto']) && file_exists(__DIR__.'/'.$ficha['foto'])): ?>
                    <img src="/trabalho1-PHP/<?= htmlspecialchars($ficha['foto']) ?>" alt="Foto" class="foto-preview">
                <?php endif; ?>
                <?php if (!$bloqueado): ?>
                    <div class="campo"><label>Fotografia (JPG/PNG, máx. 2MB)</label><input type="file" name="foto" accept=".jpg,.jpeg,.png"></div>
                <?php endif; ?>
                <div class="grid-2">
                    <div class="campo"><label>Nome completo</label><input type="text" value="<?= htmlspecialchars($aluno['nome']) ?>" readonly></div>
                    <div class="campo"><label>Email</label><input type="email" value="<?= htmlspecialchars($aluno['mail']) ?>" readonly></div>
                    <div class="campo"><label>Morada</label><input type="text" name="morada" value="<?= htmlspecialchars($aluno['morada'] ?? '') ?>" <?= $bloqueado?'readonly':'' ?> placeholder="Rua, cidade"></div>
                    <div class="campo"><label>Data de Nascimento</label><input type="date" name="data_nascimento" value="<?= htmlspecialchars($aluno['data nascimento'] ?? '') ?>" <?= $bloqueado?'readonly':'' ?>></div>
                    <div class="campo"><label>Telefone</label><input type="tel" name="telefone" value="<?= htmlspecialchars($aluno['telefone'] ?? '') ?>" <?= $bloqueado?'readonly':'' ?> placeholder="9xx xxx xxx"></div>
                </div>
            </div>
            <?php if (!$bloqueado): ?>
                <div class="btn-group">
                    <button type="submit" name="guardar" class="btn btn-ghost">💾 Guardar rascunho</button>
                    <?php if ($ficha): ?>
                        <button type="submit" name="submeter" class="btn btn-primary">🚀 Submeter para validação</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
 
<?php elseif ($perfil === 'gestor'): ?>
    <h1>Fichas de Alunos</h1>
    <p class="subtitulo">Valida ou rejeita as fichas submetidas pelos alunos.</p>
    <div class="card">
        <h2>📋 Fichas submetidas</h2>
        <?php if (empty($fichas)): ?>
            <p style="color:var(--texto2)">Nenhuma ficha submetida ainda.</p>
        <?php else: ?>
            <div class="table-wrap">
            <table>
                <thead><tr><th>Foto</th><th>Aluno</th><th>Email</th><th>Estado</th><th>Submissão</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($fichas as $f):
                    $bc = match($f['estado']) { 'aprovada'=>'b-emerald','rejeitada'=>'b-rose','submetida'=>'b-amber',default=>'b-slate' };
                ?>
                    <tr>
                        <td><?php if (!empty($f['foto']) && file_exists(__DIR__.'/'.$f['foto'])): ?><img src="/trabalho1-PHP/<?= htmlspecialchars($f['foto']) ?>" style="width:45px;height:45px;border-radius:50%;object-fit:cover;border:2px solid var(--indigo);"><?php else: ?><div style="width:45px;height:45px;border-radius:50%;background:var(--bg2);display:flex;align-items:center;justify-content:center;">👤</div><?php endif; ?></td>
                        <td><?= htmlspecialchars($f['aluno_nome']) ?></td>
                        <td><?= htmlspecialchars($f['aluno_mail']) ?></td>
                        <td><span class="badge <?= $bc ?>"><?= ucfirst($f['estado']) ?></span></td>
                        <td><?= $f['data submissão'] ?? '—' ?></td>
                        <td>
                            <?php if ($f['estado'] === 'submetida'): ?>
                                <details>
                                    <summary>Decidir</summary>
                                    <div class="form-decisao">
                                        <form method="POST">
                                            <input type="hidden" name="ficha_id" value="<?= $f['ID'] ?>">
                                            <textarea name="observações" placeholder="Observações (opcional)"></textarea>
                                            <div class="btn-group">
                                                <button type="submit" name="decisao" value="aprovar" class="btn btn-success">✅ Aprovar</button>
                                                <button type="submit" name="decisao" value="rejeitar" class="btn btn-danger">❌ Rejeitar</button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            <?php else: ?>
                                <small style="color:var(--texto2)"><?= htmlspecialchars($f['observações'] ?? '—') ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<?php layout_foot(); ?>
