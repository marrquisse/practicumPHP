<?php

function formatIngredientsList(array $ingredients): string {
    return implode(', ', array_map('trim', $ingredients));
}

function convertGramsToOz(float $grams): float {
    return round($grams * 0.035274, 2);
}