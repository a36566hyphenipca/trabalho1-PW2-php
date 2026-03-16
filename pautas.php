<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('funcionario');
$pdo=getDB(); $msg=$erro='';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['criar'])) {
    $ui=(int)$_POST['uc_id']; $al=trim($_POST['ano_letivo']??''); $ep=$_POST['epoca']??'';
    if (!$ui||!$al||!$ep) $erro='Preenche todos os campos.';
    else {
        $chk=$pdo->prepare("SELECT ID FROM `pautas` WHERE `UC ID`=? AND `ano letivo`=? AND `epoca`=?");
        $chk->execute([$ui,$al,$ep]);
        if ($chk->fetch()) $erro='Já existe esta pauta.';
        else {
            $pdo->prepare("INSERT INTO `pautas` (`UC ID`,`ano letivo`,`epoca`) VALUES (?,?,?)")->execute([$ui,$al,$ep]);
            $pid=$pdo->lastInsertId();
            // Adicionar alunos com matrícula aprovada
            $sa=$pdo->prepare("SELECT DISTINCT pm.`aluno ID` FROM `pedido matricula` pm JOIN `plano estudo` pe ON pe.`curso ID`=pm.`curso ID` WHERE pe.`UC ID`=? AND pm.`estado`='aprovado'");
            $sa->execute([$ui]);
            $alunos=$sa->fetchAll();
            foreach($alunos as $a) {
                $pdo->prepare("INSERT IGNORE INTO `notas` (`pauta ID`,`aluno ID`,`nota`) VALUES (?,?,0.0)")->execute([$pid,$a['aluno ID']]);
            }
            $msg='Pauta criada com '.count($alunos).' aluno(s)!';
        }
    }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['apagar'])) {
    $pdo->prepare("DELETE FROM `pautas` WHERE ID=?")->execute([(int)$_POST['id']]); $msg='Pauta removida!';
}

$ucs=$pdo->query("SELECT * FROM `uc` ORDER BY nome")->fetchAll();
$pautas=$pdo->query("SELECT p.*,u.nome AS uc_nome,COUNT(n.ID) AS total FROM `pautas` p JOIN `uc` u ON u.ID=p.`UC ID` LEFT JOIN `notas` n ON n.`pauta ID`=p.ID GROUP BY p.ID ORDER BY p.ID DESC")->fetchAll();

layout_head('Pautas');
layout_nav('Pautas', '/trabalho1-PHP/dashboard/funcionario.php');
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
<h1>Pautas de Avaliação</h1><p class="subtitulo">Cria pautas por UC e época. Alunos matriculados são adicionados automaticamente.</p>

<div class="card">
    <h2>➕ Nova Pauta</h2>
    <form method="POST">
        <div class="grid-3">
            <div class="campo"><label>Unidade Curricular</label>
                <select name="uc_id" required><option value="">-- Seleciona --</option>
                <?php foreach($ucs as $u): ?><option value="<?= $u['ID'] ?>"><?= htmlspecialchars($u['nome']) ?></option><?php endforeach; ?>
                </select></div>
            <div class="campo"><label>Ano Letivo</label><input type="text" name="ano_letivo" placeholder="Ex: 2024/2025" required></div>
            <div class="campo"><label>Época</label>
                <select name="epoca" required><option value="">-- Época --</option><option value="normal">Normal</option><option value="recurso">Recurso</option><option value="especial">Especial</option></select></div>
        </div>
        <button type="submit" name="criar" class="btn btn-primary">➕ Criar Pauta</button>
    </form>
</div>

<div class="card">
    <h2>📋 Todas as Pautas</h2>
    <?php if (empty($pautas)): ?><p style="color:var(--texto2)">Nenhuma pauta criada.</p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>UC</th><th>Ano Letivo</th><th>Época</th><th>Alunos</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach($pautas as $p):
            $bc=match($p['epoca']){'normal'=>'b-indigo','recurso'=>'b-amber',default=>'b-slate'};
        ?>
            <tr>
                <td><?= $p['ID'] ?></td>
                <td><?= htmlspecialchars($p['uc_nome']) ?></td>
                <td><?= htmlspecialchars($p['ano letivo']) ?></td>
                <td><span class="badge <?= $bc ?>"><?= ucfirst($p['epoca']) ?></span></td>
                <td><?= $p['total'] ?> aluno(s)</td>
                <td>
                    <a href="/trabalho1-PHP/notas.php?pauta_id=<?= $p['ID'] ?>" class="btn btn-success" style="font-size:.78rem;padding:.35rem .8rem">✏️ Notas</a>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Apagar pauta?')">
                        <input type="hidden" name="id" value="<?= $p['ID'] ?>">
                        <button type="submit" name="apagar" class="btn btn-danger" style="font-size:.78rem;padding:.35rem .8rem">🗑️</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php endif; ?>
</div>
</div>
<?php layout_foot(); ?>
