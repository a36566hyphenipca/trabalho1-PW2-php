<?php
require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/layout.php';
iniciarSessao(); requireLogin();
layout_head('Contacto');
layout_nav('Contacto', getDashboardUrl());
?>
<div class="page" style="max-width:700px">
<h1>Contacto e Suporte</h1><p class="subtitulo">Entra em contacto com os Serviços Académicos.</p>
<div class="card"><h2>🏛️ Serviços Académicos</h2>
    <?php foreach([['📧','Email','academicos@instituicao.pt'],['📞','Telefone','+351 253 000 000'],['📍','Localização','Edifício Principal, Piso 0, Sala 001']] as [$ic,$l,$v]): ?>
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 0;border-bottom:1px solid var(--borda)">
            <div style="font-size:1.3rem;width:36px;text-align:center"><?= $ic ?></div>
            <div><div style="font-size:.8rem;color:var(--texto2);font-weight:600"><?= $l ?></div><div style="font-size:.9rem;margin-top:.1rem"><?= $v ?></div></div>
        </div>
    <?php endforeach; ?>
    <div style="background:var(--emerald-l);border-radius:9px;padding:.8rem 1rem;margin-top:1rem;font-size:.85rem;color:var(--emerald-d)">
        🕐 <strong>Horário:</strong> Segunda a Sexta, 09h00 – 17h00
    </div>
</div>
<div class="card"><h2>📋 Gestão Pedagógica</h2>
    <?php foreach([['📧','Email','pedagogico@instituicao.pt'],['📞','Telefone','+351 253 000 001']] as [$ic,$l,$v]): ?>
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 0;border-bottom:1px solid var(--borda)">
            <div style="font-size:1.3rem;width:36px;text-align:center"><?= $ic ?></div>
            <div><div style="font-size:.8rem;color:var(--texto2);font-weight:600"><?= $l ?></div><div style="font-size:.9rem;margin-top:.1rem"><?= $v ?></div></div>
        </div>
    <?php endforeach; ?>
</div>
</div>
<?php layout_foot(); ?>
