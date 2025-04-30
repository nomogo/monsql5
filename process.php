<?php
session_start();

// 1. Configuration de la base de données
$host = "localhost";
$username = "u68658";
$password = "7975806";
$database = "u68658";

// 2. Fonction pour sécuriser les données
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// 3. Récupération des données POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_complet = sanitize($_POST["nom_complet"] ?? '');
    $telephone = sanitize($_POST["telephone"] ?? '');
    $email = sanitize($_POST["email"] ?? '');
    $date_naissance = sanitize($_POST["date_naissance"] ?? '');
    $genre = sanitize($_POST["genre"] ?? '');
    $biographie = sanitize($_POST["biographie"] ?? '');
    $accord = isset($_POST["accord"]) ? 1 : 0;
    $langages = isset($_POST["langages"]) ? $_POST["langages"] : [];

    // 4. Validation avec regex
    $erreurs = [];

    if (!preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/u", $nom_complet)) {
        $erreurs['nom_complet'] = "Seules les lettres, espaces et tirets sont autorisés.";
    }

    if (!preg_match("/^[0-9\s\-\+]{7,}$/", $telephone)) {
        $erreurs['telephone'] = "Le numéro de téléphone doit contenir au moins 7 chiffres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "L'adresse e-mail est invalide.";
    }

    if (!in_array($genre, ["masculin", "feminin"])) {
        $erreurs['genre'] = "Le genre sélectionné est invalide.";
    }

    if (empty($langages)) {
        $erreurs['langages'] = "Sélectionnez au moins un langage.";
    } else {
        $langages_autorises = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskel", "Clojure", "Prolog", "Scala", "Go"];
        foreach ($langages as $langage) {
            if (!in_array($langage, $langages_autorises)) {
                $erreurs['langages'] = "Langage invalide sélectionné.";
                break;
            }
        }
    }

    if (strlen($biographie) < 10) {
        $erreurs['biographie'] = "La biographie doit contenir au moins 10 caractères.";
    }

    if (!$accord) {
        $erreurs['accord'] = "Vous devez accepter le contrat.";
    }

    // 5. Gestion des erreurs avec cookies
    if (!empty($erreurs)) {
        setcookie("form_errors", json_encode($erreurs), 0, "/");
        setcookie("form_values", json_encode($_POST), 0, "/");
        header("Location: index.php");
        exit();
    }

    // 6. Connexion à la base de données
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur DB : " . $e->getMessage());
    }

    // 7. Génération login/mot de passe
    $login = strtolower(explode(" ", $nom_complet)[0]) . rand(1000, 9999);
    $mot_de_passe = bin2hex(random_bytes(4)); // mot de passe aléatoire
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // 8. Insertion dans users
    try {
        $stmt = $pdo->prepare("INSERT INTO users (nom_complet, telephone, email, date_naissance, genre, biographie, accord, login, mot_de_passe_hash)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom_complet, $telephone, $email, $date_naissance, $genre, $biographie, $accord, $login, $mot_de_passe_hash]);
        $user_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        die("Erreur utilisateur : " . $e->getMessage());
    }

    // 9. Insertion dans user_languages
    try {
        $stmt_lang = $pdo->prepare("INSERT INTO user_languages (utilisateur_id, langage) VALUES (?, ?)");
        foreach ($langages as $langage) {
            $stmt_lang->execute([$user_id, $langage]);
        }
    } catch (PDOException $e) {
        die("Erreur langages : " . $e->getMessage());
    }

    // 10. Enregistrement cookies de succès (1 an)
    setcookie("form_success_values", json_encode($_POST), time() + 365 * 24 * 60 * 60, "/");

    // 11. Stocker en session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['login'] = $login;
    $_SESSION['mot_de_passe'] = $mot_de_passe;

    // 12. Redirection vers page de succès
    header("Location: success.php");
    exit();
}
?>
