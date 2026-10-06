<?php

declare(strict_types=1);

function formatIngredientsList(array $ingredients): string
{
    $ingredients = array_map(
        static fn($ingredient) => trim((string) $ingredient),
        $ingredients
    );

    $ingredients = array_filter(
        $ingredients,
        static fn($ingredient) => $ingredient !== ''
    );

    return implode(', ', $ingredients);
}

function convertGramsToOz(float $grams): float
{
    return round($grams * 0.035274, 2);
}