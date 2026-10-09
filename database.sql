CREATE DATABASE IF NOT EXISTS practicum4
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE practicum4;

DROP TABLE IF EXISTS recipes;

CREATE TABLE recipes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    cook_time_min INT UNSIGNED NOT NULL,

    PRIMARY KEY (id),

    INDEX idx_recipes_cook_time (
        cook_time_min
    )
);

INSERT INTO recipes (
    title,
    ingredients,
    cook_time_min
) VALUES
(
    'Сирники',
    'сир, яйця, борошно, цукор',
    25
),
(
    'Паста',
    'макарони, томати, сир',
    20
),
(
    'Борщ',
    'буряк, картопля, капуста, морква',
    90
),
(
    'Яблучний пиріг',
    'яблука, борошно, яйця, цукор',
    60
),
(
    'Омлет',
    'яйця, молоко, сіль',
    10
);