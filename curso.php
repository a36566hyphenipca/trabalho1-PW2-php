<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('gestor');

$pdo=$msg=$erro=''; $pdo=getDB();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['criar'])) {
    $nome=(trim($_POST['nome']??'')); $max=(int)($_POST['max']??30);
    if (!$nome) $erro='Nome obrigatório.';
    else { $pdo->prepare("INSERT INTO `curso` (`nome`,`numero maximo alunos`,`ativo`) VALUES (?,?,1)")->execute([$nome,$max]); $msg='Curso criado!'; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['editar'])) {
    $id=(int)$_POST['id']; $nome=trim($_POST['nome']??''); $max=(int)($_POST['max']??30);
    if (!$nome) $erro='Nome obrigatório.';
    else { $pdo->prepare("UPDATE `curso` SET `nome`=?,`numero maximo alunos`=? WHERE ID=?")->execute([$nome,$max,$id]); $msg='Curso atualizado!'; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['toggle'])) {
    $id=(int)$_POST['id']; $a=(int)$_POST['ativo'];
    $pdo->prepare("UPDATE `curso` SET `ativo`=? WHERE ID=?")->execute([$a?0:1,$id]); $msg='Estado atualizado!';
}

$cursos=$pdo->query("SELECT * FROM `curso` ORDER BY ID DESC")->fetchAll();
$editar=null;
if (isset($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM `curso` WHERE ID=?"); $s->execute([(int)$_GET['edit']]); $editar=$s->fetch(); }

layout_head('Cursos');
layout_nav('Cursos', '/trabalho1-PHP/dashboard/gestor.php');
?>
<div class="page">
<?php if ($msg):  ?><div class="alerta alerta-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta-erro">⚠️ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
<h1>Cursos</h1><p class="subtitulo">Cria e gere os cursos disponíveis.</p>

<div class="card">
    <h2><?= $editar?'✏️ Editar Curso':'➕ Novo Curso' ?></h2>
    <form method="POST">
        <?php if ($editar): ?><input type="hidden" name="id" value="<?= $editar['ID'] ?>"><?php endif; ?>
        <div class="grid-2">
            <div class="campo"><label>Nome do curso</label><input type="text" name="nome" required value="<?= htmlspecialchars($editar['nome']??'') ?>" placeholder="Ex: Engenharia Informática"></div>
            <div class="campo"><label>Máx. alunos</label><input type="number" name="max" min="1" value="<?= $editar['numero maximo alunos']??30 ?>"></div>
        </div>
        <div class="btn-group">
            <?php if ($editar): ?>
                <button type="submit" name="editar" class="btn btn-primary">💾 Guardar</button>
                <a href="curso.php" class="btn btn-ghost">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="criar" class="btn btn-primary">➕ Criar Curso</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h2>📋 Todos os Cursos</h2>
    <?php if (empty($cursos)): ?><p style="color:var(--texto2)">Nenhum curso criado.</p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Nome</th><th>Máx.</th><th>Estado</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($cursos as $c): ?>
            <tr>
                <td><?= $c['ID'] ?></td>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td><?= $c['numero maximo alunos'] ?></td>
                <td><span class="badge <?= ($c['ativo']??1)?'b-emerald':'b-slate' ?>"><?= ($c['ativo']??1)?'Ativo':'Inativo' ?></span></td>
                <td>
                    <a href="curso.php?edit=<?= $c['ID'] ?>" class="btn btn-warning" style="font-size:.78rem;padding:.35rem .8rem">✏️ Editar</a>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="id" value="<?= $c['ID'] ?>">
                        <input type="hidden" name="ativo" value="<?= $c['ativo']??1 ?>">
                        <button type="submit" name="toggle" class="btn btn-danger" style="font-size:.78rem;padding:.35rem .8rem"><?= ($c['ativo']??1)?'Desativar':'Ativar' ?></button>
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
