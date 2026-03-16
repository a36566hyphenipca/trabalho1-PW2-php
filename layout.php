<?php
// layout.php — includes comuns a todas as páginas
function layout_head(string $titulo): void { ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> — Sistema Académico</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root {
            --bg:#f8fafc; --bg2:#f1f5f9; --branco:#ffffff;
            --texto:#0f172a; --texto2:#64748b; --texto3:#94a3b8;
            --borda:#e2e8f0; --borda2:#cbd5e1;
            --indigo:#6366f1; --indigo-l:#eef2ff; --indigo-d:#4338ca;
            --emerald:#10b981; --emerald-l:#ecfdf5; --emerald-d:#065f46;
            --amber:#f59e0b; --amber-l:#fffbeb; --amber-d:#92400e;
            --rose:#f43f5e; --rose-l:#fff1f2; --rose-d:#9f1239;
            --navy:#0f172a;
        }
        body { font-family:'Sora',sans-serif; background:var(--bg); color:var(--texto); min-height:100vh; }

        /* NAVBAR */
        nav {
            background:var(--navy); padding:0 2rem; height:60px;
            display:flex; align-items:center; justify-content:space-between;
            position:sticky; top:0; z-index:100;
        }
        nav .brand { color:#fff; font-weight:700; font-size:1rem; display:flex; align-items:center; gap:.5rem; }
        nav .brand .dot { width:8px; height:8px; border-radius:50%; background:var(--indigo); }
        nav .nav-right { display:flex; align-items:center; gap:.6rem; }
        nav .nav-user { color:rgba(255,255,255,.7); font-size:.82rem; }
        nav .nav-btn {
            background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
            color:#fff; padding:.35rem .85rem; border-radius:8px; font-size:.8rem;
            font-family:'Sora',sans-serif; cursor:pointer; text-decoration:none;
            transition:background .15s;
        }
        nav .nav-btn:hover { background:rgba(255,255,255,.15); }

        /* PAGE */
        .page { max-width:1080px; margin:2rem auto; padding:0 1.5rem; }

        /* HEADINGS */
        h1 { font-size:1.7rem; font-weight:700; color:var(--texto); margin-bottom:.3rem; }
        .subtitulo { color:var(--texto2); font-size:.88rem; margin-bottom:1.8rem; }

        /* ALERTS */
        .alerta { padding:.85rem 1.1rem; border-radius:10px; margin-bottom:1.2rem; font-size:.87rem; font-weight:500; display:flex; align-items:center; gap:.6rem; }
        .alerta-ok   { background:var(--emerald-l); color:var(--emerald-d); border:1px solid #a7f3d0; }
        .alerta-erro { background:var(--rose-l); color:var(--rose-d); border:1px solid #fecdd3; }
        .alerta-warn { background:var(--amber-l); color:var(--amber-d); border:1px solid #fde68a; }

        /* CARDS */
        .card { background:var(--branco); border:1px solid var(--borda); border-radius:14px; padding:1.5rem; margin-bottom:1.2rem; }
        .card h2 { font-size:1rem; font-weight:600; color:var(--texto); margin-bottom:1.1rem; padding-bottom:.6rem; border-bottom:1px solid var(--borda); }

        /* STATS BAR */
        .stats-bar { display:grid; grid-template-columns:repeat(4,1fr); gap:.8rem; margin-bottom:1.5rem; }
        @media(max-width:700px) { .stats-bar { grid-template-columns:repeat(2,1fr); } }
        .stat { background:var(--branco); border:1px solid var(--borda); border-radius:12px; padding:1rem 1.1rem; }
        .stat .num { font-size:1.8rem; font-weight:700; }
        .stat .lbl { font-size:.75rem; color:var(--texto2); margin-top:.15rem; }
        .num-indigo  { color:var(--indigo); }
        .num-emerald { color:var(--emerald); }
        .num-amber   { color:var(--amber); }
        .num-rose    { color:var(--rose); }

        /* GRID CARDS */
        .grid-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
        @media(max-width:768px) { .grid-cards { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:480px) { .grid-cards { grid-template-columns:1fr; } }
        .action-card {
            background:var(--branco); border:1px solid var(--borda); border-radius:14px;
            padding:1.3rem; text-decoration:none; color:inherit; display:block;
            transition:border-color .15s, transform .15s;
        }
        .action-card:hover { border-color:var(--indigo); transform:translateY(-2px); }
        .action-card .ac-icon { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:.95rem; margin-bottom:.8rem; }
        .action-card h3 { font-size:.9rem; font-weight:600; margin-bottom:.25rem; }
        .action-card p  { font-size:.78rem; color:var(--texto2); line-height:1.5; }
        .ic-indigo  { background:var(--indigo-l); }
        .ic-emerald { background:var(--emerald-l); }
        .ic-amber   { background:var(--amber-l); }
        .ic-rose    { background:var(--rose-l); }
        .ic-slate   { background:var(--bg2); }
        .ic-violet  { background:#f5f3ff; }

        /* BADGES */
        .badge { display:inline-block; padding:.2rem .65rem; border-radius:20px; font-size:.72rem; font-weight:600; margin-top:.55rem; }
        .b-indigo  { background:var(--indigo-l); color:var(--indigo-d); }
        .b-emerald { background:var(--emerald-l); color:var(--emerald-d); }
        .b-amber   { background:var(--amber-l); color:var(--amber-d); }
        .b-rose    { background:var(--rose-l); color:var(--rose-d); }
        .b-slate   { background:var(--bg2); color:var(--texto2); }

        /* FORM */
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
        .grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:1rem; }
        @media(max-width:600px) { .grid-2,.grid-3,.grid-4 { grid-template-columns:1fr; } }
        .campo { margin-bottom:.9rem; }
        .campo label { display:block; font-size:.8rem; font-weight:600; color:var(--texto); margin-bottom:.35rem; }
        .campo input, .campo select, .campo textarea {
            width:100%; padding:.65rem .9rem; border:1.5px solid var(--borda2);
            border-radius:9px; font-size:.88rem; font-family:'Sora',sans-serif;
            color:var(--texto); background:var(--bg); outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .campo input:focus, .campo select:focus, .campo textarea:focus {
            border-color:var(--indigo); box-shadow:0 0 0 3px rgba(99,102,241,.12); background:#fff;
        }
        .campo input[readonly] { opacity:.55; cursor:not-allowed; }

        /* BUTTONS */
        .btn { padding:.65rem 1.3rem; border:none; border-radius:9px; font-size:.87rem; font-weight:600; font-family:'Sora',sans-serif; cursor:pointer; transition:transform .12s, opacity .12s; display:inline-block; text-decoration:none; }
        .btn:hover { opacity:.88; transform:translateY(-1px); }
        .btn-primary { background:var(--indigo); color:#fff; }
        .btn-success { background:var(--emerald); color:#fff; }
        .btn-danger  { background:var(--rose); color:#fff; }
        .btn-warning { background:var(--amber); color:#fff; }
        .btn-ghost   { background:var(--bg2); color:var(--texto); }
        .btn-group   { display:flex; gap:.7rem; flex-wrap:wrap; margin-top:1rem; align-items:center; }

        /* TABLE */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.85rem; }
        th { background:var(--bg2); padding:.65rem 1rem; text-align:left; font-weight:600; color:var(--texto2); font-size:.75rem; text-transform:uppercase; letter-spacing:.04em; }
        td { padding:.7rem 1rem; border-bottom:1px solid var(--borda); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#fafbff; }

        /* FOTO */
        .foto-preview { width:90px; height:90px; border-radius:50%; object-fit:cover; border:3px solid var(--indigo); display:block; margin-bottom:.9rem; }

        /* DETAILS */
        details summary { cursor:pointer; color:var(--indigo); font-weight:600; font-size:.83rem; }
        .form-decisao { margin-top:.7rem; }
        .form-decisao textarea { width:100%; padding:.55rem .8rem; border:1.5px solid var(--borda2); border-radius:8px; font-family:'Sora',sans-serif; font-size:.83rem; min-height:60px; margin-bottom:.6rem; resize:vertical; }

        /* SEARCH */
        .search-wrap { margin-bottom:1rem; }
        .search-wrap input { width:100%; padding:.65rem 1rem; border:1.5px solid var(--borda2); border-radius:9px; font-size:.88rem; font-family:'Sora',sans-serif; outline:none; }
        .search-wrap input:focus { border-color:var(--indigo); }

        /* PROGRESS */
        .prog-global { background:var(--navy); border-radius:14px; padding:1.3rem 1.5rem; margin-bottom:1.2rem; display:flex; align-items:center; gap:1.5rem; }
        .prog-circle { position:relative; width:76px; height:76px; flex-shrink:0; }
        .prog-circle svg { transform:rotate(-90deg); }
        .prog-pct-text { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:1rem; font-weight:700; color:#fff; }
        .prog-info h2 { font-size:1rem; font-weight:700; color:#fff; margin-bottom:.25rem; }
        .prog-info p  { font-size:.78rem; color:rgba(255,255,255,.55); }
        .prog-pills { display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.5rem; }
        .ppill { padding:.15rem .6rem; border-radius:20px; font-size:.7rem; font-weight:600; }
        .pp-g { background:rgba(16,185,129,.2); color:#6ee7b7; }
        .pp-a { background:rgba(245,158,11,.2); color:#fcd34d; }
        .pp-r { background:rgba(244,63,94,.2); color:#fda4af; }
        .pp-s { background:rgba(148,163,184,.2); color:#cbd5e1; }

        .uc-bar { background:var(--branco); border:1px solid var(--borda); border-radius:12px; padding:1rem 1.1rem; margin-bottom:.7rem; }
        .uc-bar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
        .uc-bar-name  { font-size:.88rem; font-weight:600; }
        .uc-bar-nota  { font-size:.85rem; font-weight:700; }
        .bar-bg { background:var(--bg2); border-radius:20px; height:7px; overflow:hidden; margin-bottom:.4rem; }
        .bar-fill { height:100%; border-radius:20px; }
        .bf-green  { background:var(--emerald); }
        .bf-indigo { background:var(--indigo); }
        .bf-amber  { background:var(--amber); }
        .bf-rose   { background:var(--rose); }
        .bf-slate  { background:var(--texto3); }
        .uc-meta   { font-size:.73rem; color:var(--texto2); display:flex; gap:.8rem; }
    </style>
<?php }

function layout_nav(string $titulo_pagina, string $dashboard_url): void { ?>
    <nav>
        <div class="brand"><div class="dot"></div> Sistema Académico</div>
        <div class="nav-right">
            <span class="nav-user"><?= htmlspecialchars($_SESSION['nome'] ?? '') ?></span>
            <a href="<?= $dashboard_url ?>" class="nav-btn">← Dashboard</a>
            <a href="/trabalho1-PHP/logout.php" class="nav-btn">Sair</a>
        </div>
    </nav>
<?php }

function layout_foot(): void { ?>
    </body></html>
<?php }
