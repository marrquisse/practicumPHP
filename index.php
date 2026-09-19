<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/functions.php';
require_once 'classes/Recipe.php';
require_once 'classes/VeganRecipe.php';
require_once 'classes/Cookbook.php';

$cookbook = new Cookbook();
$cookbook->addRecipe(new Recipe('Яєчня з беконом', ['яйця', 'бекон', 'сіль', 'перець'], 10));
$cookbook->addRecipe(new Recipe('Борщ український', ['буряк', 'картопля', 'м\'ясо', 'капуста', 'морква'], 120));
$cookbook->addRecipe(new VeganRecipe('Салат Цезар (Веган)', ['тофу', 'салат айсберг', 'веганський майонез', 'сухарики'], 20, 'Тофу замість курки, соя замість яєць'));

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = trim($_POST['title'] ?? '');
    $newIngredientsStr = trim($_POST['ingredients'] ?? '');
    $newCookTime = (int)($_POST['cookTimeMin'] ?? 0);
    $isVegan = isset($_POST['is_vegan']);
    $substitutions = trim($_POST['substitutions'] ?? '');

    if ($newTitle === '') $errors['title'] = 'Назва обов\'язкова.';
    if ($newCookTime <= 0) $errors['cookTimeMin'] = 'Час має бути більше 0.';
    if ($newIngredientsStr === '') $errors['ingredients'] = 'Додайте інгредієнти.';

    if (empty($errors)) {
        $ingredientsArr = explode(',', $newIngredientsStr);
        
        if ($isVegan) {
            $newRecipe = new VeganRecipe($newTitle, $ingredientsArr, $newCookTime, $substitutions);
        } else {
            $newRecipe = new Recipe($newTitle, $ingredientsArr, $newCookTime);
        }
        
        $cookbook->addRecipe($newRecipe);
        $successMessage = "Рецепт успішно збережено!";
        
        $newTitle = $newIngredientsStr = $substitutions = '';
        $newCookTime = 0;
    }
}
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
            --primary: #6b46c1; 
            --accent: #ecc94b;
            --accent-hover: #d69e2e;
            --vegan: #48bb78;
            --bg: #f7fafc;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .header {
            background-color: var(--card-bg);
            padding: 24px 0;
            border-bottom: 1px solid var(--border);
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            margin: 0;
            color: var(--primary);
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: -0.5px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 40px;
        }

        @media (max-width: 850px) {
            .container { grid-template-columns: 1fr; }
        }

        /* Форма (Мінімалізм) */
        .form-panel {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border);
            height: fit-content;
        }

        .form-panel h2 {
            margin-top: 0;
            font-size: 1.25rem;
            color: var(--text-main);
            margin-bottom: 24px;
        }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-main); }
        
        input[type="text"], input[type="number"], textarea {
            width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;
            box-sizing: border-box; font-family: 'Inter', sans-serif; font-size: 0.95rem;
            transition: all 0.2s ease; background-color: #fcfcfc;
        }
        
        input:focus, textarea:focus { 
            border-color: var(--primary); 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1); 
            background-color: #fff;
        }
        
        .checkbox-group { display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.95rem;}
        .checkbox-group input { width: 18px; height: 18px; accent-color: var(--primary); }
        
        button {
            width: 100%; background-color: var(--accent); color: #1a202c; border: none;
            padding: 14px; border-radius: 8px; cursor: pointer; font-weight: 700;
            font-size: 1rem; transition: background-color 0.2s;
        }
        button:hover { background-color: var(--accent-hover); }

        .success-alert { background-color: #f0fff4; color: #276749; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.95rem; border: 1px solid #c6f6d5;}

        /* Сітка рецептів */
        .recipes-header {
            font-size: 1.25rem;
            margin-top: 0;
            margin-bottom: 24px;
            color: var(--text-main);
        }

        .recipes-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;
        }

        .recipe-card {
            background: var(--card-bg); padding: 24px; border-radius: 16px;
            border: 1px solid var(--border);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .recipe-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background-color: var(--primary);
        }

        .recipe-card.vegan-card::before { background-color: var(--vegan); }

        .recipe-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }

        .recipe-card b { font-size: 1.15rem; color: var(--text-main); display: block; margin-bottom: 12px; }
        .recipe-card small { color: var(--text-muted); display: block; margin-top: 16px; line-height: 1.5; font-size: 0.85rem;}

        /* Статистика */
        .stats-panel {
            background-color: var(--primary);
            color: white;
            padding: 24px; border-radius: 16px; margin-top: 40px;
            grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between;
        }
        .stats-panel h3 { margin: 0; font-size: 1.2rem; font-weight: 500; opacity: 0.9;}
        .stats-content { font-size: 1.1rem; font-weight: 700; text-align: right;}
        
        /* Корегування тексту всередині блоку статистики */
        .stats-content b { font-size: 1.2rem; display: block; }
        .stats-content small { font-weight: 400; opacity: 0.8; font-size: 0.85rem;}
    </style>
</head>
<body>
    <header class="header">
        <h1>Книга Рецептів</h1>
    </header>

    <div class="container">
        
        <aside class="form-panel">
            <h2>Додати рецепт</h2>
            <?php if ($successMessage): ?>
                <div class="success-alert"><?= $successMessage ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <div class="form-group">
                    <label>Назва страви</label>
                    <input type="text" name="title" required placeholder="Наприклад: Сирники" value="<?= htmlspecialchars($newTitle ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Час приготування (хв)</label>
                    <input type="number" name="cookTimeMin" min="1" required placeholder="20" value="<?= htmlspecialchars((string)($newCookTime ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label>Інгредієнти (через кому)</label>
                    <textarea name="ingredients" rows="3" required placeholder="Сир, яйця, борошно..."><?= htmlspecialchars($newIngredientsStr ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_vegan" id="veganCheck"> 
                        Веганська альтернатива
                    </label>
                </div>
                <div class="form-group" id="subsGroup" style="display: none;">
                    <label>Замінники тваринних продуктів</label>
                    <input type="text" name="substitutions" placeholder="Тофу замість сиру" value="<?= htmlspecialchars($substitutions ?? '') ?>">
                </div>
                <button type="submit">Зберегти рецепт</button>
            </form>
        </aside>

        <main>
            <h2 class="recipes-header">Колекція рецептів</h2>
            <div class="recipes-grid">
                <?php foreach ($cookbook->getAll() as $recipeObj): ?>
                    <div class="recipe-card <?= ($recipeObj instanceof VeganRecipe) ? 'vegan-card' : '' ?>">
                        <?= $recipeObj->getInfo() ?> 
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <div class="stats-panel">
            <h3>Найшвидший рецепт</h3>
            <div class="stats-content">
                <?php $fastest = $cookbook->shortestCookTime(); ?>
                <?= $fastest ? $fastest->getInfo() : 'Немає збережених рецептів' ?> 
            </div>
        </div>

    </div>

    <script>
        const veganCheck = document.getElementById('veganCheck');
        const subsGroup = document.getElementById('subsGroup');
        
        veganCheck.addEventListener('change', function() {
            subsGroup.style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>