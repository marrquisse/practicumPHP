<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/security.php';

$csrfToken = csrfToken();

?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="<?= e($csrfToken) ?>"
    >

    <title>Моя Кулінарна Книга</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap"
        rel="stylesheet"
    >

    <style>
        :root {
            --primary: #6b46c1;
            --accent: #ecc94b;
            --accent-hover: #d69e2e;
            --bg: #f7fafc;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --border: #e2e8f0;
            --danger: #e53e3e;
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
            .container {
                grid-template-columns: 1fr;
            }
        }

        .form-panel {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        input:focus,
        textarea:focus {
            border-color: var(--primary);
            outline: none;
        }

        button {
            width: 100%;
            background-color: var(--accent);
            color: #1a202c;
            border: none;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
        }

        button:hover {
            background-color: var(--accent-hover);
        }

        button:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .btn-cancel {
            display: none;
            width: 100%;
            margin-top: 15px;
            padding: 0;
            background: transparent;
            color: var(--text-muted);
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-cancel:hover {
            background: transparent;
            color: var(--text-main);
        }

        #messageBox {
            display: none;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .alert-success {
            background-color: #f0fff4;
            color: #276749;
            border: 1px solid #c6f6d5;
        }

        .alert-error {
            background-color: #fff5f5;
            color: #c53030;
            border: 1px solid #fed7d7;
        }

        .search-box {
            margin-bottom: 24px;
        }

        .recipes-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .recipe-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border);
            border-top: 4px solid var(--primary);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .recipe-title {
            font-size: 1.15rem;
            font-weight: 700;
            display: block;
            margin-bottom: 12px;
        }

        .recipe-ingredients {
            color: var(--text-muted);
            display: block;
            margin-top: 16px;
            overflow-wrap: anywhere;
        }

        .card-actions {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
        }

        .card-action {
            width: auto;
            padding: 0;
            background: transparent;
            border: none;
            border-radius: 0;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .card-action:hover {
            background: transparent;
        }

        .btn-edit {
            color: var(--primary);
        }

        .btn-delete {
            color: var(--danger);
        }

        .empty-state {
            color: var(--text-muted);
            grid-column: 1 / -1;
        }

        .load-error {
            color: #c53030;
            background: #fff5f5;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #fed7d7;
            grid-column: 1 / -1;
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Книга Рецептів</h1>
</header>

<div class="container">

    <aside class="form-panel">

        <h2 id="formTitle">
            Додати рецепт
        </h2>

        <div
            id="messageBox"
            role="status"
            aria-live="polite"
        ></div>

        <form id="recipeForm">

            <input
                type="hidden"
                id="recipeId"
            >

            <div class="form-group">
                <label for="title">
                    Назва страви
                </label>

                <input
                    type="text"
                    id="title"
                    maxlength="255"
                    autocomplete="off"
                    required
                >
            </div>

            <div class="form-group">
                <label for="cookTimeMin">
                    Час приготування (хв)
                </label>

                <input
                    type="number"
                    id="cookTimeMin"
                    min="1"
                    step="1"
                    required
                >
            </div>

            <div class="form-group">
                <label for="ingredients">
                    Інгредієнти (через кому)
                </label>

                <textarea
                    id="ingredients"
                    rows="3"
                    maxlength="5000"
                    required
                ></textarea>
            </div>

            <button
                type="submit"
                id="submitBtn"
            >
                Додати до колекції
            </button>

            <button
                type="button"
                class="btn-cancel"
                id="cancelEditBtn"
            >
                Скасувати редагування
            </button>

        </form>

    </aside>

    <main>

        <div class="search-box">
            <label
                for="search"
                style="position:absolute;left:-9999px;"
            >
                Пошук рецептів
            </label>

            <input
                type="text"
                id="search"
                placeholder="Пошук за інгредієнтом або назвою..."
                autocomplete="off"
            >
        </div>

        <h2 style="margin-top: 0;">
            Колекція рецептів
        </h2>

        <div
            id="results"
            class="recipes-grid"
        >
            <p class="empty-state">
                Завантаження даних...
            </p>
        </div>

    </main>

</div>

<script>
    const resultsContainer =
        document.getElementById('results');

    const searchInput =
        document.getElementById('search');

    const recipeForm =
        document.getElementById('recipeForm');

    const messageBox =
        document.getElementById('messageBox');

    const submitBtn =
        document.getElementById('submitBtn');

    const cancelEditBtn =
        document.getElementById('cancelEditBtn');

    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            .content;

    let recipesById = new Map();

    async function readJsonResponse(response) {
        let data;

        try {
            data = await response.json();
        } catch {
            throw new Error(
                'Сервер повернув некоректну відповідь.'
            );
        }

        if (!response.ok) {
            throw new Error(
                data.error || `Помилка сервера: ${response.status}`
            );
        }

        return data;
    }

    function createRecipeCard(recipe) {
        const card = document.createElement('article');
        card.className = 'recipe-card';

        const title = document.createElement('span');
        title.className = 'recipe-title';

        title.textContent = recipe.title;

        const time = document.createElement('div');
        time.textContent =
            `⏱ ${Number(recipe.cook_time_min)} хв.`;

        const ingredients =
            document.createElement('small');

        ingredients.className =
            'recipe-ingredients';

        ingredients.textContent =
            `Інгредієнти: ${recipe.ingredients}`;

        const actions =
            document.createElement('div');

        actions.className = 'card-actions';

        const editButton =
            document.createElement('button');

        editButton.type = 'button';
        editButton.className =
            'card-action btn-edit';

        editButton.textContent =
            'Редагувати';

        editButton.addEventListener(
            'click',
            () => editRecipe(recipe.id)
        );

        const deleteButton =
            document.createElement('button');

        deleteButton.type = 'button';
        deleteButton.className =
            'card-action btn-delete';

        deleteButton.textContent =
            'Видалити';

        deleteButton.addEventListener(
            'click',
            () => deleteRecipe(recipe.id)
        );

        actions.append(
            editButton,
            deleteButton
        );

        card.append(
            title,
            time,
            ingredients,
            actions
        );

        return card;
    }

    async function loadRecipes(query = '') {
        resultsContainer.replaceChildren();

        const loading = document.createElement('p');
        loading.className = 'empty-state';
        loading.textContent = 'Завантаження даних...';

        resultsContainer.append(loading);

        try {
            const response = await fetch(
                'api_list.php?q='
                + encodeURIComponent(query),
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const data =
                await readJsonResponse(response);

            if (!Array.isArray(data)) {
                throw new Error(
                    'Некоректний формат відповіді сервера.'
                );
            }

            resultsContainer.replaceChildren();

            recipesById = new Map(
                data.map(recipe => [
                    Number(recipe.id),
                    recipe
                ])
            );

            if (data.length === 0) {
                const empty =
                    document.createElement('p');

                empty.className = 'empty-state';
                empty.textContent =
                    'Рецептів не знайдено.';

                resultsContainer.append(empty);

                return;
            }

            const fragment =
                document.createDocumentFragment();

            for (const recipe of data) {
                fragment.append(
                    createRecipeCard(recipe)
                );
            }

            resultsContainer.append(fragment);

        } catch (error) {
            resultsContainer.replaceChildren();

            const errorBox =
                document.createElement('div');

            errorBox.className = 'load-error';

            errorBox.textContent =
                'Помилка завантаження: '
                + error.message;

            resultsContainer.append(errorBox);
        }
    }

    searchInput.addEventListener(
        'input',
        event => {
            loadRecipes(event.target.value);
        }
    );

    recipeForm.addEventListener(
        'submit',
        async event => {
            event.preventDefault();

            const id =
                document
                    .getElementById('recipeId')
                    .value;

            const title =
                document
                    .getElementById('title')
                    .value
                    .trim();

            const ingredients =
                document
                    .getElementById('ingredients')
                    .value
                    .trim();

            const cookTimeMin =
                Number(
                    document
                        .getElementById('cookTimeMin')
                        .value
                );

            if (
                title === ''
                || ingredients === ''
                || !Number.isInteger(cookTimeMin)
                || cookTimeMin <= 0
            ) {
                showMessage(
                    'Перевірте правильність заповнення полів.',
                    'error'
                );

                return;
            }

            const recipeData = {
                id: id === ''
                    ? 0
                    : Number(id),

                title,
                ingredients,
                cookTimeMin
            };

            submitBtn.disabled = true;

            try {
                const response =
                    await fetch(
                        'api_add.php',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-Token':
                                    csrfToken
                            },

                            body:
                                JSON.stringify(
                                    recipeData
                                )
                        }
                    );

                const result =
                    await readJsonResponse(
                        response
                    );

                if (!result.success) {
                    throw new Error(
                        result.error
                        || 'Не вдалося зберегти рецепт.'
                    );
                }

                showMessage(
                    result.message
                    || (
                        id === ''
                            ? 'Рецепт успішно додано.'
                            : 'Рецепт успішно оновлено.'
                    ),
                    'success'
                );

                resetForm();

                await loadRecipes(
                    searchInput.value
                );

            } catch (error) {
                showMessage(
                    'Помилка: '
                    + error.message,
                    'error'
                );

            } finally {
                submitBtn.disabled = false;
            }
        }
    );

    async function deleteRecipe(id) {
        const recipe =
            recipesById.get(Number(id));

        if (!recipe) {
            showMessage(
                'Рецепт не знайдено.',
                'error'
            );

            return;
        }

        const confirmed = confirm(
            `Видалити рецепт «${recipe.title}»?`
        );

        if (!confirmed) {
            return;
        }

        try {
            const response =
                await fetch(
                    'api_delete.php',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-Token':
                                csrfToken
                        },

                        body: JSON.stringify({
                            id: Number(id)
                        })
                    }
                );

            const result =
                await readJsonResponse(
                    response
                );

            if (!result.success) {
                throw new Error(
                    result.error
                    || 'Не вдалося видалити рецепт.'
                );
            }

            showMessage(
                result.message
                || 'Рецепт видалено.',
                'success'
            );

            resetForm();

            await loadRecipes(
                searchInput.value
            );

        } catch (error) {
            showMessage(
                'Помилка: '
                + error.message,
                'error'
            );
        }
    }

    function editRecipe(id) {
        const recipe =
            recipesById.get(Number(id));

        if (!recipe) {
            showMessage(
                'Рецепт не знайдено.',
                'error'
            );

            return;
        }

        document
            .getElementById('recipeId')
            .value = recipe.id;

        document
            .getElementById('title')
            .value = recipe.title;

        document
            .getElementById('cookTimeMin')
            .value = recipe.cook_time_min;

        document
            .getElementById('ingredients')
            .value = recipe.ingredients;

        document
            .getElementById('formTitle')
            .textContent =
                'Редагувати рецепт';

        submitBtn.textContent =
            'Зберегти зміни';

        cancelEditBtn.style.display =
            'block';

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function resetForm() {
        recipeForm.reset();

        document
            .getElementById('recipeId')
            .value = '';

        document
            .getElementById('formTitle')
            .textContent =
                'Додати рецепт';

        submitBtn.textContent =
            'Додати до колекції';

        cancelEditBtn.style.display =
            'none';
    }

    cancelEditBtn.addEventListener(
        'click',
        resetForm
    );

    function showMessage(text, type) {
        messageBox.className =
            type === 'success'
                ? 'alert-success'
                : 'alert-error';

        messageBox.style.display =
            'block';

        messageBox.textContent =
            text;

        window.setTimeout(
            () => {
                messageBox.style.display =
                    'none';
            },
            3000
        );
    }

    loadRecipes();
</script>

</body>
</html>