<?php

declare(strict_types=1);

class VeganRecipe extends Recipe
{
    private string $substitutions;

    public function __construct(
        string $title,
        array $ingredients,
        int $cookTimeMin,
        string $substitutions
    ) {
        parent::__construct($title, $ingredients, $cookTimeMin);

        $this->substitutions = $substitutions;
    }

    public function getInfo(): string
    {
        $baseInfo = parent::getInfo();

        $safeSubstitutions = htmlspecialchars(
            $this->substitutions,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        return $baseInfo .
            "<br><span style=\"color: #27ae60; font-weight: bold;\">" .
            "[Веганський] Заміни: {$safeSubstitutions}" .
            "</span>";
    }
}