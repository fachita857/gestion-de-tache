<?php
session_start();
require_once 'db.php'; // connexion PDO

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $motdepass = trim($_POST['motdepass']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['motdepass'] === sha1($motdepass)) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'admin') {
            header("Location: index.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="card p-4" style="max-width: 400px; width: 100%;">
    <h3 class="text-center mb-3">🔑 Connexion</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="motdepass" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Se connecter</button>
    </form>

    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-link">⬅ Retour à l'accueil</a>
    </div>
</div>

</body>
</html>
