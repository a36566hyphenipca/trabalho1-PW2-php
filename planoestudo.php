<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('gestor');
$pdo=getDB(); $msg=$erro='';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['criar'])) {
    $ci=(int)$_POST['curso_id']; $ui=(int)$_POST['uc_id']; $ano=(int)$_POST['ano']; $sem=(int)$_POST['semestre'];
    $chk=$pdo->prepare("SELECT ID FROM `plano estudo` WHERE `curso ID`=? AND `UC ID`=? AND `semestre`=?");
    $chk->execute([$ci,$ui,$sem]);
    if ($chk->fetch()) $erro='Esta UC já existe neste curso/semestre.';
    else { $pdo->prepare("INSERT INTO `plano estudo` (`curso ID`,`UC ID`,`ano`,`semestre`) VALUES (?,?,?,?)")->execute([$ci,$ui,$ano,$sem]); $msg='UC adicionada!'; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['apagar'])) {
    $pdo->prepare("DELETE FROM `plano estudo` WHERE ID=?")->execute([(int)$_POST['id']]); $msg='UC removida do plano!';
}

$cursos=$pdo->query("SELECT * FROM `curso` ORDER BY nome")->fetchAll();
$ucs=$pdo->query("SELECT * FROM `uc` ORDER BY nome")->fetchAll();
$planos=$pdo->query("SELECT pe.*,c.nome AS cn,u.nome AS un,u.professor FROM `plano estudo` pe JOIN `curso` c ON c.ID=pe.`curso ID` JOIN `uc` u ON u.ID=pe.`UC ID` ORDER BY c.nome,pe.ano,pe.semestre")->fetchAll();
$porCurso=[];
foreach($planos as $p) $porCurso[$p['cn']][]=$p;

layout_head('Plano de Estudos');
layout_nav('Plano de Estudos', '/trabalho1-PHP/dashboard/gestor.php');
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
<h1>Plano de Estudos</h1><p class="subtitulo">Associa UCs aos cursos com ano e semestre.</p>

<div class="card">
    <h2>➕ Adicionar UC ao Plano</h2>
    <form method="POST">
        <div class="grid-4">
            <div class="campo"><label>Curso</label>
                <select name="curso_id" required><option value="">-- Curso --</option>
                <?php foreach($cursos as $c): ?><option value="<?= $c['ID'] ?>"><?= htmlspecialchars($c['nome']) ?></option><?php endforeach; ?>
                </select></div>
            <div class="campo"><label>UC</label>
                <select name="uc_id" required><option value="">-- UC --</option>
                <?php foreach($ucs as $u): ?><option value="<?= $u['ID'] ?>"><?= htmlspecialchars($u['nome']) ?></option><?php endforeach; ?>
                </select></div>
            <div class="campo"><label>Ano</label>
                <select name="ano" required><option value="1">1º Ano</option><option value="2">2º Ano</option><option value="3">3º Ano</option></select></div>
            <div class="campo"><label>Semestre</label>
                <select name="semestre" required><option value="1">1º Sem.</option><option value="2">2º Sem.</option></select></div>
        </div>
        <button type="submit" name="criar" class="btn btn-primary">➕ Adicionar</button>
    </form>
</div>

<?php foreach($porCurso as $nomeCurso=>$entradas): ?>
<div class="card">
    <h2>🏫 <?= htmlspecialchars($nomeCurso) ?></h2>
    <div class="table-wrap"><table>
        <thead><tr><th>UC</th><th>Professor</th><th>Ano</th><th>Semestre</th><th></th></tr></thead>
        <tbody>
        <?php foreach($entradas as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['un']) ?></td>
                <td><?= htmlspecialchars($e['professor']) ?></td>
                <td><?= $e['ano'] ?>º</td>
                <td><span class="badge b-indigo"><?= $e['semestre'] ?>º Sem.</span></td>
                <td><form method="POST" onsubmit="return confirm('Remover?')"><input type="hidden" name="id" value="<?= $e['ID'] ?>"><button type="submit" name="apagar" class="btn btn-danger" style="font-size:.75rem;padding:.3rem .7rem">🗑️</button></form></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
<?php endforeach; ?>
<?php if(empty($porCurso)): ?><div class="card"><p style="color:var(--texto2)">Nenhuma UC associada ainda.</p></div><?php endif; ?>
</div>
<?php layout_foot(); ?>
