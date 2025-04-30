<?php
// Démarre la session
session_start();

// Récupération des cookies d’erreurs et de valeurs précédentes
$errors = isset($_COOKIE['form_errors']) ? json_decode($_COOKIE['form_errors'], true) : [];
$values = isset($_COOKIE['form_values']) ? json_decode($_COOKIE['form_values'], true) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscription</title>
</head>
<body>
    <h1>Formulaire d'inscription</h1>

    <?php if (!empty($_SESSION['login']) && !empty($_SESSION['mot_de_passe'])): ?>
        <div style="background-color: #e0ffe0; padding: 10px;">
            <strong>Identifiants générés :</strong><br>
            Login : <?= htmlspecialchars($_SESSION['login']) ?><br>
            Mot de passe : <?= htmlspecialchars($_SESSION['mot_de_passe']) ?>
        </div>
        <?php
            // Supprimer les identifiants de la session après affichage
            unset($_SESSION['login'], $_SESSION['mot_de_passe']);
        ?>
    <?php endif; ?>

    <form action="process.php" method="POST">
        <label>Nom complet :
            <input type="text" name="nom_complet" value="<?= htmlspecialchars($values['nom_complet'] ?? '') ?>">
        </label>
        <span style="color: red;"><?= $errors['nom_complet'] ?? '' ?></span>
        <br><br>

        <label>Téléphone :
            <input type="text" name="telephone" value="<?= htmlspecialchars($values['telephone'] ?? '') ?>">
        </label>
        <span style="color: red;"><?= $errors['telephone'] ?? '' ?></span>
        <br><br>

        <label>Email :
            <input type="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>">
        </label>
        <span style="color: red;"><?= $errors['email'] ?? '' ?></span>
        <br><br>

        <label>Date de naissance :
            <input type="date" name="date_naissance" value="<?= htmlspecialchars($values['date_naissance'] ?? '') ?>">
        </label>
        <br><br>

        <label>Genre :</label><br>
        <label><input type="radio" name="genre" value="masculin" <?= (isset($values['genre']) && $values['genre'] == 'masculin') ? 'checked' : '' ?>> Masculin</label>
        <label><input type="radio" name="genre" value="feminin" <?= (isset($values['genre']) && $values['genre'] == 'feminin') ? 'checked' : '' ?>> Féminin</label>
        <span style="color: red;"><?= $errors['genre'] ?? '' ?></span>
        <br><br>

        <label>Langages de programmation :</label><br>
        <?php
        $langages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskel", "Clojure", "Prolog", "Scala", "Go"];
        foreach ($langages as $langage) {
            $checked = (isset($values['langages']) && in_array($langage, $values['langages'])) ? 'checked' : '';
            echo "<label><input type='checkbox' name='langages[]' value='$langage' $checked> $langage</label><br>";
        }
        ?>
        <span style="color: red;"><?= $errors['langages'] ?? '' ?></span>
        <br>

        <label>Biographie :</label><br>
        <textarea name="biographie" rows="5" cols="40"><?= htmlspecialchars($values['biographie'] ?? '') ?></textarea>
        <span style="color: red;"><?= $errors['biographie'] ?? '' ?></span>
        <br><br>

        <label><input type="checkbox" name="accord" value="1" <?= (isset($values['accord']) && $values['accord']) ? 'checked' : '' ?>> J'accepte le contrat</label>
        <span style="color: red;"><?= $errors['accord'] ?? '' ?></span>
        <br><br>

        <input type="submit" value="Envoyer">
    </form>

</body>
</html>

<?php
// Nettoyage des cookies après affichage
setcookie("form_errors", "", time() - 3600, "/");
setcookie("form_values", "", time() - 3600, "/");
?>
