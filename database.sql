-- ============================================================
--  ShopEasy  -  E-Commerce Database
--  Database name: ecommerce_db
-- ------------------------------------------------------------
--  This file matches the REAL structure of ecommerce_db exactly:
--      admin, users, categories, products, orders, order_details
--
--  *** IMPORTANT - READ BEFORE IMPORTING ***
--  This file is for creating the database FROM SCRATCH on a new
--  computer. The DROP TABLE lines below DELETE ALL EXISTING DATA
--  (your customers, orders and products).
--
--  If you already have a working ecommerce_db and only want to
--  repair it, DO NOT import this file. Import database-fix.sql
--  instead - it updates the database without deleting anything.
--
--  HOW TO IMPORT (fresh install only):
--    1. Open http://localhost/phpmyadmin
--    2. Click the "Import" tab at the top.
--    3. Click "Choose File" and pick this file (database.sql).
--    4. Scroll down and click "Go".
-- ============================================================

CREATE DATABASE IF NOT EXISTS ecommerce_db
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
USE ecommerce_db;

-- Remove old tables so this file can be imported again cleanly.
-- (Order matters because of foreign keys: children first, parents last.)
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS users;

-- ------------------------------------------------------------
--  Table: admin
--  Staff accounts that can log in to the admin panel.
--  NOTE: the table is called "admin" (singular).
-- ------------------------------------------------------------
CREATE TABLE admin (
    id       INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- ------------------------------------------------------------
--  Table: users
--  Customers who register on the website.
--  The password column stores a hash, never plain text.
-- ------------------------------------------------------------
CREATE TABLE users (
    id         INT(11) AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
--  Table: categories
--  Product categories (Electronics, Clothing, ...).
-- ------------------------------------------------------------
CREATE TABLE categories (
    id   INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- ------------------------------------------------------------
--  Table: products
--  category_id is a FOREIGN KEY pointing to categories.id.
--  If a category is deleted its products are kept, but their
--  category_id becomes NULL (shown as "Uncategorized").
-- ------------------------------------------------------------
CREATE TABLE products (
    id          INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) NULL,
    name        VARCHAR(150) NOT NULL,
    description TEXT NULL,
    stock       INT(11) NOT NULL DEFAULT 0,
    price       DECIMAL(10,2) NOT NULL,
    image       VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
--  Table: orders
--  One row per placed order, linked to the user who ordered.
-- ------------------------------------------------------------
CREATE TABLE orders (
    id           INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id      INT(11) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    phone        VARCHAR(20) NULL,
    address      TEXT NULL,
    status       VARCHAR(50) DEFAULT 'Pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
--  Table: order_details
--  One row per product inside an order (an order can hold many).
--  price is copied here so the order remembers the price paid,
--  even if the product price changes later.
--  NOTE: the table is called "order_details" (not order_items).
--
--  Both foreign keys are ON DELETE CASCADE, which means deleting
--  a product also removes it from past orders. Keep this in mind
--  before deleting products that customers have already bought.
-- ------------------------------------------------------------
CREATE TABLE order_details (
    id         INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id   INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity   INT(11) NOT NULL,
    price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================================
--  SAMPLE DATA
-- ============================================================

-- ---- Admin account ----
--   Username: admin
--   Password: 123456
-- The value below is the bcrypt hash of "123456", created with
-- PHP's password_hash(). The login page checks it using
-- password_verify(), so the plain password is never stored.
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$NxC.YrNM2k/jeYwRBatvNejq3LrhQdgp27rw7PT03asfhrkImg3/q');

-- ---- Categories ----
INSERT INTO categories (name) VALUES
('Electronics'),   -- id 1
('Clothing'),      -- id 2
('Shoes'),         -- id 3
('Accessories'),   -- id 4
('Books');         -- id 5

-- ---- Products ----
-- category_id matches the ids above.
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
-- Electronics
(1, 'Wireless Headphones', 'Comfortable over-ear wireless headphones with deep bass and 20-hour battery life. Perfect for music and calls.', 59.99, 25, 'product1.svg'),
(1, 'Smart Watch', 'Track your steps, heart rate and notifications with this sleek smart watch. Water resistant and lightweight.', 129.99, 15, 'product2.svg'),
(1, 'Bluetooth Speaker', 'Portable Bluetooth speaker with rich sound and a rugged design. Great for indoor and outdoor use.', 39.99, 40, 'product3.svg'),
(1, 'USB-C Fast Charger', '20W USB-C wall charger that quickly powers up phones, tablets and other devices.', 19.99, 100, 'product4.svg'),
(1, 'Wireless Mouse', 'Ergonomic wireless mouse with silent clicks and a long-lasting battery. Plug and play.', 24.99, 60, 'product5.svg'),
-- Clothing
(2, 'Cotton T-Shirt', 'Soft 100% cotton t-shirt available in a classic fit. Breathable and comfortable for daily wear.', 14.99, 80, 'product6.svg'),
(2, 'Denim Jacket', 'Timeless denim jacket with a modern cut. A versatile layer for any season.', 49.99, 20, 'product7.svg'),
(2, 'Hooded Sweatshirt', 'Cozy fleece-lined hoodie with a front pocket and adjustable drawstring hood.', 34.99, 35, 'product8.svg'),
(2, 'Summer Dress', 'Light and flowy summer dress with a floral pattern. Perfect for warm sunny days.', 29.99, 25, 'product9.svg'),
-- Shoes
(3, 'Running Shoes', 'Lightweight running shoes with cushioned soles for maximum comfort on long runs.', 69.99, 30, 'product10.svg'),
(3, 'Leather Boots', 'Durable leather boots with a classic design. Sturdy and stylish for everyday use.', 89.99, 12, 'product11.svg'),
(3, 'Casual Sneakers', 'Everyday casual sneakers that pair with any outfit. Comfortable and easy to wear.', 44.99, 45, 'product12.svg'),
-- Accessories
(4, 'Leather Wallet', 'Slim genuine leather wallet with multiple card slots and a coin pocket.', 24.99, 50, 'product13.svg'),
(4, 'Sunglasses', 'UV-protection sunglasses with a lightweight frame and a modern look.', 19.99, 40, 'product14.svg'),
(4, 'Travel Backpack', 'Spacious water-resistant backpack with padded laptop compartment and many pockets.', 39.99, 22, 'product15.svg'),
(4, 'Classic Wrist Watch', 'Elegant analog wrist watch with a stainless steel strap. Simple and timeless.', 59.99, 18, 'product16.svg'),
-- Books
(5, 'The Great Novel', 'A best-selling fiction novel full of adventure, mystery and unforgettable characters.', 12.99, 70, 'product17.svg'),
(5, 'Learn PHP Programming', 'A beginner-friendly guide to building dynamic websites with PHP and MySQL.', 27.99, 33, 'product18.svg'),
(5, 'Cooking Made Easy', 'Over 100 simple and delicious recipes anyone can cook at home step by step.', 18.99, 28, 'product19.svg');

-- ============================================================
--  End of file
-- ============================================================
