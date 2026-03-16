<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('funcionario', 'aluno');

$pdo    = getDB();
$perfil = obterPerfil();
$msg    = '';
$erro   = '';

if ($perfil === 'funcionario') {
    $pauta_id = (int)($_GET['pauta_id'] ?? 0);
    if (!$pauta_id) { header('Location: /trabalho1-PHP/pautas.php'); exit; }

    $stmtP = $pdo->prepare("SELECT p.*, u.nome AS uc_nome FROM `pautas` p JOIN `uc` u ON u.ID=p.`UC ID` WHERE p.ID=?");
    $stmtP->execute([$pauta_id]);
    $pauta = $stmtP->fetch();
    if (!$pauta) die('Pauta não encontrada.');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
        foreach ($_POST['nota'] ?? [] as $nota_id => $valor) {
            $v = $valor === '' ? 0.0 : (float)str_replace(',', '.', $valor);
            if ($v < 0 || $v > 20) continue;
            $pdo->prepare("UPDATE `notas` SET `nota`=? WHERE ID=? AND `pauta ID`=?")->execute([$v, (int)$nota_id, $pauta_id]);
        }
        $msg = 'Notas guardadas!';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_aluno'])) {
        $aluno_id = (int)($_POST['aluno_id'] ?? 0);
        if ($aluno_id) {
            $chk = $pdo->prepare("SELECT ID FROM `notas` WHERE `pauta ID`=? AND `aluno ID`=?");
            $chk->execute([$pauta_id, $aluno_id]);
            if ($chk->fetch()) { $erro = 'Aluno já está na pauta.'; }
            else {
                $pdo->prepare("INSERT INTO `notas` (`pauta ID`, `aluno ID`, `nota`) VALUES (?,?,0.0)")->execute([$pauta_id, $aluno_id]);
                $msg = 'Aluno adicionado!';
            }
        }
    }

    $stmtN = $pdo->prepare("SELECT n.*, a.nome AS aluno_nome, a.mail AS aluno_mail FROM `notas` n JOIN `alunos` a ON a.ID=n.`aluno ID` WHERE n.`pauta ID`=? ORDER BY a.nome");
    $stmtN->execute([$pauta_id]);
    $notas = $stmtN->fetchAll();
    $todosAlunos = $pdo->query("SELECT ID, nome FROM `alunos` ORDER BY nome")->fetchAll();
}

if ($perfil === 'aluno') {
    $stmtA = $pdo->prepare("SELECT * FROM `alunos` WHERE `mail`=? LIMIT 1");
    $stmtA->execute([$_SESSION['mail']]);
    $aluno = $stmtA->fetch();

    $minhasNotas = [];
    $aprovadas = $reprovadas = $pendentes = 0;

    if ($aluno) {
        $stmt = $pdo->prepare("
            SELECT n.nota, n.ID as nota_id, u.nome AS uc_nome, p.`ano letivo`, p.epoca
            FROM `notas` n
            JOIN `pautas` p ON p.ID = n.`pauta ID`
            JOIN `uc` u ON u.ID = p.`UC ID`
            WHERE n.`aluno ID` = ?
            ORDER BY u.nome, p.`ano letivo`
        ");
        $stmt->execute([$aluno['ID']]);
        $minhasNotas = $stmt->fetchAll();

        foreach ($minhasNotas as $n) {
            if ($n['nota'] === null)    $pendentes++;
            elseif ($n['nota'] >= 10)  $aprovadas++;
            else                        $reprovadas++;
        }
    }

    $total = count($minhasNotas);
    $pct   = $total > 0 ? round(($aprovadas / $total) * 100) : 0;
    $circ  = 220;
    $offset = $circ - ($circ * $pct / 100);
}

layout_head('Notas');
layout_nav('Notas', getDashboardUrl());
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>

<?php if ($perfil === 'funcionario'): ?>
    <h1>Lançar Notas</h1>
    <p class="subtitulo">Edita as notas dos alunos nesta pauta.</p>

    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
        <div style="background:var(--bg2);border-radius:9px;padding:.55rem 1rem;font-size:.83rem"><strong>UC:</strong> <?= htmlspecialchars($pauta['uc_nome']) ?></div>
        <div style="background:var(--bg2);border-radius:9px;padding:.55rem 1rem;font-size:.83rem"><strong>Ano:</strong> <?= htmlspecialchars($pauta['ano letivo']) ?></div>
        <div style="background:var(--bg2);border-radius:9px;padding:.55rem 1rem;font-size:.83rem"><strong>Época:</strong> <?= ucfirst($pauta['epoca']) ?></div>
    </div>

    <div class="card">
        <h2>➕ Adicionar Aluno Manualmente</h2>
        <form method="POST">
            <div class="campo">
                <label>Selecionar Aluno</label>
                <select name="aluno_id" required>
                    <option value="">-- Seleciona --</option>
                    <?php foreach ($todosAlunos as $a): ?>
                        <option value="<?= $a['ID'] ?>"><?= htmlspecialchars($a['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="btn-group">
                <button type="submit" name="adicionar_aluno" class="btn btn-ghost">➕ Adicionar</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>📋 Alunos e Notas (0–20)</h2>
        <?php if (empty($notas)): ?>
            <p style="color:var(--texto2)">Nenhum aluno nesta pauta ainda.</p>
        <?php else: ?>
            <form method="POST">
                <div class="table-wrap"><table>
                    <thead><tr><th>#</th><th>Aluno</th><th>Email</th><th>Nota (0–20)</th></tr></thead>
                    <tbody>
                    <?php foreach ($notas as $n): ?>
                        <tr>
                            <td><?= $n['ID'] ?></td>
                            <td><?= htmlspecialchars($n['aluno_nome']) ?></td>
                            <td><?= htmlspecialchars($n['aluno_mail']) ?></td>
                            <td>
                                <input type="number" name="nota[<?= $n['ID'] ?>]"
                                       min="0" max="20" step="0.1"
                                       value="<?= $n['nota'] ?? '0' ?>"
                                       style="width:80px;padding:.4rem .6rem;border:1.5px solid var(--borda2);border-radius:8px;font-size:.88rem;text-align:center;font-family:'Sora',sans-serif;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
                <div class="btn-group" style="margin-top:1.2rem">
                    <button type="submit" name="guardar" class="btn btn-primary">💾 Guardar Notas</button>
                    <a href="/trabalho1-PHP/pautas.php" class="btn btn-ghost">← Voltar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

<?php elseif ($perfil === 'aluno'): ?>
    <h1>As Minhas Notas</h1>
    <p class="subtitulo">Progresso e histórico de avaliações.</p>

    <!-- Círculo de progresso -->
    <div class="prog-global">
        <div class="prog-circle">
            <svg viewBox="0 0 80 80" width="76" height="76">
                <circle class="circle-bg" cx="40" cy="40" r="35" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="8"/>
                <circle cx="40" cy="40" r="35" fill="none" stroke="#6366f1" stroke-width="8" stroke-linecap="round"
                    stroke-dasharray="<?= $circ ?>" stroke-dashoffset="<?= $offset ?>" style="transform:rotate(-90deg);transform-origin:center;transition:stroke-dashoffset 1s ease"/>
            </svg>
            <div class="prog-pct-text"><?= $pct ?>%</div>
        </div>
        <div class="prog-info">
            <h2>Progresso do Ano Letivo</h2>
            <p><?= $total ?> unidade(s) curricular(es) registada(s)</p>
            <div class="prog-pills">
                <span class="ppill pp-g"><?= $aprovadas ?> aprovada(s)</span>
                <span class="ppill pp-r"><?= $reprovadas ?> reprovada(s)</span>
                <span class="ppill pp-s"><?= $pendentes ?> pendente(s)</span>
            </div>
        </div>
    </div>

    <!-- Barras por UC -->
    <?php if (empty($minhasNotas)): ?>
        <div class="card"><p style="color:var(--texto2)">Ainda não tens notas registadas.</p></div>
    <?php else: ?>
        <?php foreach ($minhasNotas as $n):
            $nota = $n['nota'];
            if ($nota === null)    { $cor = 'bf-slate';  $pctBar = 5;  $corText = 'var(--texto2)'; $status = 'Pendente'; }
            elseif ($nota >= 10)   { $cor = 'bf-green';  $pctBar = ($nota/20)*100; $corText = 'var(--emerald)'; $status = 'Aprovado'; }
            else                   { $cor = 'bf-rose';   $pctBar = ($nota/20)*100; $corText = 'var(--rose)';    $status = 'Reprovado'; }
        ?>
            <div class="uc-bar">
                <div class="uc-bar-header">
                    <span class="uc-bar-name"><?= htmlspecialchars($n['uc_nome']) ?></span>
                    <span class="uc-bar-nota" style="color:<?= $corText ?>"><?= $nota !== null ? number_format($nota,1).' / 20' : '— / 20' ?></span>
                </div>
                <div class="bar-bg"><div class="bar-fill <?= $cor ?>" style="width:<?= round($pctBar) ?>%"></div></div>
                <div class="uc-meta">
                    <span><?= $status ?></span>
                    <span><?= ucfirst($n['epoca']) ?></span>
                    <span><?= htmlspecialchars($n['ano letivo']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
</div>
<?php layout_foot(); ?>
