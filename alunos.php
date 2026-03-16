<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('aluno','funcionario','gestor');
$pdo=getDB(); $perfil=obterPerfil();

if ($perfil==='aluno') {
    $sa=$pdo->prepare('SELECT * FROM `alunos` WHERE `mail`=? LIMIT 1'); $sa->execute([$_SESSION['mail']]); $aluno=$sa->fetch();
    $ficha=$pedidos=null;
    if ($aluno) {
        $sf=$pdo->prepare('SELECT * FROM `ficha aluno` WHERE `aluno ID`=? ORDER BY ID DESC LIMIT 1'); $sf->execute([$aluno['ID']]); $ficha=$sf->fetch();
        $sp=$pdo->prepare('SELECT pm.*,c.nome AS cn FROM `pedido matricula` pm JOIN `curso` c ON c.ID=pm.`curso ID` WHERE pm.`aluno ID`=? ORDER BY pm.ID DESC'); $sp->execute([$aluno['ID']]); $pedidos=$sp->fetchAll();
    }
} else {
    $alunos=$pdo->query("SELECT * FROM `alunos` ORDER BY nome")->fetchAll();
}

layout_head('Alunos');
layout_nav('Alunos', getDashboardUrl());
?>
<div class="page">
<?php if ($perfil==='aluno'): ?>
    <h1>Os Meus Dados</h1><p class="subtitulo">Consulta os teus dados e pedidos.</p>
    <?php if (!$aluno): ?>
        <div class="card"><p style="color:var(--texto2)">Sem ficha criada. <a href="/trabalho1-PHP/fichaaluno.php" style="color:var(--indigo)">Criar ficha →</a></p></div>
    <?php else: ?>
        <div class="card"><h2>👤 Dados Pessoais</h2>
            <div class="grid-2">
                <?php foreach([['Nome',$aluno['nome']],['Email',$aluno['mail']],['Morada',$aluno['morada']??'—'],['Data Nasc.',$aluno['data nascimento']??'—'],['Telefone',$aluno['telefone']??'—'],['Ficha',$ficha?ucfirst($ficha['estado']):'Sem ficha']] as [$l,$v]): ?>
                    <div style="background:var(--bg2);border-radius:9px;padding:.75rem 1rem">
                        <div style="font-size:.75rem;color:var(--texto2);font-weight:600;text-transform:uppercase;letter-spacing:.04em"><?= $l ?></div>
                        <div style="font-size:.9rem;margin-top:.2rem;font-weight:500"><?= htmlspecialchars($v) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card"><h2>📝 Pedidos de Matrícula</h2>
            <?php if (empty($pedidos)): ?><p style="color:var(--texto2)">Sem pedidos. <a href="/trabalho1-PHP/pedidomatricula.php" style="color:var(--indigo)">Fazer pedido →</a></p>
            <?php else: ?>
                <div class="table-wrap"><table>
                    <thead><tr><th>Curso</th><th>Estado</th><th>Data</th><th>Obs.</th></tr></thead>
                    <tbody>
                    <?php foreach($pedidos as $p): $bc=match($p['estado']){'aprovado'=>'b-emerald','rejeitado'=>'b-rose',default=>'b-amber'}; ?>
                        <tr><td><?= htmlspecialchars($p['cn']) ?></td><td><span class="badge <?= $bc ?>"><?= ucfirst($p['estado']) ?></span></td><td><?= $p['data'] ?></td><td><?= htmlspecialchars($p['observações']??'—') ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <h1>Lista de Alunos</h1><p class="subtitulo">Todos os alunos registados (<?= count($alunos) ?>).</p>
    <div class="card">
        <div class="search-wrap"><input type="text" id="srch" placeholder="🔍 Pesquisar por nome ou email..." oninput="filtrar()"></div>
        <?php if (empty($alunos)): ?><p style="color:var(--texto2)">Nenhum aluno.</p>
        <?php else: ?>
            <div class="table-wrap"><table id="tbl">
                <thead><tr><th>#</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Morada</th></tr></thead>
                <tbody>
                <?php foreach($alunos as $a): ?>
                    <tr><td><?= $a['ID'] ?></td><td><?= htmlspecialchars($a['nome']) ?></td><td><?= htmlspecialchars($a['mail']) ?></td><td><?= htmlspecialchars($a['telefone']??'—') ?></td><td><?= htmlspecialchars($a['morada']??'—') ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>
    <script>function filtrar(){const v=document.getElementById('srch').value.toLowerCase();document.querySelectorAll('#tbl tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(v)?'':'none');}</script>
<?php endif; ?>
</div>
<?php layout_foot(); ?>
