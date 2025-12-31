<?php
session_start();
require_once 'db.php'; // fichier de connexion PDO

// Vérification rôle admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: pageconec.php");
    exit;
}

// Ajouter un utilisateur
if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $motdepass = sha1($_POST['motdepass']); // sécurité basique
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, motdepass, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $motdepass, $role]);
    header("Location: users.php");
    exit;
}

// Modifier un utilisateur
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    if (!empty($_POST['motdepass'])) {
        $motdepass = sha1($_POST['motdepass']);
        $stmt = $pdo->prepare("UPDATE users SET username=?, motdepass=?, role=? WHERE id=?");
        $stmt->execute([$username, $motdepass, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->execute([$username, $role, $id]);
    }
    header("Location: users.php");
    exit;
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);
    header("Location: users.php");
    exit;
}

// Récupérer tous les utilisateurs
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2>Gestion des utilisateurs (Admin)</h2>

    <!-- Boutons Accueil et Déconnexion -->
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary">← Retour à l'accueil</a>
        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
    </div>

    <!-- Formulaire ajout utilisateur -->
    <form method="post" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="username" class="form-control" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="col-md-3">
                <input type="password" name="motdepass" class="form-control" placeholder="Mot de passe" required>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" name="add" class="btn btn-success w-100">Ajouter</button>
            </div>
        </div>
    </form>

    <!-- Tableau utilisateurs -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="4" class="text-center">Aucun utilisateur trouvé.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td>
                        <!-- Formulaire édition inline -->
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" required>
                            <input type="password" name="motdepass" placeholder="Nouveau mot de passe">
                            <select name="role">
                                <option value="user" <?= $u['role']=='user'?'selected':'' ?>>Utilisateur</option>
                                <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                            </select>
                            <button type="submit" name="edit" class="btn btn-primary btn-sm">Modifier</button>
                        </form>

                        <!-- Bouton supprimer -->
                        <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
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
