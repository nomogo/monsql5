<?php
session_start();

// Récupération des erreurs et des valeurs précédentes
$erreurs = isset($_COOKIE['form_errors']) ? json_decode($_COOKIE['form_errors'], true) : [];
$valeurs = isset($_COOKIE['form_values']) ? json_decode($_COOKIE['form_values'], true) : [];

setcookie("form_errors", "", time() - 3600, "/");
setcookie("form_values", "", time() - 3600, "/");

function old($name, $default = '') {
    global $valeurs;
    return isset($valeurs[$name]) ? htmlspecialchars($valeurs[$name]) : $default;
}

function oldChecked($name, $value) {
    global $valeurs;
    return (isset($valeurs[$name]) && in_array($value, (array)$valeurs[$name])) ? 'checked' : '';
}

function oldRadio($name, $value) {
    global $valeurs;
    return (isset($valeurs[$name]) && $valeurs[$name] === $value) ? 'checked' : '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscription</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        .checkbox-group label, .radio-group label { display: inline-block; margin-right: 10px; }
        .error { background: #fdd; color: #900; padding: 10px; margin-bottom: 15px; border: 1px solid #c00; }
        button { padding: 10px 20px; background: #0066cc; color: white; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #004999; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Inscription</h2>

    <?php if (!empty($erreurs)): ?>
        <div class="error">
            <ul>
                <?php foreach ($erreurs as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="process.php">
        <div class="form-group">
            <label for="nom_complet">Nom complet</label>
            <input type="text" id="nom_complet" name="nom_complet" value="<?= old('nom_complet') ?>" required>
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?= old('telephone') ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
        </div>

        <div class="form-group">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance" value="<?= old('date_naissance') ?>" required>
        </div>

        <div class="form-group radio-group">
            <label>Genre :</label>
            <label><input type="radio" name="genre" value="masculin" <?= oldRadio('genre', 'masculin') ?>> Masculin</label>
            <label><input type="radio" name="genre" value="feminin" <?= oldRadio('genre', 'feminin') ?>> Féminin</label>
        </div>

        <div class="form-group">
            <label for="biographie">Biographie</label>
            <textarea id="biographie" name="biographie"><?= old('biographie') ?></textarea>
        </div>

        <div class="form-group checkbox-group">
            <label>Langages préférés :</label>
            <?php
            $langages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskel", "Clojure", "Prolog", "Scala", "Go"];
            foreach ($langages as $langage): ?>
                <label><input type="checkbox" name="langages[]" value="<?= $langage ?>" <?= oldChecked('langages', $langage) ?>> <?= $langage ?></label>
            <?php endforeach; ?>
        </div>

        <div class="form-group">
            <label for="login">Identifiant (login)</label>
            <input type="text" id="login" name="login" value="<?= old('login') ?>" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="accord" <?= isset($valeurs['accord']) ? 'checked' : '' ?>> J’accepte les conditions</label>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</div>

</body>
</html>
