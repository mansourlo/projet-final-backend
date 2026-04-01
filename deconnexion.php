<?php
require_once __DIR__ . '/config.php';
session_start();
session_unset();
session_destroy();
// Redémarre la session pour stocker le flash
session_start();
redirect('connexion.php', 'Vous avez été déconnecté avec succès.');
