<?php

declare(strict_types=1);

class Recipe
{
    protected string $title;
    protected array $ingredients;
    protected int $cookTimeMin;

    public function __construct(
        string $title,
        array $ingredients,
        int $cookTimeMin
    ) {
        $this->title = $title;
        $this->ingredients = $ingredients;
        $this->cookTimeMin = $cookTimeMin;
    }

    public function getInfo(): string
    {
        $safeTitle = htmlspecialchars(
            $this->title,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $ingList = htmlspecialchars(
            formatIngredientsList($this->ingredients),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        return
            "<b>{$safeTitle}</b><br>" .
            "Час приготування: {$this->cookTimeMin} хв.<br>" .
            "<small>Інгредієнти: {$ingList}</small>";
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getCookTimeMin(): int
    {
        return $this->cookTimeMin;
    }
}