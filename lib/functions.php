<?php
// lib/functions.php

// Функція для форматування масиву інгредієнтів у рядок
function formatIngredientsList(array $ingredients): string {
    return implode(', ', array_map('trim', $ingredients));
}

// Функція для конвертації грамів в унції
function convertGramsToOz(float $grams): float {
    return round($grams * 0.035274, 2);
}