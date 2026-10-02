<?php

class Cookbook {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function addRecipe(string $title, string $ingredients, int $cookTimeMin): void {
        $stmt = $this->pdo->prepare('INSERT INTO recipes (title, ingredients, cook_time_min) VALUES (:title, :ingredients, :time)');
        $stmt->execute([':title' => $title, ':ingredients' => $ingredients, ':time' => $cookTimeMin]);
    }

    public function updateRecipe(int $id, string $title, string $ingredients, int $cookTimeMin): void {
        $stmt = $this->pdo->prepare('UPDATE recipes SET title = :title, ingredients = :ingredients, cook_time_min = :time WHERE id = :id');
        $stmt->execute([':id' => $id, ':title' => $title, ':ingredients' => $ingredients, ':time' => $cookTimeMin]);
    }

    public function deleteRecipe(int $id): void {
        $stmt = $this->pdo->prepare('DELETE FROM recipes WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function getAll(): array {
        return $this->pdo->query('SELECT * FROM recipes ORDER BY id DESC')->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findByIngredient(string $text): array {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes WHERE ingredients LIKE :pattern');
        $stmt->execute([':pattern' => '%' . $text . '%']);
        return $stmt->fetchAll();
    }

    public function shortestCookTime(): ?array {
        $stmt = $this->pdo->query('SELECT * FROM recipes ORDER BY cook_time_min ASC LIMIT 1');
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
?>