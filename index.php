<?php
session_start();

// Vérification de connexion
if (!isset($_SESSION['user'])) {
    header("Location: pageconec.php");
    exit();
}

// Récupération des infos utilisateur
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Gestion des Tâches</title>
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
        .btn-custom {
            background-color: #4e73df;
            color: #fff;
            border-radius: 25px;
        }
        .btn-custom:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>

<div class="card text-center p-5" style="max-width: 600px;">
    <h1 class="mb-3">🎄 Application de Gestion des Tâches</h1>
    <p class="lead">Bienvenue  !<br>
       Gérez vos tâches facilement et efficacement.</p>

    <?php if ($user['role'] === 'admin'): ?>
        <a href="action.php" class="btn btn-success btn-lg mt-3">Gestion des utilisateurs</a>
        <a href="tache.php" class="btn btn-primary btn-lg mt-3">Gestion des tâches</a>
    <?php else: ?>
        <a href="tache.php" class="btn btn-primary btn-lg mt-3">Mes tâches</a>
    <?php endif; ?>
    
    <a href="logout.php" class="btn btn-danger btn-lg mt-3">Déconnexion</a>
</div>

</body>
</html>
