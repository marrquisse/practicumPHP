<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моя Кулінарна Книга</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #6b46c1; --accent: #ecc94b; --accent-hover: #d69e2e; --bg: #f7fafc; --card-bg: #ffffff; --text-main: #2d3748; --text-muted: #718096; --border: #e2e8f0; --danger: #e53e3e; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); margin: 0; padding: 0; line-height: 1.6; }
        .header { background-color: var(--card-bg); padding: 24px 0; border-bottom: 1px solid var(--border); text-align: center; margin-bottom: 40px; }
        .header h1 { margin: 0; color: var(--primary); font-weight: 700; font-size: 2rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 20px; display: grid; grid-template-columns: 350px 1fr; gap: 40px; }
        @media (max-width: 850px) { .container { grid-template-columns: 1fr; } }
        
        .form-panel { background: var(--card-bg); padding: 30px; border-radius: 16px; border: 1px solid var(--border); position: sticky; top: 20px; height: fit-content; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 500; margin-bottom: 8px; font-size: 0.9rem; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        input:focus, textarea:focus { border-color: var(--primary); outline: none; }
        
        button { width: 100%; background-color: var(--accent); color: #1a202c; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 1rem; }
        button:hover { background-color: var(--accent-hover); }
        .btn-cancel { display: none; text-align: center; margin-top: 15px; color: var(--text-muted); cursor: pointer; font-size: 0.9rem; }
        
        #messageBox { display: none; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .alert-success { background-color: #f0fff4; color: #276749; border: 1px solid #c6f6d5; }
        .alert-error { background-color: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }
        
        .search-box { margin-bottom: 24px; }
        .recipes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
        .recipe-card { background: var(--card-bg); padding: 24px; border-radius: 16px; border: 1px solid var(--border); border-top: 4px solid var(--primary); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .recipe-card b { font-size: 1.15rem; display: block; margin-bottom: 12px; }
        .recipe-card small { color: var(--text-muted); display: block; margin-top: 16px; }
        
        .card-actions { margin-top: 15px; display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid var(--border); }
        .card-actions span { cursor: pointer; font-size: 0.85rem; font-weight: 600; }
        .btn-edit { color: var(--primary); }
        .btn-delete { color: var(--danger); }
    </style>
</head>
<body>
    <header class="header">
        <h1>Книга Рецептів</h1>
    </header>

    <div class="container">
        <aside class="form-panel">
            <h2 id="formTitle">Додати рецепт</h2>
            <div id="messageBox"></div>

            <form id="recipeForm">
                <input type="hidden" id="recipeId">
                <div class="form-group">
                    <label>Назва страви</label>
                    <input type="text" id="title" required>
                </div>
                <div class="form-group">
                    <label>Час приготування (хв)</label>
                    <input type="number" id="cookTimeMin" min="1" required>
                </div>
                <div class="form-group">
                    <label>Інгредієнти (через кому)</label>
                    <textarea id="ingredients" rows="3" required></textarea>
                </div>
                <button type="submit" id="submitBtn">Додати до колекції</button>
                <div class="btn-cancel" id="cancelEditBtn" onclick="resetForm()">Скасувати редагування</div>
            </form>
        </aside>

        <main>
            <div class="search-box">
                <input type="text" id="search" placeholder="Пошук за інгредієнтом або назвою...">
            </div>

            <h2 style="margin-top: 0;">Колекція рецептів</h2>
            <div id="results" class="recipes-grid">
                <p style="color: var(--text-muted);">Завантаження даних...</p>
            </div>
        </main>
    </div>

    <script>
        const resultsContainer = document.getElementById('results');
        const searchInput = document.getElementById('search');
        const recipeForm = document.getElementById('recipeForm');
        const messageBox = document.getElementById('messageBox');

        function escapeHTML(str) {
            const div = document.createElement('div');
            div.innerText = str;
            return div.innerHTML;
        }

        async function loadRecipes(query = '') {
            try {
                const response = await fetch('api_list.php?q=' + encodeURIComponent(query));

                if (!response.ok) throw new Error(`Помилка сервера: ${response.status}`);
                
                const data = await response.json();
                
                if (data.length === 0) {
                    resultsContainer.innerHTML = '<p style="color: #718096; grid-column: 1/-1;">Рецептів не знайдено.</p>';
                    return;
                }

                resultsContainer.innerHTML = data.map(r => `
                    <div class="recipe-card">
                        <b>${escapeHTML(r.title)}</b>
                        <div>⏱ ${r.cook_time_min} хв.</div>
                        <small>Інгредієнти: ${escapeHTML(r.ingredients)}</small>
                        <div class="card-actions">
                            <span class="btn-edit" onclick="editRecipe(${r.id}, '${escapeHTML(r.title)}', ${r.cook_time_min}, '${escapeHTML(r.ingredients)}')">Редагувати</span>
                            <span class="btn-delete" onclick="deleteRecipe(${r.id})">Видалити</span>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                resultsContainer.innerHTML = `<div style="color: #c53030; background: #fff5f5; padding: 15px; border-radius: 8px; border: 1px solid #fed7d7; grid-column: 1/-1;">Помилка завантаження: ${error.message}</div>`;
            }
        }

        searchInput.addEventListener('input', (e) => {
            loadRecipes(e.target.value);
        });

        recipeForm.addEventListener('submit', async (e) => {
            e.preventDefault(); 

            const recipeData = {
                id: document.getElementById('recipeId').value,
                title: document.getElementById('title').value,
                cookTimeMin: document.getElementById('cookTimeMin').value,
                ingredients: document.getElementById('ingredients').value
            };

            try {
                const response = await fetch('api_add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(recipeData)
                });

                if (!response.ok) throw new Error(`Помилка: ${response.status}`);
                const result = await response.json();

                if (result.success) {
                    showMessage(result.message, 'success');
                    resetForm();
                    loadRecipes(searchInput.value); 
                } else {
                    throw new Error(result.error);
                }
            } catch (error) {
                showMessage('Помилка: ' + error.message, 'error');
            }
        });

        async function deleteRecipe(id) {
            if (!confirm('Видалити цей рецепт?')) return;
            try {
                const response = await fetch('api_delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                if (!response.ok) throw new Error('Помилка сервера');
                loadRecipes(searchInput.value);
            } catch (error) {
                alert('Не вдалося видалити рецепт.');
            }
        }

        function editRecipe(id, title, time, ingredients) {
            document.getElementById('recipeId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('cookTimeMin').value = time;
            document.getElementById('ingredients').value = ingredients;
            
            document.getElementById('formTitle').textContent = 'Редагувати рецепт';
            document.getElementById('submitBtn').textContent = 'Зберегти зміни';
            document.getElementById('cancelEditBtn').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            recipeForm.reset();
            document.getElementById('recipeId').value = '';
            document.getElementById('formTitle').textContent = 'Додати рецепт';
            document.getElementById('submitBtn').textContent = 'Додати до колекції';
            document.getElementById('cancelEditBtn').style.display = 'none';
        }

        function showMessage(text, type) {
            messageBox.className = type === 'success' ? 'alert-success' : 'alert-error';
            messageBox.style.display = 'block';
            messageBox.textContent = text;
            setTimeout(() => messageBox.style.display = 'none', 3000);
        }

        loadRecipes();
    </script>
</body>
</html>