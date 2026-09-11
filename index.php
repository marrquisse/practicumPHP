<?php
// Крок 7 (підготовка): Увімкнення відображення помилок для зручності розробки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Крок 2: Оголосити масив даних свого домену (Варіант 6)
$recipes = [
    ['title' => 'Яєчня з беконом', 'cookTimeMin' => 10, 'servings' => 1, 'difficulty' => 'Легко'],
    ['title' => 'Борщ український', 'cookTimeMin' => 120, 'servings' => 6, 'difficulty' => 'Складно'],
    ['title' => 'Салат Цезар', 'cookTimeMin' => 20, 'servings' => 2, 'difficulty' => 'Легко'],
    ['title' => 'Паста Карбонара', 'cookTimeMin' => 25, 'servings' => 2, 'difficulty' => 'Середньо'],
    ['title' => 'Вівсянка з ягодами', 'cookTimeMin' => 15, 'servings' => 1, 'difficulty' => 'Легко'],
];

// Крок 3: Написати функцію форматування з типізованими параметрами
function formatRecipe(array $recipe): string {
    return "<b>{$recipe['title']}</b> (Порцій: {$recipe['servings']}, Складність: {$recipe['difficulty']})";
}

// Крок 6: Обчислити агрегатний показник (середній час приготування)
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
        /* Базове оформлення (Крок 1) */
        body { font-family: Arial, sans-serif; padding: 20px; }
        .recipe-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .badge { background-color: #4CAF50; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .aggregate { font-weight: bold; margin-top: 20px; padding: 10px; background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Моя Кулінарна Книга</h1>

    <!-- Крок 5: Вивести дані у вебсторінку за допомогою циклу foreach -->
    <?php foreach ($recipes as $recipe): ?>
        <?php
            // Крок 4: Додати умовну логіку (якщо час <= 20 хв)
            $isFast = $recipe['cookTimeMin'] <= 20;
        ?>
        <div class="recipe-card">
            <p>
                <!-- Використання функції форматування та скороченого синтаксису -->
                <?= formatRecipe($recipe) ?> <br>
                Час приготування: <?= $recipe['cookTimeMin'] ?> хв.
                
                <!-- Виведення умовної мітки -->
                <?php if ($isFast): ?>
                    <span class="badge">Швидкий рецепт</span>
                <?php endif; ?>
            </p>
        </div>
    <?php endforeach; ?>

    <!-- Крок 6 (Вивід): Вивести агрегатний показник окремим блоком -->
    <div class="aggregate">
        Середній час приготування по всіх рецептах: <?= $averageTime ?> хв.
    </div>
</body>
</html>