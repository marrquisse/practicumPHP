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

        $stmt->bindValue(':max_time', $maxCookTime, PDO::PARAM_INT);
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
}