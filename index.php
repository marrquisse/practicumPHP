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

    <title>Кулінарна книга та конвертер</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        :root {
            --primary: #6b46c1;
            --primary-light: #eee8ff;
            --accent: #ecc94b;
            --accent-hover: #d69e2e;
            --bg: #f7fafc;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --border: #e2e8f0;
            --danger: #e53e3e;
            --success: #2f855a;
        }

        * {
            box-sizing: border-box;
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
            padding: 24px 20px;
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

        .header p {
            margin: 8px 0 0;
            color: var(--text-muted);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        .recipes-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 40px;
        }

        @media (max-width: 850px) {
            .recipes-layout {
                grid-template-columns: 1fr;
            }
        }

        .form-panel,
        .converter-card,
        .chart-card,
        .history-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .form-panel {
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        h2,
        h3 {
            color: var(--text-main);
        }

        .section-heading {
            margin-top: 0;
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
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            background: #fff;
            color: var(--text-main);
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.08);
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
            font-family: inherit;
        }

        button:hover {
            background-color: var(--accent-hover);
        }

        button:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .secondary-button {
            background: var(--primary-light);
            color: var(--primary);
        }

        .secondary-button:hover {
            background: #e4dcff;
        }

        .danger-button {
            background: #fff5f5;
            color: var(--danger);
            border: 1px solid #fed7d7;
        }

        .danger-button:hover {
            background: #fed7d7;
        }

        .btn-cancel {
            display: none;
            margin-top: 15px;
            background: transparent;
            color: var(--text-muted);
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
                repeat(auto-fill, minmax(280px, 1fr));
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

        /* =========================
           ПРАКТИЧНА РОБОТА №12
           ========================= */

        .practicum12 {
            margin-top: 70px;
            padding-top: 50px;
            border-top: 2px solid var(--border);
        }

        .practicum12-header {
            margin-bottom: 28px;
        }

        .practicum12-label {
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .practicum12-header h2 {
            font-size: 2rem;
            margin: 8px 0;
        }

        .practicum12-header p {
            color: var(--text-muted);
            max-width: 720px;
            margin: 0;
        }

        .converter-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 28px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .converter-grid {
                grid-template-columns: 1fr;
            }
        }

        .converter-fields-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .conversion-result {
            margin-top: 22px;
            padding: 20px;
            border-radius: 12px;
            background: var(--primary-light);
            border: 1px solid #ded3ff;
        }

        .conversion-result-label {
            display: block;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .conversion-result-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
            overflow-wrap: anywhere;
        }

        .chart-card {
            min-width: 0;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .chart-header h3 {
            margin: 0;
        }

        .chart-header p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .chart-status {
            white-space: nowrap;
            padding: 5px 9px;
            border-radius: 999px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .canvas-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            background: #fbfaff;
            border: 1px solid var(--border);
            padding: 8px;
        }

        #conversionChart {
            display: block;
            width: 100%;
            max-width: 760px;
            min-width: 560px;
            height: auto;
        }

        .chart-info {
            margin-top: 12px;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .history-card {
            margin-top: 28px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            margin-bottom: 18px;
        }

        .history-header h3 {
            margin: 0;
        }

        .history-header button {
            width: auto;
            padding: 9px 14px;
            font-size: 0.85rem;
        }

        .conversion-history {
            display: grid;
            gap: 10px;
        }

        .history-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-main {
            font-weight: 600;
        }

        .history-meta {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 3px;
        }

        .history-result {
            text-align: right;
            font-weight: 700;
            color: var(--primary);
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Книга Рецептів</h1>
</header>

<div class="container">

    <div class="recipes-layout">

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

                <label for="search">
                    Пошук рецептів
                </label>

                <input
                    type="text"
                    id="search"
                    placeholder="Пошук за інгредієнтом або назвою..."
                >

            </div>

            <h2 class="section-heading">
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


    <!-- ==================================
         ПРАКТИЧНА РОБОТА №12 — CANVAS
         ================================== -->

    <section class="practicum12">

        <div class="practicum12-header">

            <h2>
                Конвертер валют і одиниць виміру
            </h2>

            <p>
                Результати реальних конвертацій зберігаються
                в localStorage і використовуються для побудови
                анімованого Canvas-графіка.
            </p>

        </div>

        <div class="converter-grid">

            <!-- Конвертер -->
            <div class="converter-card">

                <h3 style="margin-top: 0;">
                    Нова конвертація
                </h3>

                <div class="form-group">

                    <label for="conversionType">
                        Тип конвертації
                    </label>

                    <select id="conversionType">
                        <option value="currency">
                            Валюта
                        </option>

                        <option value="length">
                            Довжина
                        </option>

                        <option value="mass">
                            Маса
                        </option>

                        <option value="temperature">
                            Температура
                        </option>
                    </select>

                </div>

                <div class="form-group">

                    <label for="conversionAmount">
                        Значення
                    </label>

                    <input
                        type="number"
                        id="conversionAmount"
                        value="1"
                        step="any"
                    >

                </div>

                <div class="converter-fields-row">

                    <div class="form-group">
                        <label for="conversionFrom">
                            З
                        </label>

                        <select id="conversionFrom"></select>
                    </div>

                    <div class="form-group">
                        <label for="conversionTo">
                            В
                        </label>

                        <select id="conversionTo"></select>
                    </div>

                </div>

                <button
                    type="button"
                    id="convertButton"
                >
                    Конвертувати
                </button>

                <div
                    class="conversion-result"
                    id="conversionResult"
                >
                    <span class="conversion-result-label">
                        Результат
                    </span>

                    <div
                        class="conversion-result-value"
                        id="conversionResultValue"
                    >
                        —
                    </div>
                </div>

            </div>


            <!-- Canvas -->
            <div class="chart-card">

                <div class="chart-header">

                    <div>
                        <h3>
                            Історія конвертацій
                        </h3>

                        <p>
                            Останні результати для поточного
                            типу та цільової одиниці
                        </p>
                    </div>

                    <span
                        class="chart-status"
                        id="chartStatus"
                    >
                        0 точок
                    </span>

                </div>

                <div class="canvas-wrapper">

                    <!--
                        Фіксовані width/height —
                        вимога Canvas-варіанта.
                    -->
                    <canvas
                        id="conversionChart"
                        width="760"
                        height="280"
                    >
                        Ваш браузер не підтримує Canvas.
                    </canvas>

                </div>

                <div class="chart-info">
                    Графік використовує останні 8 реальних
                    результатів із localStorage.
                </div>

            </div>

        </div>


        <!-- Історія -->
        <div class="history-card">

            <div class="history-header">

                <h3>
                    Останні конвертації
                </h3>

                <button
                    type="button"
                    class="danger-button"
                    id="clearHistoryButton"
                >
                    Очистити історію
                </button>

            </div>

            <div
                id="conversionHistory"
                class="conversion-history"
            >
            </div>

        </div>

    </section>

</div>


<script>
    /* ==================================================
       ПРАКТИЧНІ №1–8 — РЕЦЕПТИ
       ================================================== */

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
                data.error ||
                `Помилка сервера: ${response.status}`
            );
        }

        return data;
    }


    function createRecipeCard(recipe) {

        const card =
            document.createElement('article');

        card.className =
            'recipe-card';


        const title =
            document.createElement('span');

        title.className =
            'recipe-title';

        // XSS-захист.
        title.textContent =
            recipe.title;


        const time =
            document.createElement('div');

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

        actions.className =
            'card-actions';


        const editButton =
            document.createElement('button');

        editButton.type =
            'button';

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

        deleteButton.type =
            'button';

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

        const loading =
            document.createElement('p');

        loading.className =
            'empty-state';

        loading.textContent =
            'Завантаження даних...';

        resultsContainer.append(
            loading
        );


        try {

            const response =
                await fetch(
                    'api_list.php?q='
                    + encodeURIComponent(query),
                    {
                        method: 'GET',
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            const data =
                await readJsonResponse(
                    response
                );

            if (!Array.isArray(data)) {
                throw new Error(
                    'Некоректний формат відповіді сервера.'
                );
            }

            resultsContainer.replaceChildren();

            recipesById =
                new Map(
                    data.map(recipe => [
                        Number(recipe.id),
                        recipe
                    ])
                );


            if (data.length === 0) {

                const empty =
                    document.createElement('p');

                empty.className =
                    'empty-state';

                empty.textContent =
                    'Рецептів не знайдено.';

                resultsContainer.append(
                    empty
                );

                return;
            }


            const fragment =
                document.createDocumentFragment();

            for (const recipe of data) {
                fragment.append(
                    createRecipeCard(recipe)
                );
            }

            resultsContainer.append(
                fragment
            );

        } catch (error) {

            resultsContainer.replaceChildren();

            const errorBox =
                document.createElement('div');

            errorBox.className =
                'load-error';

            errorBox.textContent =
                'Помилка завантаження: '
                + error.message;

            resultsContainer.append(
                errorBox
            );
        }
    }


    searchInput.addEventListener(
        'input',
        event => {
            loadRecipes(
                event.target.value
            );
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
                id:
                    id === ''
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
                            method:
                                'POST',

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
                        result.error ||
                        'Не вдалося зберегти рецепт.'
                    );
                }


                showMessage(
                    result.message ||
                    (
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

                submitBtn.disabled =
                    false;
            }
        }
    );


    async function deleteRecipe(id) {

        const recipe =
            recipesById.get(
                Number(id)
            );

        if (!recipe) {
            showMessage(
                'Рецепт не знайдено.',
                'error'
            );

            return;
        }


        const confirmed =
            confirm(
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
                        method:
                            'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-Token':
                                csrfToken
                        },

                        body:
                            JSON.stringify({
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
                    result.error ||
                    'Не вдалося видалити рецепт.'
                );
            }


            showMessage(
                result.message ||
                'Рецепт видалено.',
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
            recipesById.get(
                Number(id)
            );

        if (!recipe) {
            return;
        }

        document
            .getElementById('recipeId')
            .value =
                recipe.id;

        document
            .getElementById('title')
            .value =
                recipe.title;

        document
            .getElementById('cookTimeMin')
            .value =
                recipe.cook_time_min;

        document
            .getElementById('ingredients')
            .value =
                recipe.ingredients;

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


    function showMessage(
        text,
        type
    ) {

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


    /* ==================================================
       ПРАКТИЧНА №12
       КОНВЕРТЕР + CANVAS
       ================================================== */

    const conversionType =
        document.getElementById(
            'conversionType'
        );

    const conversionAmount =
        document.getElementById(
            'conversionAmount'
        );

    const conversionFrom =
        document.getElementById(
            'conversionFrom'
        );

    const conversionTo =
        document.getElementById(
            'conversionTo'
        );

    const convertButton =
        document.getElementById(
            'convertButton'
        );

    const conversionResultValue =
        document.getElementById(
            'conversionResultValue'
        );

    const conversionHistoryElement =
        document.getElementById(
            'conversionHistory'
        );

    const clearHistoryButton =
        document.getElementById(
            'clearHistoryButton'
        );

    const chartStatus =
        document.getElementById(
            'chartStatus'
        );


    /*
     * Canvas із фіксованими розмірами.
     */
    const canvas =
        document.getElementById(
            'conversionChart'
        );

    const ctx =
        canvas.getContext('2d');


    /*
     * ID активного кадру.
     * Потрібний для cancelAnimationFrame().
     */
    let chartAnimationId = null;


    const STORAGE_KEY =
        'practicum12_conversion_history';


    /*
     * Доступні одиниці.
     *
     * Значення в rate — коефіцієнт
     * відносно базової одиниці категорії.
     */
    const conversionData = {

        currency: {
            UAH: {
                label: 'UAH — гривня',
                rate: 1
            },

            USD: {
                label: 'USD — долар',
                rate: 45.24
            },

            EUR: {
                label: 'EUR — євро',
                rate: 50.70
            },

            GBP: {
                label: 'GBP — фунт',
                rate: 59.60
            },

            PLN: {
                label: 'PLN — злотий',
                rate: 11.46
            }
        },


        length: {
            mm: {
                label: 'Міліметри',
                rate: 0.001
            },

            cm: {
                label: 'Сантиметри',
                rate: 0.01
            },

            m: {
                label: 'Метри',
                rate: 1
            },

            km: {
                label: 'Кілометри',
                rate: 1000
            }
        },


        mass: {
            g: {
                label: 'Грами',
                rate: 0.001
            },

            kg: {
                label: 'Кілограми',
                rate: 1
            },

            t: {
                label: 'Тонни',
                rate: 1000
            },

            lb: {
                label: 'Фунти',
                rate: 0.45359237
            }
        },


        temperature: {
            C: {
                label: '°C — Цельсій'
            },

            F: {
                label: '°F — Фаренгейт'
            },

            K: {
                label: 'K — Кельвін'
            }
        }
    };


    /*
     * Читаємо історію з localStorage.
     *
     * У ній знаходяться реальні результати
     * конвертацій користувача.
     */
    function loadConversionHistory() {

        try {

            const raw =
                localStorage.getItem(
                    STORAGE_KEY
                );

            if (!raw) {
                return [];
            }

            const history =
                JSON.parse(raw);

            return Array.isArray(history)
                ? history
                : [];

        } catch {

            return [];
        }
    }


    let conversionHistory =
        loadConversionHistory();


    /*
     * Зберігаємо максимум 50 записів,
     * щоб localStorage не ріс безмежно.
     */
    function saveConversionHistory() {

        conversionHistory =
            conversionHistory.slice(-50);

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(
                conversionHistory
            )
        );
    }


    /*
     * Оновлення select відповідно
     * до вибраної категорії.
     */
    function updateUnitOptions() {

        const type =
            conversionType.value;

        const units =
            conversionData[type];

        conversionFrom.replaceChildren();
        conversionTo.replaceChildren();


        Object.entries(units)
            .forEach(
                ([value, item]) => {

                    const fromOption =
                        document.createElement(
                            'option'
                        );

                    fromOption.value =
                        value;

                    fromOption.textContent =
                        item.label;


                    const toOption =
                        document.createElement(
                            'option'
                        );

                    toOption.value =
                        value;

                    toOption.textContent =
                        item.label;


                    conversionFrom.append(
                        fromOption
                    );

                    conversionTo.append(
                        toOption
                    );
                }
            );


        if (
            conversionTo.options.length > 1
        ) {
            conversionTo.selectedIndex = 1;
        }


        renderConversionHistory();

        /*
         * При зміні типу відображаємо
         * актуальні дані без анімації.
         */
        drawChart(
            getChartHistory(),
            1
        );
    }


    /*
     * Температура потребує окремих формул,
     * бо тут не працює простий коефіцієнт.
     */
    function convertTemperature(
        value,
        from,
        to
    ) {

        let celsius;


        if (from === 'C') {
            celsius = value;

        } else if (from === 'F') {
            celsius =
                (value - 32)
                * 5 / 9;

        } else {
            celsius =
                value - 273.15;
        }


        if (to === 'C') {
            return celsius;
        }

        if (to === 'F') {
            return (
                celsius * 9 / 5
                + 32
            );
        }

        return (
            celsius + 273.15
        );
    }


    /*
     * Реальна функція конвертації.
     */
    function calculateConversion(
        type,
        amount,
        from,
        to
    ) {

        if (type === 'temperature') {
            return convertTemperature(
                amount,
                from,
                to
            );
        }


        const units =
            conversionData[type];

        const fromRate =
            units[from].rate;

        const toRate =
            units[to].rate;


        /*
         * Спочатку переводимо
         * в базову одиницю,
         * потім — у цільову.
         */
        return (
            amount * fromRate / toRate
        );
    }


    function formatNumber(value) {

        return new Intl.NumberFormat(
            'uk-UA',
            {
                maximumFractionDigits: 4
            }
        ).format(value);
    }


    /*
     * Після реальної конвертації
     * створюємо новий запис історії.
     */
    function performConversion() {

        const amount =
            Number(
                conversionAmount.value
            );

        if (!Number.isFinite(amount)) {

            conversionResultValue.textContent =
                'Некоректне значення';

            return;
        }


        const type =
            conversionType.value;

        const from =
            conversionFrom.value;

        const to =
            conversionTo.value;


        const result =
            calculateConversion(
                type,
                amount,
                from,
                to
            );


        conversionResultValue.textContent =
            `${formatNumber(result)} ${to}`;


        const record = {

            id:
                Date.now(),

            type,

            amount,

            from,

            to,

            result,

            createdAt:
                new Date().toISOString()
        };


        conversionHistory.push(
            record
        );

        saveConversionHistory();

        renderConversionHistory();


        /*
         * Крок 6 методички:
         * Canvas реагує саме на нову
         * конвертацію користувача.
         */
        animateChart();
    }


    /*
     * Для графіка беремо не випадкові
     * числа, а реальні записи history.
     *
     * Щоб значення були логічно
     * порівнюваними, залишаємо
     * поточний тип і цільову одиницю.
     */
    function getChartHistory() {

        const type =
            conversionType.value;

        const to =
            conversionTo.value;


        return conversionHistory
            .filter(
                item =>
                    item.type === type
                    && item.to === to
            )
            .slice(-8);
    }


    /*
     * Вивід текстової історії.
     */
    function renderConversionHistory() {

        conversionHistoryElement
            .replaceChildren();


        const recent =
            conversionHistory
                .slice(-10)
                .reverse();


        if (recent.length === 0) {

            const empty =
                document.createElement(
                    'p'
                );

            empty.className =
                'empty-state';

            empty.textContent =
                'Історія поки порожня. Виконайте першу конвертацію.';

            conversionHistoryElement
                .append(empty);

            return;
        }


        for (const item of recent) {

            const row =
                document.createElement(
                    'div'
                );

            row.className =
                'history-item';


            const left =
                document.createElement(
                    'div'
                );


            const main =
                document.createElement(
                    'div'
                );

            main.className =
                'history-main';

            main.textContent =
                `${formatNumber(item.amount)} ${item.from} → ${item.to}`;


            const meta =
                document.createElement(
                    'div'
                );

            meta.className =
                'history-meta';

            meta.textContent =
                new Date(
                    item.createdAt
                ).toLocaleString(
                    'uk-UA'
                );


            left.append(
                main,
                meta
            );


            const result =
                document.createElement(
                    'div'
                );

            result.className =
                'history-result';

            result.textContent =
                `${formatNumber(item.result)} ${item.to}`;


            row.append(
                left,
                result
            );


            conversionHistoryElement
                .append(row);
        }
    }


    /*
     * Canvas-графік.
     *
     * progress:
     * 0 — лінія ще не намальована,
     * 1 — повністю намальована.
     */
    function drawChart(
        history,
        progress = 1
    ) {

        /*
         * clearRect перед кожним кадром —
         * вимога для Canvas-анімації.
         */
        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );


        const width =
            canvas.width;

        const height =
            canvas.height;


        const padding = {
            left: 58,
            right: 25,
            top: 28,
            bottom: 52
        };


        const plotWidth =
            width
            - padding.left
            - padding.right;

        const plotHeight =
            height
            - padding.top
            - padding.bottom;


        /*
         * Фон графіка.
         */
        ctx.fillStyle =
            '#fbfaff';

        ctx.fillRect(
            0,
            0,
            width,
            height
        );


        /*
         * Допоміжна сітка.
         */
        ctx.strokeStyle =
            '#ece7f8';

        ctx.lineWidth =
            1;


        for (let i = 0; i <= 4; i++) {

            const y =
                padding.top
                + (
                    plotHeight
                    * i / 4
                );

            ctx.beginPath();

            ctx.moveTo(
                padding.left,
                y
            );

            ctx.lineTo(
                width
                - padding.right,
                y
            );

            ctx.stroke();
        }


        chartStatus.textContent =
            `${history.length} ${
                history.length === 1
                    ? 'точка'
                    : 'точок'
            }`;


        /*
         * Якщо даних ще немає.
         */
        if (history.length === 0) {

            ctx.fillStyle =
                '#718096';

            ctx.font =
                '15px Inter, sans-serif';

            ctx.textAlign =
                'center';

            ctx.fillText(
                'Виконайте конвертацію, щоб побачити графік',
                width / 2,
                height / 2
            );

            return;
        }


        const values =
            history.map(
                item =>
                    Number(item.result)
            );


        let minValue =
            Math.min(...values);

        let maxValue =
            Math.max(...values);


        /*
         * Якщо всі значення однакові,
         * додаємо невеликий діапазон,
         * щоб графік не був рівно
         * на одному пікселі.
         */
        if (minValue === maxValue) {

            const difference =
                Math.abs(minValue) > 0
                    ? Math.abs(minValue) * 0.1
                    : 1;

            minValue -= difference;
            maxValue += difference;
        }


        const valueRange =
            maxValue - minValue;


        const points =
            history.map(
                (item, index) => {

                    const x =
                        history.length === 1
                            ? padding.left
                              + plotWidth / 2

                            : padding.left
                              + plotWidth
                              * index
                              / (
                                  history.length
                                  - 1
                              );


                    const normalized =
                        (
                            Number(item.result)
                            - minValue
                        )
                        / valueRange;


                    const y =
                        padding.top
                        + plotHeight
                        - normalized
                        * plotHeight;


                    return {
                        x,
                        y,
                        record: item
                    };
                }
            );


        /*
         * Мінімальне та максимальне
         * значення по осі Y.
         */
        ctx.fillStyle =
            '#718096';

        ctx.font =
            '11px Inter, sans-serif';

        ctx.textAlign =
            'right';


        ctx.fillText(
            formatNumber(maxValue),
            padding.left - 8,
            padding.top + 4
        );


        ctx.fillText(
            formatNumber(minValue),
            padding.left - 8,
            padding.top
            + plotHeight
        );


        /*
         * Якщо лише одна точка —
         * просто малюємо її.
         */
        if (points.length === 1) {

            drawChartPoint(
                points[0],
                1
            );

            drawChartLabel(
                points[0]
            );

            return;
        }


        /*
         * Плавно відкриваємо сегменти
         * лінії залежно від progress.
         */
        const segmentCount =
            points.length - 1;

        const revealedSegments =
            progress * segmentCount;

        const fullSegments =
            Math.floor(
                revealedSegments
            );

        const partialProgress =
            revealedSegments
            - fullSegments;


        ctx.strokeStyle =
            '#6b46c1';

        ctx.lineWidth =
            3;

        ctx.lineCap =
            'round';

        ctx.lineJoin =
            'round';

        ctx.beginPath();

        ctx.moveTo(
            points[0].x,
            points[0].y
        );


        for (
            let i = 1;
            i <= fullSegments
            && i < points.length;
            i++
        ) {
            ctx.lineTo(
                points[i].x,
                points[i].y
            );
        }


        if (
            fullSegments
            < segmentCount
        ) {

            const start =
                points[fullSegments];

            const end =
                points[
                    fullSegments + 1
                ];


            const partialX =
                start.x
                + (
                    end.x
                    - start.x
                )
                * partialProgress;


            const partialY =
                start.y
                + (
                    end.y
                    - start.y
                )
                * partialProgress;


            ctx.lineTo(
                partialX,
                partialY
            );
        }


        ctx.stroke();


        /*
         * Малюємо вже "відкриті" точки.
         */
        const visiblePoints =
            Math.min(
                points.length,
                fullSegments + 1
            );


        for (
            let i = 0;
            i < visiblePoints;
            i++
        ) {

            drawChartPoint(
                points[i],
                1
            );


            if (progress >= 1) {
                drawChartLabel(
                    points[i]
                );
            }
        }


        /*
         * Після завершення анімації
         * гарантуємо малювання
         * останньої точки.
         */
        if (progress >= 1) {

            drawChartPoint(
                points[
                    points.length - 1
                ],
                1
            );

            drawChartLabel(
                points[
                    points.length - 1
                ]
            );
        }
    }


    function drawChartPoint(point) {

        ctx.beginPath();

        ctx.arc(
            point.x,
            point.y,
            5,
            0,
            Math.PI * 2
        );

        ctx.fillStyle =
            '#ffffff';

        ctx.fill();

        ctx.lineWidth =
            3;

        ctx.strokeStyle =
            '#6b46c1';

        ctx.stroke();
    }


    function drawChartLabel(point) {

        ctx.fillStyle =
            '#2d3748';

        ctx.font =
            '11px Inter, sans-serif';

        ctx.textAlign =
            'center';

        ctx.fillText(
            formatNumber(
                point.record.result
            ),
            point.x,
            point.y - 12
        );


        ctx.fillStyle =
            '#718096';

        ctx.fillText(
            point.record.to,
            point.x,
            canvas.height - 18
        );
    }


    /*
     * Анімація Canvas через
     * requestAnimationFrame.
     */
    function animateChart() {

        /*
         * Якщо попередня анімація
         * ще не закінчилася —
         * зупиняємо її.
         */
        if (
            chartAnimationId !== null
        ) {
            cancelAnimationFrame(
                chartAnimationId
            );

            chartAnimationId =
                null;
        }


        const history =
            getChartHistory();


        let startTime =
            null;


        const duration =
            650;


        function frame(timestamp) {

            if (startTime === null) {
                startTime =
                    timestamp;
            }


            const elapsed =
                timestamp
                - startTime;


            const rawProgress =
                Math.min(
                    elapsed / duration,
                    1
                );


            /*
             * easeOutCubic —
             * плавніше завершення.
             */
            const progress =
                1
                - Math.pow(
                    1 - rawProgress,
                    3
                );


            drawChart(
                history,
                progress
            );


            if (rawProgress < 1) {

                chartAnimationId =
                    requestAnimationFrame(
                        frame
                    );

            } else {

                chartAnimationId =
                    null;
            }
        }


        chartAnimationId =
            requestAnimationFrame(
                frame
            );
    }


    /*
     * Реальна дія користувача —
     * натискання "Конвертувати".
     */
    convertButton.addEventListener(
        'click',
        performConversion
    );


    conversionAmount.addEventListener(
        'keydown',
        event => {

            if (event.key === 'Enter') {
                performConversion();
            }
        }
    );


    conversionType.addEventListener(
        'change',
        updateUnitOptions
    );


    conversionTo.addEventListener(
        'change',
        () => {

            drawChart(
                getChartHistory(),
                1
            );
        }
    );


    /*
     * Очищення історії —
     * додаткова взаємодія
     * з даними застосунку.
     */
    clearHistoryButton.addEventListener(
        'click',
        () => {

            const confirmed =
                confirm(
                    'Очистити всю історію конвертацій?'
                );

            if (!confirmed) {
                return;
            }


            conversionHistory = [];

            localStorage.removeItem(
                STORAGE_KEY
            );

            renderConversionHistory();

            drawChart(
                [],
                1
            );

            conversionResultValue
                .textContent = '—';
        }
    );


    /*
     * Крок 8 методички:
     * коли вкладка стає неактивною,
     * анімація коректно зупиняється.
     */
    document.addEventListener(
        'visibilitychange',
        () => {

            if (
                document.hidden
                && chartAnimationId !== null
            ) {

                cancelAnimationFrame(
                    chartAnimationId
                );

                chartAnimationId =
                    null;

            } else if (
                !document.hidden
            ) {

                drawChart(
                    getChartHistory(),
                    1
                );
            }
        }
    );


    window.addEventListener(
        'beforeunload',
        () => {

            if (
                chartAnimationId !== null
            ) {
                cancelAnimationFrame(
                    chartAnimationId
                );
            }
        }
    );


    /* ==================================================
       ПОЧАТКОВИЙ ЗАПУСК
       ================================================== */

    loadRecipes();

    updateUnitOptions();

    renderConversionHistory();

    drawChart(
        getChartHistory(),
        1
    );
</script>

</body>
</html>