<?php
// classes/Cookbook.php

class Cookbook {
    private array $recipes = [];

    public function addRecipe(Recipe $recipe): void {
        $this->recipes[] = $recipe;
    }

    public function getAll(): array {
        return $this->recipes;
    }

    // Пошук за інгредієнтом
    public function findByIngredient(string $search): array {
        $found = [];
        foreach ($this->recipes as $recipe) {
            foreach ($recipe->getIngredients() as $ingredient) {
                if (stripos(trim($ingredient), $search) !== false) {
                    $found[] = $recipe;
                    break;
                }
            }
        }
        return $found;
    }

    // Пошук найшвидшого рецепта
    public function shortestCookTime(): ?Recipe {
        if (empty($this->recipes)) return null;
        
        $shortest = $this->recipes[0];
        foreach ($this->recipes as $recipe) {
            if ($recipe->getCookTimeMin() < $shortest->getCookTimeMin()) {
                $shortest = $recipe;
            }
        }
        return $shortest;
    }
}