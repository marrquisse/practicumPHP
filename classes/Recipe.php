<?php

class Recipe {
    protected string $title;
    protected array $ingredients;
    protected int $cookTimeMin;

    public function __construct(string $title, array $ingredients, int $cookTimeMin) {
        $this->title = $title;
        $this->ingredients = $ingredients;
        $this->cookTimeMin = $cookTimeMin;
    }

    public function getInfo(): string {
        $ingList = formatIngredientsList($this->ingredients);
        return "<b>" . htmlspecialchars($this->title) . "</b> <br>" .
               "Час приготування: {$this->cookTimeMin} хв.<br>" .
               "<small>Інгредієнти: {$ingList}</small>";
    }

    public function getIngredients(): array { return $this->ingredients; }
    public function getCookTimeMin(): int { return $this->cookTimeMin; }
}