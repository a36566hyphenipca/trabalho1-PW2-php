<?php
function layout_head(string $titulo): void { ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> — Sistema Académico</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Crimson+Text:ital,wght@0,400;0,600;1,400&family=Dancing+Script:wght@500;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root {
            --creme:     #f5f0e8;
            --bege:      #e8dcc8;
            --bege2:     #d4c4a8;
            --castanho:  #8b6914;
            --castanho2: #5c4a1e;
            --castanho3: #3d2e0f;
            --texto:     #2c1e0a;
            --texto2:    #6b4f2a;
            --texto3:    #9a7a4a;
            --branco:    #fdf8f0;
            --borda:     #c4a97a;
            --borda2:    #a08050;
            --sombra:    rgba(92, 74, 30, 0.15);
            --verde:     #4a7c59;
            --vermelho:  #8b2e2e;
            --amarelo:   #8b6914;
        }

        body {
            font-family: 'Crimson Text', Georgia, serif;
            background: var(--creme);
            color: var(--texto);
            min-height: 100vh;
            background-image:
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c4a97a' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* NAVBAR */
        nav {
            background: var(--castanho3);
            padding: 0 2rem;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--castanho);
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        nav .brand {
            color: var(--bege);
            font-family: 'Dancing Script', cursive;
            font-size: 1.6rem;
            letter-spacing: .02em;
        }
        nav .brand .dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--castanho);
            margin-right: .4rem;
            vertical-align: middle;
        }
        nav .nav-right { display:flex; align-items:center; gap:.6rem; }
        nav .nav-user { color: var(--bege2); font-size: .88rem; font-family: 'Crimson Text', serif; font-style: italic; }
        nav .nav-btn {
            background: transparent;
            border: 1.5px solid var(--borda);
            color: var(--bege);
            padding: .35rem .9rem;
            border-radius: 4px;
            font-size: .85rem;
            font-family: 'Crimson Text', serif;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        nav .nav-btn:hover { background: var(--castanho); color: var(--branco); }

        /* PAGE */
        .page { max-width: 1080px; margin: 2rem auto; padding: 0 1.5rem; }

        /* HEADINGS */
        h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--castanho3);
            margin-bottom: .3rem;
        }
        .subtitulo {
            color: var(--texto2);
            font-size: 1rem;
            font-style: italic;
            margin-bottom: 1.8rem;
        }

        /* ALERTS */
        .alerta {
            padding: .85rem 1.2rem;
            border-radius: 6px;
            margin-bottom: 1.2rem;
            font-size: .95rem;
            font-family: 'Crimson Text', serif;
            display: flex;
            align-items: center;
            gap: .6rem;
            border-left: 4px solid;
        }
        .alerta-ok   { background: #f0ebe0; color: var(--verde);    border-color: var(--verde);    border: 1px solid #b5d0bc; border-left: 4px solid var(--verde); }
        .alerta-erro { background: #f5e8e8; color: var(--vermelho); border-color: var(--vermelho); border: 1px solid #d4a0a0; border-left: 4px solid var(--vermelho); }
        .alerta-warn { background: #f5ede0; color: var(--castanho);  border-color: var(--castanho); border: 1px solid #d4b880; border-left: 4px solid var(--castanho); }

        /* CARDS */
        .card {
            background: var(--branco);
            border: 1px solid var(--borda);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 12px var(--sombra);
            position: relative;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--castanho), var(--bege2));
            border-radius: 8px 8px 0 0;
        }
        .card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--castanho2);
            margin-bottom: 1.1rem;
            padding-bottom: .6rem;
            border-bottom: 1px dashed var(--borda);
        }

        /* STATS BAR */
        .stats-bar { display:grid; grid-template-columns:repeat(4,1fr); gap:.8rem; margin-bottom:1.5rem; }
        @media(max-width:700px) { .stats-bar { grid-template-columns:repeat(2,1fr); } }
        .stat {
            background: var(--branco);
            border: 1px solid var(--borda);
            border-radius: 8px;
            padding: 1rem 1.1rem;
            box-shadow: 0 2px 8px var(--sombra);
            text-align: center;
        }
        .stat .num {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .stat .lbl { font-size: .8rem; color: var(--texto2); font-style: italic; margin-top: .15rem; }
        .num-indigo  { color: #5c4a8b; }
        .num-emerald { color: var(--verde); }
        .num-amber   { color: var(--castanho); }
        .num-rose    { color: var(--vermelho); }

        /* GRID CARDS */
        .grid-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
        @media(max-width:768px) { .grid-cards { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:480px) { .grid-cards { grid-template-columns:1fr; } }
        .action-card {
            background: var(--branco);
            border: 1px solid var(--borda);
            border-radius: 8px;
            padding: 1.3rem;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: border-color .15s, transform .15s, box-shadow .15s;
            box-shadow: 0 2px 8px var(--sombra);
        }
        .action-card:hover {
            border-color: var(--castanho);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px var(--sombra);
        }
        .action-card .ac-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            margin-bottom: .8rem;
            border: 1.5px solid var(--borda);
            background: var(--creme);
        }
        .action-card h3 { font-family:'Playfair Display',serif; font-size:.95rem; font-weight:600; margin-bottom:.25rem; color:var(--castanho3); }
        .action-card p  { font-size:.85rem; color:var(--texto2); line-height:1.5; font-style:italic; }
        .ic-indigo, .ic-emerald, .ic-amber, .ic-rose, .ic-slate, .ic-violet { background:var(--creme); }

        /* BADGES */
        .badge { display:inline-block; padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; margin-top:.55rem; font-family:'Crimson Text',serif; }
        .b-indigo  { background:#ede8f5; color:#5c4a8b; border:1px solid #c4b8e0; }
        .b-emerald { background:#e8f2ec; color:var(--verde); border:1px solid #b5d0bc; }
        .b-amber   { background:#f5ede0; color:var(--castanho); border:1px solid #d4b880; }
        .b-rose    { background:#f5e8e8; color:var(--vermelho); border:1px solid #d4a0a0; }
        .b-slate   { background:var(--bege); color:var(--texto2); border:1px solid var(--borda); }

        /* FORM */
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
        .grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:1rem; }
        @media(max-width:600px) { .grid-2,.grid-3,.grid-4 { grid-template-columns:1fr; } }
        .campo { margin-bottom:.9rem; }
        .campo label { display:block; font-size:.88rem; font-weight:600; color:var(--castanho2); margin-bottom:.35rem; font-family:'Playfair Display',serif; }
        .campo input, .campo select, .campo textarea {
            width:100%; padding:.65rem .9rem;
            border: 1.5px solid var(--borda);
            border-radius: 5px;
            font-size:.95rem;
            font-family: 'Crimson Text', serif;
            color: var(--texto);
            background: var(--branco);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .campo input:focus, .campo select:focus, .campo textarea:focus {
            border-color: var(--castanho);
            box-shadow: 0 0 0 3px rgba(139,105,20,.1);
        }
        .campo input[readonly] { opacity:.55; cursor:not-allowed; background:var(--creme); }

        /* BUTTONS */
        .btn {
            padding: .65rem 1.4rem;
            border: none;
            border-radius: 5px;
            font-size: .92rem;
            font-weight: 600;
            font-family: 'Crimson Text', serif;
            cursor: pointer;
            transition: transform .12s, opacity .12s;
            display: inline-block;
            text-decoration: none;
        }
        .btn:hover { opacity: .88; transform: translateY(-1px); }
        .btn-primary { background: var(--castanho2); color: var(--branco); border: 1px solid var(--castanho3); }
        .btn-success { background: var(--verde);     color: var(--branco); }
        .btn-danger  { background: var(--vermelho);  color: var(--branco); }
        .btn-warning { background: var(--castanho);  color: var(--branco); }
        .btn-ghost   { background: var(--creme);     color: var(--texto);  border: 1.5px solid var(--borda); }
        .btn-group   { display:flex; gap:.7rem; flex-wrap:wrap; margin-top:1rem; align-items:center; }

        /* TABLE */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.92rem; font-family:'Crimson Text',serif; }
        th {
            background: var(--bege);
            padding: .65rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--castanho2);
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-family: 'Playfair Display', serif;
            border-bottom: 2px solid var(--borda);
        }
        td { padding:.7rem 1rem; border-bottom:1px solid var(--bege); }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background: #faf6ee; }

        /* FOTO */
        .foto-preview { width:90px; height:90px; border-radius:50%; object-fit:cover; border:3px solid var(--castanho); display:block; margin-bottom:.9rem; }

        /* DETAILS */
        details summary { cursor:pointer; color:var(--castanho2); font-weight:600; font-size:.9rem; font-family:'Playfair Display',serif; }
        .form-decisao { margin-top:.7rem; }
        .form-decisao textarea { width:100%; padding:.55rem .8rem; border:1.5px solid var(--borda); border-radius:5px; font-family:'Crimson Text',serif; font-size:.9rem; min-height:60px; margin-bottom:.6rem; resize:vertical; background:var(--branco); }

        /* SEARCH */
        .search-wrap { margin-bottom:1rem; }
        .search-wrap input { width:100%; padding:.65rem 1rem; border:1.5px solid var(--borda); border-radius:5px; font-size:.92rem; font-family:'Crimson Text',serif; outline:none; background:var(--branco); }
        .search-wrap input:focus { border-color:var(--castanho); }

        /* PROGRESS */
        .prog-global { background:var(--castanho3); border-radius:10px; padding:1.3rem 1.5rem; margin-bottom:1.2rem; display:flex; align-items:center; gap:1.5rem; border:2px solid var(--castanho); }
        .prog-circle { position:relative; width:76px; height:76px; flex-shrink:0; }
        .prog-circle svg { transform:rotate(-90deg); }
        .prog-pct-text { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:1rem; font-weight:700; color:var(--bege); font-family:'Playfair Display',serif; }
        .prog-info h2 { font-size:1rem; font-weight:700; color:var(--bege); font-family:'Playfair Display',serif; margin-bottom:.25rem; }
        .prog-info p  { font-size:.82rem; color:var(--bege2); font-style:italic; }
        .prog-pills   { display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.5rem; }
        .ppill { padding:.15rem .65rem; border-radius:20px; font-size:.72rem; font-weight:600; font-family:'Crimson Text',serif; }
        .pp-g { background:rgba(74,124,89,.3);  color:#a8d5b5; }
        .pp-a { background:rgba(139,105,20,.3); color:#e8c870; }
        .pp-r { background:rgba(139,46,46,.3);  color:#e8a0a0; }
        .pp-s { background:rgba(196,169,122,.2);color:var(--bege2); }

        .uc-bar { background:var(--branco); border:1px solid var(--borda); border-radius:8px; padding:1rem 1.1rem; margin-bottom:.7rem; }
        .uc-bar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
        .uc-bar-name  { font-size:.92rem; font-weight:600; font-family:'Playfair Display',serif; color:var(--castanho3); }
        .uc-bar-nota  { font-size:.88rem; font-weight:700; }
        .bar-bg { background:var(--bege); border-radius:20px; height:7px; overflow:hidden; margin-bottom:.4rem; }
        .bar-fill { height:100%; border-radius:20px; }
        .bf-green  { background:var(--verde); }
        .bf-indigo { background:#5c4a8b; }
        .bf-amber  { background:var(--castanho); }
        .bf-rose   { background:var(--vermelho); }
        .bf-slate  { background:var(--texto3); }
        .uc-meta   { font-size:.78rem; color:var(--texto2); display:flex; gap:.8rem; font-style:italic; }
    </style>
<?php }

function layout_nav(string $titulo_pagina, string $dashboard_url): void { ?>
    <nav>
        <div class="brand"><span class="dot"></span> Sistema Académico</div>
        <div class="nav-right">
            <span class="nav-user">✦ <?= htmlspecialchars($_SESSION['nome'] ?? '') ?></span>
            <a href="<?= $dashboard_url ?>" class="nav-btn">← Dashboard</a>
            <a href="/trabalho1-PHP/logout.php" class="nav-btn">Sair</a>
        </div>
    </nav>
<?php }

function layout_foot(): void { ?>
    </body></html>
<?php }
