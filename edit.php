<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=nom_de_ta_bdd;charset=utf8', 'utilisateur', 'motdepasse');
$user_id = $_SESSION['user_id'];

// Récupération des données
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajouter des validations si nécessaire
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->execute([$_POST['email'], $user_id]);
    echo "Mise à jour réussie.";
}
?>

<!DOCTYPE html>
<html>
<head><title>Modifier mes données</title></head>
<body>
    <h1>Modifier mes informations</h1>
    <form method="post">
        <label>Email : <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>"></label><br>
        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
