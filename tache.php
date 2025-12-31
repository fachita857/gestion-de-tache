<?php
session_start();
require_once 'db.php';

// Vérification si connecté
if (!isset($_SESSION['user'])) {
    header("Location: pageconec.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// Ajouter une tâche
if (isset($_POST['add'])) {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $stmt = $pdo->prepare("INSERT INTO taches (user_id, titre, description) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $titre, $description]);
    header("Location: tache.php");
    exit;
}

// Modifier une tâche
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE taches SET titre=?, description=?, status=? WHERE id=?");
    $stmt->execute([$titre, $description, $status, $id]);
    header("Location: tache.php");
    exit;
}

// Supprimer une tâche
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM taches WHERE id=?");
    $stmt->execute([$id]);
    header("Location: tache.php");
    exit;
}

// Récupérer les tâches
if ($role === 'admin') {
    $stmt = $pdo->query("SELECT taches.*, users.username 
                         FROM taches JOIN users ON taches.user_id = users.id 
                         ORDER BY taches.id DESC");
} else {
    $stmt = $pdo->prepare("SELECT taches.*, users.username 
                           FROM taches JOIN users ON taches.user_id = users.id 
                           WHERE user_id=? ORDER BY taches.id DESC");
    $stmt->execute([$user_id]);
}
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>📋 Gestion des tâches</h2>
    <p>Bienvenue <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong> (<?= $role ?>)</p>

    <!-- Boutons retour Accueil et Déconnexion en haut -->
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary">← Retour à l'accueil</a>
        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
    </div>

    <!-- Formulaire ajout -->
    <form method="post" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="titre" class="form-control" placeholder="Titre" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="description" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-2">
                <button type="submit" name="add" class="btn btn-success w-100">Ajouter</button>
            </div>
        </div>
    </form>

    <!-- Tableau des tâches -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Titre</th><th>Description</th><th>Statut</th>
                <?php if ($role === 'admin'): ?><th>Utilisateur</th><?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($taches)): ?>
                <tr><td colspan="<?= $role === 'admin' ? 6 : 5 ?>" class="text-center">Aucune tâche enregistrée.</td></tr>
            <?php else: ?>
                <?php foreach ($taches as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['titre']) ?></td>
                    <td><?= htmlspecialchars($t['description']) ?></td>
                    <td><?= $t['status'] ?></td>
                    <?php if ($role === 'admin'): ?><td><?= htmlspecialchars($t['username']) ?></td><?php endif; ?>
                    <td>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <input type="text" name="titre" value="<?= htmlspecialchars($t['titre']) ?>" required>
                            <input type="text" name="description" value="<?= htmlspecialchars($t['description']) ?>">
                            <select name="status">
                                <option value="en_cours" <?= $t['status']=='en_cours'?'selected':'' ?>>En cours</option>
                                <option value="terminee" <?= $t['status']=='terminee'?'selected':'' ?>>Terminée</option>
                            </select>
                            <button type="submit" name="edit" class="btn btn-primary btn-sm">Modifier</button>
                        </form>
                        <a href="tache.php?delete=<?= $t['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cette tâche ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Bouton retour Accueil en bas -->
    <a href="index.php" class="btn btn-secondary mt-3">← Retour à l'accueil</a>

</body>
</html>
