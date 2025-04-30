<?php
session_start();

if (!isset($_SESSION['nouvel_utilisateur'])) {
    header("Location: index.php");
    exit();
}

$login = $_SESSION['nouvel_utilisateur']['login'];
$password = $_SESSION['nouvel_utilisateur']['password'];
unset($_SESSION['nouvel_utilisateur']);
?>

<!DOCTYPE html>
<html>
<head><title>Inscription réussie</title></head>
<body>
    <h1>Votre inscription est confirmée !</h1>
    <p>Conservez ces identifiants pour modifier vos informations plus tard :</p>
    <ul>
        <li><strong>Identifiant :</strong> <?= htmlspecialchars($login) ?></li>
        <li><strong>Mot de passe :</strong> <?= htmlspecialchars($password) ?></li>
    </ul>
    <a href="login.php">Se connecter</a>
</body>
</html>
