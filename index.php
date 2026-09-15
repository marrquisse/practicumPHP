<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Базовий масив рецептів (Практична 1)
$recipes = [
    ['title' => 'Яєчня з беконом', 'cookTimeMin' => 10, 'servings' => 1, 'difficulty' => 'Легко'],
    ['title' => 'Борщ український', 'cookTimeMin' => 120, 'servings' => 6, 'difficulty' => 'Складно'],
    ['title' => 'Салат Цезар', 'cookTimeMin' => 20, 'servings' => 2, 'difficulty' => 'Легко']
];

// Обробка форми (Практична 2)
$errors = [];
$successMessage = '';

// Перевіряємо, чи форму було надіслано методом POST[cite: 2]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Отримуємо дані з форми[cite: 2]
    $newTitle = trim($_POST['title'] ?? '');
    $newIngredients = trim($_POST['ingredients'] ?? '');
    $newCookTime = (int)($_POST['cookTimeMin'] ?? 0);
    $unit = $_POST['unit'] ?? 'g';

    // Серверна валідація[cite: 2]
    if ($newTitle === '') {
        $errors['title'] = 'Назва рецепта обов\'язкова.';
    }
    // Перевірка, щоб час був більше 0[cite: 2]
    if ($newCookTime <= 0) {
        $errors['cookTimeMin'] = 'Час приготування має бути більше 0.';
    }
    // Перевірка, щоб інгредієнти не були порожніми[cite: 2]
    if ($newIngredients === '') {
        $errors['ingredients'] = 'Додайте хоча б один інгредієнт.';
    }

    // Якщо помилок немає, виводимо підтвердження[cite: 2]
    if (empty($errors)) {
        $successMessage = "Рецепт «" . htmlspecialchars($newTitle) . "» успішно додано!";
        
        // Додаємо новий рецепт у наш масив, щоб він з'явився на сторінці
        $recipes[] = [
            'title' => htmlspecialchars($newTitle),
            'cookTimeMin' => $newCookTime,
            'servings' => 2, // ставимо за замовчуванням
            'difficulty' => 'Середньо' // ставимо за замовчуванням
        ];

        // Очищаємо поля форми після успіху
        $newTitle = $newIngredients = '';
        $newCookTime = 0;
    }
}

// Розрахунок середнього часу
$totalTime = 0;
foreach ($recipes as $recipe) {
    $totalTime += $recipe['cookTimeMin'];
}
$averageTime = count($recipes) > 0 ? round($totalTime / count($recipes), 1) : 0;
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Кулінарна книга</title>
    <style>
  
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1, h2 { color: #2c3e50; }
        

        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; }
        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; 
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover { background-color: #45a049; }
 
        .error-text { color: red; font-size: 0.85em; display: block; margin-top: 5px; }
        .success-alert { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        

        .recipe-card {
            border: 1px solid #eee;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .badge {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 10px;
        }
        .aggregate { font-weight: bold; margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Моя Кулінарна Книга</h1>

        <?php if ($successMessage): ?>
            <div class="success-alert"><?= $successMessage ?></div>
        <?php endif; ?>

        <h2>Додати новий рецепт</h2>
        <form method="POST" action="index.php" id="recipeForm">
            <div class="form-group">
                <label>Назва рецепта:</label>
                <!-- Підставляємо введені дані назад у форму[cite: 2] -->
                <input type="text" name="title" id="title" required value="<?= htmlspecialchars($newTitle ?? '') ?>">
                <?php if (isset($errors['title'])) echo "<span class='error-text'>{$errors['title']}</span>"; ?>
            </div>

            <div class="form-group">
                <label>Час приготування (хв):</label>
                <!-- HTML5 валідація[cite: 2] -->
                <input type="number" name="cookTimeMin" id="cookTimeMin" min="1" required value="<?= htmlspecialchars((string)($newCookTime ?? '')) ?>">
                <?php if (isset($errors['cookTimeMin'])) echo "<span class='error-text'>{$errors['cookTimeMin']}</span>"; ?>
            </div>

            <div class="form-group">
                <label>Одиниця виміру:</label>
                <select name="unit" id="unitSelect">
                    <option value="g">Грами (g)</option>
                    <option value="oz">Унції (oz)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Інгредієнти:</label>
                <textarea name="ingredients" id="ingredients" rows="3" required><?= htmlspecialchars($newIngredients ?? '') ?></textarea>
                <?php if (isset($errors['ingredients'])) echo "<span class='error-text'>{$errors['ingredients']}</span>"; ?>
            </div>

            <button type="submit">Зберегти рецепт</button>
        </form>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

        <h2>Існуючі рецепти</h2>
        <?php foreach ($recipes as $recipe): ?>
            <?php $isFast = $recipe['cookTimeMin'] <= 20; ?>
            <div class="recipe-card">
                <b><?= htmlspecialchars($recipe['title']) ?></b>
                <span style="color: #666; font-size: 0.9em;">(Порцій: <?= $recipe['servings'] ?>, Складність: <?= $recipe['difficulty'] ?>)</span>
                <br>
                Час приготування: <?= $recipe['cookTimeMin'] ?> хв.
                <?php if ($isFast): ?>
                    <span class="badge">Швидкий рецепт</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="aggregate">
            Середній час: <?= $averageTime ?> хв.
        </div>
    </div>

    <!-- Клієнтська валідація та localStorage[cite: 2] -->
    <script>
        const unitSelect = document.getElementById('unitSelect');
        const form = document.getElementById('recipeForm');

        // Відновлення налаштувань із localStorage[cite: 2]
        const savedUnit = localStorage.getItem('preferredUnit');
        if (savedUnit) unitSelect.value = savedUnit;

        // Збереження в localStorage при зміні[cite: 2]
        unitSelect.addEventListener('change', () => {
            localStorage.setItem('preferredUnit', unitSelect.value);
        });

        // JavaScript валідація перед відправкою[cite: 2]
        form.addEventListener('submit', (event) => {
            const cookTime = parseInt(document.getElementById('cookTimeMin').value, 10);
            const ingredients = document.getElementById('ingredients').value.trim();

            if (cookTime <= 0 || ingredients === '') {
                alert('Перевірте правильність заповнення полів (час > 0, інгредієнти не порожні)!');
                event.preventDefault(); // Зупиняємо відправку[cite: 2]
            }
        });
    </script>
</body>
</html>