<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('aluno', 'funcionario');

$pdo    = getDB();
$perfil = obterPerfil();
$msg    = '';
$erro   = '';

if ($perfil === 'aluno') {
    $stmtA = $pdo->prepare('SELECT * FROM `alunos` WHERE `mail`=? LIMIT 1');
    $stmtA->execute([$_SESSION['mail']]);
    $aluno = $stmtA->fetch();
    $cursos = $pdo->query("SELECT * FROM `curso` WHERE `ativo`=1 ORDER BY nome")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_pedido'])) {
        $curso_id = (int)$_POST['curso_id'];
        if (!$aluno)     { $erro = 'Cria a tua ficha de aluno primeiro.'; }
        elseif (!$curso_id) { $erro = 'Seleciona um curso.'; }
        else {
            $chk = $pdo->prepare("SELECT ID FROM `pedido matricula` WHERE `aluno ID`=? AND `curso ID`=? AND `estado`='pendente'");
            $chk->execute([$aluno['ID'], $curso_id]);
            if ($chk->fetch()) { $erro = 'Já tens um pedido pendente para este curso.'; }
            else {
                $pdo->prepare("INSERT INTO `pedido matricula` (`aluno ID`, `curso ID`, `estado`, `data`) VALUES (?,?,'pendente',NOW())")->execute([$aluno['ID'], $curso_id]);
                $msg = 'Pedido enviado com sucesso!';
            }
        }
    }

    $pedidos = [];
    if ($aluno) {
        $sp = $pdo->prepare("SELECT pm.*, c.nome AS curso_nome FROM `pedido matricula` pm JOIN `curso` c ON c.ID=pm.`curso ID` WHERE pm.`aluno ID`=? ORDER BY pm.ID DESC");
        $sp->execute([$aluno['ID']]);
        $pedidos = $sp->fetchAll();
    }
}

if ($perfil === 'funcionario') {
    $stmtF = $pdo->prepare('SELECT * FROM `foncionario` WHERE `mail`=? LIMIT 1');
    $stmtF->execute([$_SESSION['mail']]);
    $func = $stmtF->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decisao'])) {
        $pedido_id   = (int)$_POST['pedido_id'];
        $decisao     = $_POST['decisao'] === 'aprovar' ? 'aprovado' : 'rejeitado';
        $observacoes = trim($_POST['observacoes'] ?? '');
        $func_id     = $func ? $func['ID'] : null;
        $pdo->prepare("UPDATE `pedido matricula` SET `estado`=?, `observações`=?, `foncionario ID`=?, `data`=NOW() WHERE ID=?")
            ->execute([$decisao, $observacoes, $func_id, $pedido_id]);
        $msg = 'Pedido ' . ($decisao === 'aprovado' ? 'aprovado' : 'rejeitado') . '!';
    }

    $pedidos = $pdo->query("
        SELECT pm.*, a.nome AS aluno_nome, a.mail AS aluno_mail, c.nome AS curso_nome
        FROM `pedido matricula` pm
        JOIN `alunos` a ON a.ID = pm.`aluno ID`
        JOIN `curso` c ON c.ID = pm.`curso ID`
        ORDER BY FIELD(pm.estado,'pendente','aprovado','rejeitado'), pm.ID DESC
    ")->fetchAll();
}

layout_head('Pedidos de Matrícula');
layout_nav('Pedidos de Matrícula', getDashboardUrl());
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>

<?php if ($perfil === 'aluno'): ?>
    <h1>Pedido de Matrícula</h1>
    <p class="subtitulo">Submete um pedido de inscrição num curso.</p>

    <div class="card">
        <h2>➕ Novo Pedido</h2>
        <form method="POST">
            <div class="campo">
                <label>Seleciona o curso</label>
                <select name="curso_id" required>
                    <option value="">-- Escolhe um curso --</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['ID'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="btn-group">
                <button type="submit" name="criar_pedido" class="btn btn-primary">🚀 Enviar Pedido</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>📋 Os Meus Pedidos</h2>
        <?php if (empty($pedidos)): ?>
            <p style="color:var(--texto2)">Ainda não tens pedidos.</p>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Curso</th><th>Estado</th><th>Data</th><th>Observações</th></tr></thead>
                <tbody>
                <?php foreach ($pedidos as $p):
                    $bc = match($p['estado']) { 'aprovado'=>'b-emerald','rejeitado'=>'b-rose',default=>'b-amber' };
                ?>
                    <tr>
                        <td><?= htmlspecialchars($p['curso_nome']) ?></td>
                        <td><span class="badge <?= $bc ?>"><?= ucfirst($p['estado']) ?></span></td>
                        <td><?= $p['data'] ?></td>
                        <td><?= htmlspecialchars($p['observações'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>

<?php elseif ($perfil === 'funcionario'): ?>
    <h1>Pedidos de Matrícula</h1>
    <p class="subtitulo">Aprova ou rejeita os pedidos dos alunos.</p>

    <div class="card">
        <h2>📋 Todos os Pedidos</h2>
        <?php if (empty($pedidos)): ?>
            <p style="color:var(--texto2)">Nenhum pedido encontrado.</p>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Aluno</th><th>Email</th><th>Curso</th><th>Estado</th><th>Data</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($pedidos as $p):
                    $bc = match($p['estado']) { 'aprovado'=>'b-emerald','rejeitado'=>'b-rose',default=>'b-amber' };
                ?>
                    <tr>
                        <td><?= htmlspecialchars($p['aluno_nome']) ?></td>
                        <td><?= htmlspecialchars($p['aluno_mail']) ?></td>
                        <td><?= htmlspecialchars($p['curso_nome']) ?></td>
                        <td><span class="badge <?= $bc ?>"><?= ucfirst($p['estado']) ?></span></td>
                        <td><?= $p['data'] ?></td>
                        <td>
                            <?php if ($p['estado'] === 'pendente'): ?>
                                <details>
                                    <summary>Decidir</summary>
                                    <div class="form-decisao">
                                        <form method="POST">
                                            <input type="hidden" name="pedido_id" value="<?= $p['ID'] ?>">
                                            <textarea name="observacoes" placeholder="Observações (opcional)"></textarea>
                                            <div class="btn-group">
                                                <button type="submit" name="decisao" value="aprovar" class="btn btn-success">✅ Aprovar</button>
                                                <button type="submit" name="decisao" value="rejeitar" class="btn btn-danger">❌ Rejeitar</button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            <?php else: ?>
                                <small style="color:var(--texto2)"><?= htmlspecialchars($p['observações'] ?? '—') ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<?php layout_foot(); ?>
