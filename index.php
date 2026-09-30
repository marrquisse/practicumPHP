<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Підключення БД та бібліотек
require_once 'db.php';
require_once 'lib/functions.php';
require_once 'classes/Cookbook.php';

$cookbook = new Cookbook($pdo);
$message = '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $title = trim($_POST['title'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $cookTime = (int)($_POST['cookTimeMin'] ?? 0);

    if ($title !== '' && $ingredients !== '' && $cookTime > 0) {
        if ($action === 'add') {
            $cookbook->addRecipe($title, $ingredients, $cookTime);
            $message = "Рецепт успішно додано!";
        } elseif ($action === 'edit' && !empty($_POST['id'])) {
            $cookbook->updateRecipe((int)$_POST['id'], $title, $ingredients, $cookTime);
            $message = "Рецепт успішно оновлено!";
        }
    } else {
        $message = "Помилка: перевірте правильність заповнення полів.";
    }
}


if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && !empty($_GET['id'])) {
        $cookbook->deleteRecipe((int)$_GET['id']);
        header('Location: index.php'); // Перенаправлення після видалення
        exit;
    } elseif ($_GET['action'] === 'edit' && !empty($_GET['id'])) {
        $editData = $cookbook->getById((int)$_GET['id']);
    }
}

$recipes = $cookbook->getAll();
$fastest = $cookbook->shortestCookTime();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моя Кулінарна Книга</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6b46c1; --accent: #ecc94b; --accent-hover: #d69e2e;
            --bg: #f7fafc; --card-bg: #ffffff; --text-main: #2d3748;
            --text-muted: #718096; --border: #e2e8f0; --danger: #e53e3e;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); margin: 0; padding: 0; line-height: 1.6; }
        .header { background-color: var(--card-bg); padding: 24px 0; border-bottom: 1px solid var(--border); text-align: center; margin-bottom: 40px; }
        .header h1 { margin: 0; color: var(--primary); font-weight: 700; font-size: 2rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 20px; display: grid; grid-template-columns: 350px 1fr; gap: 40px; }
        @media (max-width: 850px) { .container { grid-template-columns: 1fr; } }
        
        .form-panel { background: var(--card-bg); padding: 30px; border-radius: 16px; border: 1px solid var(--border); height: fit-content; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9rem; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        input:focus, textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1); }
        
        button { width: 100%; background-color: var(--accent); color: #1a202c; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 1rem; }
        button:hover { background-color: var(--accent-hover); }
        .btn-cancel { display: block; text-align: center; margin-top: 10px; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; }
        
        .alert { background-color: #f0fff4; color: #276749; padding: 12px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #c6f6d5; }
        
        .recipes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
        .recipe-card { background: var(--card-bg); padding: 24px; border-radius: 16px; border: 1px solid var(--border); position: relative; border-top: 4px solid var(--primary); }
        .recipe-card b { font-size: 1.15rem; display: block; margin-bottom: 12px; }
        .recipe-card small { color: var(--text-muted); display: block; margin-top: 16px; }
        
        .card-actions { margin-top: 15px; display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid var(--border); }
        .card-actions a { text-decoration: none; font-size: 0.85rem; font-weight: 600; }
        .btn-edit { color: var(--primary); }
        .btn-delete { color: var(--danger); }

        .stats-panel { background-color: var(--primary); color: white; padding: 24px; border-radius: 16px; margin-top: 40px; grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between; }
        .stats-panel h3 { margin: 0; font-weight: 500; }
        
    
        .tip-box { background: #ebf4ff; color: #3182ce; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header">
        <h1>Книга Рецептів</h1>
    </header>

    <div class="container">
        
        <aside class="form-panel">
            <h2><?= $editData ? 'Редагувати рецепт' : 'Додати рецепт' ?></h2>
            <?php if ($message): ?>
                <div class="alert"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'add' ?>">
                <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Назва страви</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($editData['title'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Час приготування (хв)</label>
                    <input type="number" name="cookTimeMin" min="1" required value="<?= htmlspecialchars((string)($editData['cook_time_min'] ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label>Інгредієнти</label>
                    <textarea name="ingredients" rows="3" required><?= htmlspecialchars($editData['ingredients'] ?? '') ?></textarea>
                </div>
                
                <button type="submit"><?= $editData ? 'Зберегти зміни' : 'Додати до колекції' ?></button>
                <?php if ($editData): ?>
                    <a href="index.php" class="btn-cancel">Скасувати редагування</a>
                <?php endif; ?>
            </form>

           
            <div class="tip-box">
                <b>💡 Кулінарна порада:</b><br>
                Часто в рецептах вказують вагу в грамах. Наприклад, 250 г борошна — це приблизно <b><?= convertGramsToOz(250) ?> унцій</b>. 
            </div>
        </aside>

        <main>
            <h2 style="margin-top: 0;">Колекція рецептів</h2>
            <div class="recipes-grid">
                <?php foreach ($recipes as $row): ?>
                    <div class="recipe-card">
                        <b><?= htmlspecialchars($row['title']) ?></b>
                        <div>⏱ <?= $row['cook_time_min'] ?> хв.</div>
                        <small>Інгредієнти: <?= htmlspecialchars($row['ingredients']) ?></small>
                        
                        <div class="card-actions">
                            <a href="?action=edit&id=<?= $row['id'] ?>" class="btn-edit">Редагувати</a>
                            <a href="?action=delete&id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Ви впевнені, що хочете видалити цей рецепт?');">Видалити</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <div class="stats-panel">
            <h3>Найшвидший рецепт</h3>
            <div style="text-align: right;">
                <?php if ($fastest): ?>
                    <b style="font-size: 1.2rem;"><?= htmlspecialchars($fastest['title']) ?></b><br>
                    Всього <?= $fastest['cook_time_min'] ?> хв.
                <?php else: ?>
                    Немає збережених рецептів
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>