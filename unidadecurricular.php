<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('gestor');
$pdo=getDB(); $msg=$erro='';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['criar'])) {
    $nome=trim($_POST['nome']??''); $prof=trim($_POST['professor']??'');
    if (!$nome) $erro='Nome obrigatório.';
    else { $pdo->prepare("INSERT INTO `uc` (`nome`,`professor`) VALUES (?,?)")->execute([$nome,$prof]); $msg='UC criada!'; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['editar'])) {
    $id=(int)$_POST['id']; $nome=trim($_POST['nome']??''); $prof=trim($_POST['professor']??'');
    if (!$nome) $erro='Nome obrigatório.';
    else { $pdo->prepare("UPDATE `uc` SET `nome`=?,`professor`=? WHERE ID=?")->execute([$nome,$prof,$id]); $msg='UC atualizada!'; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['apagar'])) {
    $pdo->prepare("DELETE FROM `uc` WHERE ID=?")->execute([(int)$_POST['id']]); $msg='UC removida!';
}

$ucs=$pdo->query("SELECT * FROM `uc` ORDER BY ID DESC")->fetchAll();
$editar=null;
if (isset($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM `uc` WHERE ID=?"); $s->execute([(int)$_GET['edit']]); $editar=$s->fetch(); }

layout_head('Unidades Curriculares');
layout_nav('UCs', '/trabalho1-PHP/dashboard/gestor.php');
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
<h1>Unidades Curriculares</h1><p class="subtitulo">Gere as UCs disponíveis.</p>

<div class="card">
    <h2><?= $editar?'✏️ Editar UC':'➕ Nova UC' ?></h2>
    <form method="POST">
        <?php if ($editar): ?><input type="hidden" name="id" value="<?= $editar['ID'] ?>"><?php endif; ?>
        <div class="grid-2">
            <div class="campo"><label>Nome da UC</label><input type="text" name="nome" required value="<?= htmlspecialchars($editar['nome']??'') ?>" placeholder="Ex: Programação Web"></div>
            <div class="campo"><label>Professor</label><input type="text" name="professor" value="<?= htmlspecialchars($editar['professor']??'') ?>" placeholder="Ex: Prof. Silva"></div>
        </div>
        <div class="btn-group">
            <?php if ($editar): ?>
                <button type="submit" name="editar" class="btn btn-primary">💾 Guardar</button>
                <a href="unidadecurricular.php" class="btn btn-ghost">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="criar" class="btn btn-primary">➕ Criar UC</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h2>📋 Todas as UCs</h2>
    <?php if (empty($ucs)): ?><p style="color:var(--texto2)">Nenhuma UC criada.</p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Nome</th><th>Professor</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($ucs as $u): ?>
            <tr>
                <td><?= $u['ID'] ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['professor']) ?></td>
                <td>
                    <a href="unidadecurricular.php?edit=<?= $u['ID'] ?>" class="btn btn-warning" style="font-size:.78rem;padding:.35rem .8rem">✏️ Editar</a>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Apagar UC?')">
                        <input type="hidden" name="id" value="<?= $u['ID'] ?>">
                        <button type="submit" name="apagar" class="btn btn-danger" style="font-size:.78rem;padding:.35rem .8rem">🗑️ Apagar</button>
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
