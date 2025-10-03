CREATE DATABASE IF NOT EXISTS solomono_catalog;
USE solomono_catalog;

-- Создание таблицы категорий
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы товаров
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(14,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Вставка категорий
INSERT INTO categories (name) VALUES
    ('Ноутбуки'),
    ('Смартфоны'),
    ('Планшеты'),
    ('Аксессуары');

-- Вставка товаров
INSERT INTO products (category_id, name, price, created_at) VALUES
    (1, 'Ноутбук Asus Zenbook Pro Duo', 15000.00, '2025-01-01 10:00:00'),
    (1, 'Ноутбук Lenovo ThinkPad', 20000.00, '2025-02-15 12:00:00'),
    (1, 'Ноутбук Dell XPS 15', 18000.00, '2025-03-20 14:00:00'),
    (1, 'Ноутбук Dell Inspiron', 22000.00, '2025-04-10 16:00:00'),
    (1, 'Ноутбук Apple MacBook Pro', 50000.00, '2025-05-05 18:00:00'),
    (2, 'Смартфон Samsung Galaxy S23', 12000.00, '2025-06-01 10:00:00'),
    (2, 'Смартфон iPhone 17 Pro', 30000.00, '2025-07-15 12:00:00'),
    (2, 'Смартфон Google Pixel 7 Pro', 15000.00, '2025-08-20 14:00:00'),
    (2, 'Смартфон POCO X4 Pro', 10000.00, '2025-09-10 16:00:00'),
    (3, 'Планшет iPad Air', 20000.00, '2025-10-01 10:00:00'),
    (3, 'Планшет Samsung Tab', 15000.00, '2025-11-15 12:00:00'),
    (3, 'Планшет Lenovo Tab', 12000.00, '2025-12-20 14:00:00'),
    (4, 'Мышка Corsair', 500.00, '2025-01-10 16:00:00'),
    (4, 'Клавиатура HP', 2000.00, '2025-02-05 18:00:00'),
    (4, 'Наушники JBL', 3000.00, '2025-03-01 10:00:00');
