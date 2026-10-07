CREATE DATABASE IF NOT EXISTS practicum4
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE practicum4;

CREATE TABLE IF NOT EXISTS recipes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    cook_time_min INT UNSIGNED NOT NULL,
    INDEX idx_recipes_cook_time (cook_time_min)
);