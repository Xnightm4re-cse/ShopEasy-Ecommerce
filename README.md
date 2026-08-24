# ShopEasy – E-Commerce Website

A complete, responsive, and functional e-commerce web application developed as an individual university **Web Programming Lab** project. Built using pure **HTML5, CSS3, Vanilla JavaScript, PHP (procedural with MySQLi), and MySQL**, running locally on **XAMPP** without external frameworks or dependencies.

---

## 1. Project Overview

**ShopEasy** is a lightweight, academic e-commerce solution designed to demonstrate foundational full-stack web development principles:

- **Customer Storefront:** Users can register an account, securely authenticate, explore categorized product catalogs, search items in real-time, view detailed specifications, manage dynamic shopping cart quantities, and complete purchases via Cash on Delivery (COD) with immediate inventory updates.
- **Administration Portal:** Administrators have access to a dedicated, authenticated management dashboard providing summary analytics, full CRUD operations on products and categories, real-time order tracking with status lifecycle management, and a directory of registered customers.

---

## 2. Features

### Customer Module
- **User Authentication:** Secure customer registration, login verification using PHP `password_verify()`, session persistence, and profile overview with order history.
- **Product Browsing & Filtering:** Interactive catalog displaying products by category chips and keyword search queries using MySQLi prepared statements.
- **Product Details:** Single product view featuring pricing, inventory availability badge (In Stock / Out of Stock), descriptions, and stock-clamped quantity selection.
- **Shopping Cart:** Session-based cart (`$_SESSION['cart']`) supporting item additions, quantity updates constrained to available stock, single-item removal, subtotal calculations, and full cart clearing.
- **Checkout & Order Placement:** Delivery details validation and atomic database transaction processing (order creation, line item insertion, and product stock decrement).
- **Order History & Tracking:** Dedicated order list and individual order summary page displaying line items, unit prices, date, and order status.

### Admin Module
- **Admin Authentication:** Secure access control protecting administrative endpoints via dedicated admin sessions (`$_SESSION['admin_id']`).
- **Dashboard Analytics:** Real-time metrics tracking total inventory count, registered user accounts, order volume, and cumulative revenue.
- **Product Management (CRUD):** Add new products with image upload handling, update existing items, and delete products with automatic image placeholder fallback.
- **Category Management (CRUD):** Create, update, and remove product categories with foreign key constraints (`ON DELETE SET NULL`).
- **Order Management:** View all incoming customer orders and transition statuses through `Pending`, `Processing`, `Completed`, and `Cancelled`.
- **Customer Directory:** Read-only listing of all registered customer accounts and their total order counts.

---

## 3. Technologies Used

| Component | Technology | Purpose |
| :--- | :--- | :--- |
| **Structure** | HTML5 | Semantic markup across customer and administrative interfaces |
| **Styling** | CSS3 | Custom responsive stylesheet with CSS Grid, Flexbox, and media queries |
| **Interactivity** | Vanilla JavaScript | Client-side form validation, mobile navigation toggles, stock quantity constraints |
| **Server Engine** | PHP 8.x (procedural) | Backend routing, session management, transaction logic, MySQLi prepared statements |
| **Database** | MySQL | Relational data persistence with foreign keys and cascade rules |
| **Environment** | XAMPP | Local web server package (Apache + MySQL / MariaDB + phpMyAdmin) |

> **Note:** This project intentionally avoids heavy frontend and backend frameworks (e.g., React, Vue, Node.js, Laravel, Tailwind CSS) to showcase native web development competencies suitable for university lab examination and viva voce.

---

## 4. Database Structure

The relational database `ecommerce_db` consists of 6 structured tables:

```
categories (id)  ────────< products (category_id)
                                  │ (id)
                                  │
users (id) ──────< orders (user_id)│
                       │ (id)     │
                       │          │
                       └───< order_details >───┘
                             (order_id, product_id)

admin (id, username, password)
```

### Table Descriptions
- `admin`: Stores administrative credentials (`id`, `username`, `password`).
- `users`: Stores registered customer information (`id`, `name`, `email`, `password`, `created_at`).
- `categories`: Stores product taxonomy (`id`, `name`).
- `products`: Stores product listings (`id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `created_at`).
- `orders`: Stores customer purchase orders (`id`, `user_id`, `total_amount`, `phone`, `address`, `status`, `created_at`).
- `order_details`: Stores individual line items per order (`id`, `order_id`, `product_id`, `quantity`, `price`).

---

## 5. Local Installation Guide

### Prerequisites
1. Download and install [XAMPP](https://www.apachefriends.org/) (PHP 7.4+ or PHP 8.x).
2. Start the **Apache** and **MySQL** services from the XAMPP Control Panel.

### Installation Steps
1. **Clone or Copy Repository:**
   Place the project directory inside your local XAMPP `htdocs` folder:
   ```text
   C:\xampp\htdocs\ecommerce\
   ```

2. **Create & Import Database:**
   - Open phpMyAdmin in your web browser: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
   - Click the **Import** tab.
   - Click **Choose File** and select `database.sql` from the project root.
   - Click **Go** at the bottom of the page.
   *(This automatically creates `ecommerce_db`, tables, relationships, and starter demo data).*

3. **Configure Database Connection:**
   Open `config/database.php` and ensure the connection parameters match your local server (default XAMPP settings):
   ```php
   $db_host = 'localhost';
   $db_user = 'root';
   $db_pass = '';
   $db_name = 'ecommerce_db';
   ```

4. **Launch Application:**
   - **Customer Storefront:** [http://localhost/ecommerce/](http://localhost/ecommerce/)
   - **Admin Login:** [http://localhost/ecommerce/admin/login.php](http://localhost/ecommerce/admin/login.php)

---

## 6. Administrator Demo Account

> [!IMPORTANT]
> **Academic / Demonstration Account:**
> - **Username:** `admin`
> - **Password:** `admin000`
>
> *(The password is stored securely in MySQL using a standard bcrypt hash via `password_hash()`, verified via `password_verify()`).*

---

## 7. Screenshots

*(Screenshots can be added to an `/assets/screenshots` folder for demonstration)*

| Interface | Description | Placeholder |
| :--- | :--- | :--- |
| **Homepage** | Hero banner, categories, featured products | `![Homepage](screenshots/homepage.png)` |
| **Products Catalog** | Filterable product grid with live search | `![Products](screenshots/products.png)` |
| **Product Details** | Full description, stock count, add-to-cart | `![Product Details](screenshots/product_details.png)` |
| **Shopping Cart** | Item management, quantity controls, totals | `![Cart](screenshots/cart.png)` |
| **Customer Login** | Secure customer authentication | `![Login](screenshots/login.png)` |
| **Admin Dashboard** | Statistics overview and recent order cards | `![Admin Dashboard](screenshots/admin_dashboard.png)` |
| **Product Management** | Product listing table with Add/Edit/Delete | `![Product Management](screenshots/admin_products.png)` |
| **Order Management** | Order listing and status updates | `![Order Management](screenshots/admin_orders.png)` |

---

## 8. Security Implementations

- **SQL Injection Prevention:** 100% of dynamic database queries use MySQLi prepared statements (`prepare()` and `bind_param()`).
- **Cross-Site Scripting (XSS) Mitigation:** Output sanitization using the custom `e()` helper function wrapping `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **Password Security:** Cryptographic password hashing utilizing PHP's native `password_hash($password, PASSWORD_DEFAULT)` and `password_verify()`.
- **Session Protection:** Segregated sessions for customers (`user_id`) and administrators (`admin_id`), with strict authentication guards (`require_login()` and `require_admin_login()`).
- **Transactional Integrity:** ACID-compliant database transactions (`begin_transaction`, `commit`, `rollback`) protecting multi-step checkout processes.

---

## 9. Online Deployment (Hosting)

To deploy this project to an online PHP/MySQL web host (such as cPanel or Apache VPS):

1. **Upload Files:** Upload the repository files to `public_html` or a designated subdomain folder.
2. **Import Database:** Create a MySQL database in cPanel MySQL Database Wizard, open phpMyAdmin online, and import `database.sql`.
3. **Set Database Credentials:**
   Update `config/database.php` with your hosting credentials or configure server environment variables:
   - `DB_HOST`: Hostname (usually `localhost`)
   - `DB_USER`: Database username (e.g. `u123456_ecom`)
   - `DB_PASS`: Database password
   - `DB_NAME`: Database name (e.g. `u123456_ecommerce_db`)
4. **File Permissions:** Ensure the `images/` directory has write permissions (`755` or `775`) for product image uploads.

---

## 10. Future Improvements

- Integration of real-time online payment gateways (e.g. Stripe, SSLCommerz, PayPal).
- Customer product review and star rating system.
- Customer wishlist and saved-for-later items.
- Automated email order confirmations via PHPMailer / SMTP.
- Customer order tracking timeline and shipment status.
- Admin pagination and multi-criteria product filtering.

---

## 11. Academic License & Disclaimer

This project was built for educational and academic submission purposes as part of a university Web Programming curriculum. It is intended for coursework demonstration and evaluation.
