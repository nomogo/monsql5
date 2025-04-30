<?php
session_start();

// 1. Connexion à la base de données
$host = "localhost";
$username = "u68658";
$password = "7975806";
$database = "u68658";

// Fonction de sécurisation
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_complet = sanitize($_POST["nom_complet"]);
    $telephone = sanitize($_POST["telephone"]);
    $email = sanitize($_POST["email"]);
    $date_naissance = sanitize($_POST["date_naissance"]);
    $genre = sanitize($_POST["genre"]);
    $biographie = sanitize($_POST["biographie"]);
    $accord = isset($_POST["accord"]) ? 1 : 0;

    $langages = isset($_POST["langages"]) && is_array($_POST["langages"]) ? $_POST["langages"] : [];

    $login = sanitize($_POST["login"]);
    $mot_de_passe = $_POST["mot_de_passe"]; // pas sanitize car hashé directement

    $erreurs = [];

    // Validation avec regex
    if (!preg_match("/^[a-zA-Zà-ÿ\s-]+$/u", $nom_complet) || strlen($nom_complet) > 150) {
        $erreurs[] = "Le champ Nom complet doit contenir uniquement des lettres, espaces et tirets (max 150 caractères).";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Format d'email incorrect.";
    }

    if (!preg_match("/^[0-9\s\-\+]+$/", $telephone) || strlen($telephone) < 7) {
        $erreurs[] = "Numéro de téléphone invalide.";
    }

    if (!in_array($genre, ["masculin", "feminin"])) {
        $erreurs[] = "Genre invalide.";
    }

    $langages_autorises = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskel", "Clojure", "Prolog", "Scala", "Go"];
    foreach ($langages as $langage) {
        if (!in_array($langage, $langages_autorises)) {
            $erreurs[] = "Langage non valide : " . htmlspecialchars($langage);
        }
    }

    // Validation login / mot de passe
    if (!preg_match("/^[a-zA-Z0-9_]{4,20}$/", $login)) {
        $erreurs[] = "Le login doit contenir entre 4 et 20 caractères alphanumériques ou '_'.";
    }
    if (strlen($mot_de_passe) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Si erreur -> redirection avec cookies
    if (!empty($erreurs)) {
        setcookie("form_errors", json_encode($erreurs), 0, "/");
        setcookie("form_values", json_encode($_POST), 0, "/");
        header("Location: index.php");
        exit();
    }

    // Connexion BDD
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connexion échouée : " . $e->getMessage());
    }

    // Vérifier si login déjà pris
    $check_login = $pdo->prepare("SELECT id FROM users WHERE login = ?");
    $check_login->execute([$login]);
    if ($check_login->rowCount() > 0) {
        setcookie("form_errors", json_encode(["Ce login est déjà utilisé."]), 0, "/");
        setcookie("form_values", json_encode($_POST), 0, "/");
        header("Location: index.php");
        exit();
    }

    // Hash du mot de passe
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insertion dans users
    $sql_utilisateur = "INSERT INTO users (nom_complet, telephone, email, date_naissance, genre, biographie, accord, login, mot_de_passe_hash)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql_utilisateur);
    $stmt->execute([$nom_complet, $telephone, $email, $date_naissance, $genre, $biographie, $accord, $login, $hash]);
    $utilisateur_id = $pdo->lastInsertId();

    // Insertion des langages
    $sql_langage = "INSERT INTO user_languages (utilisateur_id, langage) VALUES (?, ?)";
    $stmt_langage = $pdo->prepare($sql_langage);
    foreach ($langages as $langage) {
        $stmt_langage->execute([$utilisateur_id, $langage]);
    }

    // Cookies de confirmation pour un an
    setcookie("form_success_values", json_encode($_POST), time() + 365 * 24 * 60 * 60, "/");

    echo "<div class='success'>";
    echo "<h3>Inscription réussie !</h3>";
    echo "<p>Votre login : <b>$login</b></p>";
    echo "<p>Gardez bien votre mot de passe.</p>";
    echo "<a href='login.php'>Se connecter</a>";
    echo "</div>";
}
?>
