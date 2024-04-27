<?php
// Détruire le cookie JWT en le rendant expiré
setcookie('jwt', '', time() - 3600, '/');

// Détruire la session PHP
session_start();
session_unset();
session_destroy();

// Rediriger vers la page de connexion
header("Location: connexion.html");
exit;
?>
