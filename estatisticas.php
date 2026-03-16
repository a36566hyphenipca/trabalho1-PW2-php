<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao();
requireRole('funcionario', 'gestor');

$pdo = getDB();

$totalAlunos  = $pdo->query("SELECT COUNT(*) FROM `alunos`")->fetchColumn();
$totalCursos  = $pdo->query("SELECT COUNT(*) FROM `curso`")->fetchColumn();
$totalUCs     = $pdo->query("SELECT COUNT(*) FROM `uc`")->fetchColumn();
$totalPautas  = $pdo->query("SELECT COUNT(*) FROM `pautas`")->fetchColumn();

$mats = $pdo->query("SELECT `estado`, COUNT(*) as total FROM `pedido matricula` GROUP BY `estado`")->fetchAll();
$matStats = ['pendente'=>0,'aprovado'=>0,'rejeitado'=>0];
foreach ($mats as $m) $matStats[$m['estado']] = $m['total'];

$fichs = $pdo->query("SELECT `estado`, COUNT(*) as total FROM `ficha aluno` GROUP BY `estado`")->fetchAll();
$fichaStats = ['rascunho'=>0,'submetida'=>0,'aprovada'=>0,'rejeitada'=>0];
foreach ($fichs as $f) $fichaStats[$f['estado']] = $f['total'];

$topCursos = $pdo->query("
    SELECT c.nome, COUNT(pm.ID) as total
    FROM `curso` c
    LEFT JOIN `pedido matricula` pm ON pm.`curso ID`=c.ID AND pm.`estado`='aprovado'
    GROUP BY c.ID ORDER BY total DESC LIMIT 5
")->fetchAll();

$totalMat   = array_sum($matStats);
$totalFicha = array_sum($fichaStats);

layout_head('Estatísticas');
layout_nav('Estatísticas', getDashboardUrl());
?>
<div class="page">
<h1>Estatísticas Gerais</h1>
<p class="subtitulo">Visão geral do sistema académico.</p>

<div class="stats-bar">
    <div class="stat"><div class="num num-indigo"><?= $totalAlunos ?></div><div class="lbl">🎓 Alunos</div></div>
    <div class="stat"><div class="num num-emerald"><?= $totalCursos ?></div><div class="lbl">🏫 Cursos</div></div>
    <div class="stat"><div class="num num-amber"><?= $totalUCs ?></div><div class="lbl">📚 UCs</div></div>
    <div class="stat"><div class="num num-rose"><?= $totalPautas ?></div><div class="lbl">📊 Pautas</div></div>
</div>

<div class="grid-2">
    <div class="card">
        <h2>📝 Pedidos de Matrícula</h2>
        <?php foreach ([
            ['Pendentes', $matStats['pendente'],  'var(--amber)',   $totalMat],
            ['Aprovados', $matStats['aprovado'],  'var(--emerald)', $totalMat],
            ['Rejeitados',$matStats['rejeitado'], 'var(--rose)',    $totalMat],
        ] as [$lbl, $val, $cor, $tot]):
            $pct = $tot > 0 ? round($val / $tot * 100) : 0;
        ?>
        <div style="margin-bottom:.9rem">
            <div style="display:flex;justify-content:space-between;font-size:.83rem;margin-bottom:.3rem">
                <span><?= $lbl ?></span>
                <span style="font-weight:600"><?= $val ?> (<?= $pct ?>%)</span>
            </div>
            <div style="background:var(--bg2);border-radius:20px;height:8px;overflow:hidden">
                <div style="width:<?= $pct ?>%;height:100%;background:<?= $cor ?>;border-radius:20px"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h2>📋 Fichas de Aluno</h2>
        <?php foreach ([
            ['Rascunho',   $fichaStats['rascunho'],  'var(--texto3)',  $totalFicha],
            ['Submetidas', $fichaStats['submetida'], 'var(--amber)',   $totalFicha],
            ['Aprovadas',  $fichaStats['aprovada'],  'var(--emerald)', $totalFicha],
            ['Rejeitadas', $fichaStats['rejeitada'], 'var(--rose)',    $totalFicha],
        ] as [$lbl, $val, $cor, $tot]):
            $pct = $tot > 0 ? round($val / $tot * 100) : 0;
        ?>
        <div style="margin-bottom:.9rem">
            <div style="display:flex;justify-content:space-between;font-size:.83rem;margin-bottom:.3rem">
                <span><?= $lbl ?></span>
                <span style="font-weight:600"><?= $val ?> (<?= $pct ?>%)</span>
            </div>
            <div style="background:var(--bg2);border-radius:20px;height:8px;overflow:hidden">
                <div style="width:<?= $pct ?>%;height:100%;background:<?= $cor ?>;border-radius:20px"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <h2>🏆 Cursos com Mais Matrículas Aprovadas</h2>
    <div class="table-wrap"><table>
        <thead><tr><th>Curso</th><th>Matrículas aprovadas</th></tr></thead>
        <tbody>
        <?php foreach ($topCursos as $c): ?>
            <tr><td><?= htmlspecialchars($c['nome']) ?></td><td><?= $c['total'] ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($topCursos)): ?>
            <tr><td colspan="2" style="color:var(--texto2)">Sem dados ainda.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div>
<?php layout_foot(); ?>
