<?php
require_once __DIR__ . '/auth/auth.php';
iniciarSessao();
if (estaLogado()) { header('Location: ' . getDashboardUrl()); }
else { header('Location: /trabalho1-PHP/login.php'); }
exit;
