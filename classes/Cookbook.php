<?php

declare(strict_types=1);

class Cookbook
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addRecipe(
        string $title,
        string $ingredients,
        int $cookTimeMin
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO recipes (title, ingredients, cook_time_min)
             VALUES (:title, :ingredients, :time)'
        );

        $stmt->execute([
            ':title' => $title,
            ':ingredients' => $ingredients,
            ':time' => $cookTimeMin
        ]);

        $this->clearShortestCache();

        return (int) $this->pdo->lastInsertId();
    }

    public function updateRecipe(
        int $id,
        string $title,
        string $ingredients,
        int $cookTimeMin
    ): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE recipes
             SET title = :title,
                 ingredients = :ingredients,
                 cook_time_min = :time
             WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':ingredients' => $ingredients,
            ':time' => $cookTimeMin
        ]);

        $this->clearShortestCache();

        return $stmt->rowCount() > 0;
    }

    public function deleteRecipe(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM recipes WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $this->clearShortestCache();

        return $stmt->rowCount() > 0;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, title, ingredients, cook_time_min
             FROM recipes
             ORDER BY id DESC'
        );

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, ingredients, cook_time_min
             FROM recipes
             WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $recipe = $stmt->fetch();

        return $recipe ?: null;
    }

    public function findByIngredient(string $text): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, ingredients, cook_time_min
             FROM recipes
             WHERE ingredients LIKE :pattern
             ORDER BY id DESC'
        );

        $stmt->execute([
            ':pattern' => '%' . $text . '%'
        ]);

        return $stmt->fetchAll();
    }

    public function search(string $text): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, ingredients, cook_time_min
             FROM recipes
             WHERE title LIKE :title_pattern
                OR ingredients LIKE :ingredients_pattern
             ORDER BY id DESC'
        );

        $pattern = '%' . $text . '%';

        $stmt->execute([
            ':title_pattern' => $pattern,
            ':ingredients_pattern' => $pattern
        ]);

        return $stmt->fetchAll();
    }

    public function findByMaxCookTime(int $maxCookTime): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, ingredients, cook_time_min
             FROM recipes
             WHERE cook_time_min <= :max_time
             ORDER BY cook_time_min ASC, id DESC'
        );

        $stmt->bindValue(
            ':max_time',
            $maxCookTime,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function shortestCookTime(): ?array
    {
        $stmt = $this->pdo->query(
            'SELECT id, title, ingredients, cook_time_min
             FROM recipes
             ORDER BY cook_time_min ASC
             LIMIT 1'
        );

        $recipe = $stmt->fetch();

        return $recipe ?: null;
    }

    public function getAllWithIngredientCountNPlusOne(): array
    {
        $recipes = $this->getAll();

        $queryCount = 1;

        foreach ($recipes as &$recipe) {
            $firstIngredient = trim(
                explode(',', $recipe['ingredients'])[0] ?? ''
            );

            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*)
                 FROM recipes
                 WHERE id <> :id
                   AND ingredients LIKE :pattern'
            );

            $stmt->execute([
                ':id' => $recipe['id'],
                ':pattern' => '%' . $firstIngredient . '%'
            ]);

            $queryCount++;

            $recipe['same_ingredient_count'] =
                (int) $stmt->fetchColumn();
        }

        unset($recipe);

        error_log(
            'N+1 SQL queries: ' . $queryCount
        );

        return $recipes;
    }

    public function getAllWithIngredientCountOptimized(): array
    {
        $stmt = $this->pdo->query(
            'SELECT
                r.id,
                r.title,
                r.ingredients,
                r.cook_time_min,
                COUNT(r2.id) AS same_ingredient_count
             FROM recipes AS r
             LEFT JOIN recipes AS r2
                ON r2.id <> r.id
                AND r2.ingredients LIKE CONCAT(
                    "%",
                    TRIM(SUBSTRING_INDEX(r.ingredients, ",", 1)),
                    "%"
                )
             GROUP BY
                r.id,
                r.title,
                r.ingredients,
                r.cook_time_min
             ORDER BY r.id DESC'
        );

        $recipes = $stmt->fetchAll();

        error_log('Optimized SQL queries: 1');

        return $recipes;
    }

    private function getShortestCacheFile(): string
    {
        return sys_get_temp_dir()
            . '/recipes_shortest_cache.json';
    }

    private function clearShortestCache(): void
    {
        $cacheFile = $this->getShortestCacheFile();

        if (is_file($cacheFile)) {
            if (!unlink($cacheFile)) {
                error_log(
                    'Не вдалося видалити кеш shortestCookTime.'
                );
            }
        }
    }


    public function shortestCookTimeCached(
        int $ttlSeconds = 60
    ): ?array {
        $cacheFile = $this->getShortestCacheFile();

        if (
            is_file($cacheFile)
            && (time() - filemtime($cacheFile)) < $ttlSeconds
        ) {
            $cacheContent = file_get_contents($cacheFile);

            if ($cacheContent !== false) {
                $cached = json_decode(
                    $cacheContent,
                    true
                );

                if (is_array($cached)) {
                    error_log(
                        'CACHE HIT: shortestCookTime'
                    );

                    return $cached;
                }
            }
        }

        error_log(
            'CACHE MISS: shortestCookTime'
        );

        $recipe = $this->shortestCookTime();

        if ($recipe !== null) {
            $json = json_encode(
                $recipe,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

            if ($json !== false) {
                if (
                    file_put_contents(
                        $cacheFile,
                        $json,
                        LOCK_EX
                    ) === false
                ) {
                    error_log(
                        'Не вдалося записати кеш shortestCookTime.'
                    );
                }
            }
        }

        return $recipe;
    }
}