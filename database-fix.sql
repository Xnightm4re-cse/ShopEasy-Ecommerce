-- ============================================================
--  ShopEasy  -  DATABASE REPAIR SCRIPT
--  Database: ecommerce_db
-- ------------------------------------------------------------
--  WHAT THIS FILE IS FOR
--  This file updates an EXISTING ecommerce_db. It does NOT drop
--  or recreate any table, and it does NOT delete your data.
--
--  It does four things:
--    1. Adds the missing "stock" column to products.
--    2. Adds the missing "phone" and "address" columns to orders.
--    3. Replaces the plain-text admin password with a secure hash.
--    4. Adds sample categories and products (the tables were empty).
--
--  HOW TO RUN IT IN phpMyAdmin
--    1. Open http://localhost/phpmyadmin
--    2. Click "ecommerce_db" in the left-hand list.
--    3. Click the "Import" tab at the top.
--    4. Click "Choose File" and pick this file (database-fix.sql).
--    5. Scroll down and click "Go".
--
--  NOTE: this script was already run for you. Re-running steps 1
--  and 2 will report "Duplicate column name" - that is harmless
--  and simply means the column is already there. Only re-run
--  step 4 if your products/categories tables are empty, or you
--  will get duplicate products.
-- ============================================================

USE ecommerce_db;

-- ------------------------------------------------------------
--  1. products.stock
--  The shop code tracks how many of each item are available
--  (the "In Stock" label, the quantity limit in the cart, and
--  the stock reduction at checkout). The column was missing.
-- ------------------------------------------------------------
ALTER TABLE products
    ADD COLUMN stock INT(11) NOT NULL DEFAULT 0 AFTER price;

-- ------------------------------------------------------------
--  2. orders.phone and orders.address
--  The checkout page asks the customer for a phone number and a
--  delivery address, and the admin order page displays them.
--  Both columns were missing.
-- ------------------------------------------------------------
ALTER TABLE orders
    ADD COLUMN phone   VARCHAR(20) NULL AFTER total_amount,
    ADD COLUMN address TEXT        NULL AFTER phone;

-- ------------------------------------------------------------
--  3. Secure the admin password
--  The password was stored as the plain text "123456", which
--  password_verify() can never match. The value below is the
--  bcrypt hash of "123456" produced by PHP password_hash().
--
--  The login details DO NOT CHANGE:
--      Username: admin
--      Password: 123456
-- ------------------------------------------------------------
UPDATE admin
SET password = '$2y$10$NxC.YrNM2k/jeYwRBatvNejq3LrhQdgp27rw7PT03asfhrkImg3/q'
WHERE username = 'admin';

-- ------------------------------------------------------------
--  4. Sample categories and products
--  The categories and products tables were completely empty, so
--  the shop had nothing to display. These are safe starter rows.
--  Skip this section if you already have your own products.
-- ------------------------------------------------------------
INSERT INTO categories (name) VALUES
('Electronics'),
('Clothing'),
('Shoes'),
('Accessories'),
('Books');

-- category_id below matches the ids created just above (1-5).
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
