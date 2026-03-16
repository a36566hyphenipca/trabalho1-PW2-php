<?php
require_once __DIR__ . '/auth/auth.php';
iniciarSessao();
terminarSessao();
header('Location: /trabalho1-PHP/login.php');
exit;
