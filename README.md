# ShopEasy – Simple E-Commerce Website

A complete, functional e-commerce website built as a **university Web Programming
project**. It has a customer side (browse, cart, checkout, orders) and an admin
side (manage products, categories, orders and customers). Everything runs locally
on **XAMPP** – no internet, no frameworks, no payment gateways.

---

## 1. Project Overview

ShopEasy is a small online store where:

- **Customers** can register, log in, browse and search products, filter by
  category, view product details, add items to a shopping cart, check out using
  **Cash on Delivery**, and see their order history.
- **Administrators** can log in to a separate admin panel, see dashboard
  statistics, and perform full **CRUD** (Create, Read, Update, Delete) on
  products and categories, view all orders and change their status, and view
  the list of registered customers.

The site is intentionally kept **simple and easy to explain** for a viva.

---

## 2. Technologies Used

| Layer            | Technology                    |
|------------------|-------------------------------|
| Structure        | HTML5                         |
| Styling          | CSS3 (custom, responsive, no framework) |
| Interactivity    | Vanilla JavaScript            |
| Server language  | PHP (procedural, MySQLi)      |
| Database         | MySQL                         |
| Server package   | XAMPP (Apache + MySQL)        |
| DB admin tool    | phpMyAdmin                    |
| Editor           | Visual Studio Code            |

**No** React/Vue/Angular, Node.js, Laravel, Bootstrap, Firebase, cloud services
or online payment gateways are used.

---

## 3. How to Install XAMPP

1. Download XAMPP from <https://www.apachefriends.org>.
2. Run the installer and install it (default location is `C:\xampp`).
3. Open the **XAMPP Control Panel**.

---

## 4. Where to Put the Project

Copy the whole `ecommerce` folder into the XAMPP web root:

```
C:\xampp\htdocs\ecommerce\
```

So, for example, the home page file should be at:

```
C:\xampp\htdocs\ecommerce\index.php
```

---

## 5. Start Apache and MySQL

In the **XAMPP Control Panel**, click **Start** next to:

- **Apache**
- **MySQL**

Both should turn green.

---

## 6. Create / Import the Database (phpMyAdmin)

1. Open your browser and go to: <http://localhost/phpmyadmin>
2. Click the **Import** tab (top menu).
3. Click **Choose File** and select the `database.sql` file from this project.
4. Click **Go** (bottom of the page).

This automatically:

- creates the database **`ecommerce_db`**,
- creates all tables (users, admin, categories, products, orders, order_details),
- inserts sample data (5 categories, 19 products, and 1 admin account).

> You do **not** need to create the database manually – the SQL file does it for you.

---

## 7. Access the Website (Customer Side)

Open:

```
http://localhost/ecommerce/
```

### Demo customer account
You can register your own account, or log in with a ready-made one:

- **Email:** `john@example.com`
- **Password:** `password123`

(Other demo customers: `sarah@example.com`, `mike@example.com` – same password.)

---

## 8. Access the Admin Panel

Open:

```
http://localhost/ecommerce/admin/
```

### Default admin credentials
- **Username:** `admin`
- **Password:** `admin123`

> These are set in `database.sql`. The password is stored as a secure bcrypt
> hash, not as plain text.

---

## 9. Project Structure

```
ecommerce/
│
├── index.php                # Home page (hero, categories, featured products)
├── products.php             # All products + search + category filter
├── product-details.php      # Single product + quantity + add to cart
├── cart.php                 # Shopping cart (add/update/remove/clear)
├── checkout.php             # Checkout + place order (Cash on Delivery)
├── order-confirmation.php   # "Thank you" page after ordering
├── login.php                # Customer login
├── register.php             # Customer registration
├── logout.php               # Customer logout
├── account.php              # Customer profile + recent orders
├── orders.php               # Customer order history + order details
├── about.php                # About page
├── contact.php              # Contact form
│
├── config/
│   └── database.php         # MySQL connection (MySQLi)
│
├── includes/
│   ├── functions.php        # Helpers + session + cart helpers + product card
│   ├── auth.php             # Customer login helpers
│   ├── header.php           # Navigation bar (shared)
│   └── footer.php           # Footer (shared)
│
├── css/
│   └── style.css            # All styling (responsive, media queries)
│
├── js/
│   └── script.js            # Menu toggle, form validation, quantity limits
│
├── images/                  # Product images (SVG placeholders)
│
├── admin/
│   ├── index.php            # Admin dashboard (statistics)
│   ├── login.php            # Admin login
│   ├── logout.php           # Admin logout
│   ├── products.php         # Product list
│   ├── add-product.php      # Add product (with image upload)
│   ├── edit-product.php     # Edit product
│   ├── delete-product.php   # Delete product
│   ├── categories.php       # Category CRUD
│   ├── orders.php           # Order list + details + status change
│   ├── customers.php        # Customer list
│   └── includes/
│       ├── admin-auth.php   # Admin login helpers
│       ├── admin-header.php # Admin top bar + sidebar
│       └── admin-footer.php # Admin footer
│
├── database.sql             # Database + tables + sample data
└── README.md                # This file
```

---

## 10. How Each Part Works (for the viva)

**Frontend:** HTML is written inside the PHP pages. `css/style.css` styles
everything and uses **media queries** so the site works on desktop, laptop,
tablet and mobile. `js/script.js` adds the mobile menu, client-side form
validation and keeps quantities within stock.

**PHP ↔ MySQL:** `config/database.php` opens a MySQLi connection to
`ecommerce_db`. Every page that needs data includes this file and runs queries
using **prepared statements** (`prepare()` + `bind_param()`), which prevents SQL
injection.

**Registration/Login:** Passwords are hashed with `password_hash()` on register
and checked with `password_verify()` on login. After a successful login the user
is stored in a **PHP session** (`$_SESSION`), which is how the site remembers who
is logged in across pages.

**Sessions:** `session_start()` runs in `includes/functions.php`. The customer
session uses `user_id`; the admin session uses `admin_id`. They are separate, so
being a customer does not make you an admin.

**Products:** Products live in the `products` table and each belongs to a
category (`category_id` foreign key). Admins add/edit/delete them via CRUD pages.

**Shopping cart:** The cart is stored in the session as a simple array
`$_SESSION['cart'] = [ product_id => quantity ]`. This is the simplest reliable
approach and needs no extra table. Quantities can never exceed the product stock.

**Orders:** At checkout the order is saved in `orders`, each item in
`order_details`, and product stock is reduced – all inside a **transaction** so it
either fully succeeds or fully rolls back. Then the cart is emptied.

**Admin panel:** A separate `/admin` section protected by an admin session. It
shows dashboard statistics and provides CRUD for products/categories, order
status management and a customer list.

**Table relationships:**
- `products.category_id` → `categories.id`
- `orders.user_id` → `users.id`
- `order_details.order_id` → `orders.id`
- `order_details.product_id` → `products.id`

---

## 11. Security Practices Used

- PHP **sessions** for authentication (customer and admin).
- `password_hash()` / `password_verify()` – passwords are never stored as text.
- **Prepared statements** for all database queries.
- Input validation in both **JavaScript** (browser) and **PHP** (server).
- `htmlspecialchars()` (via the `e()` helper) on all output to prevent XSS.
- Admin pages call `require_admin_login()`, so they cannot be opened without
  logging in.

---

## 12. Quick Test Checklist

**Customer:** register → login → browse → search → filter by category →
open product → add to cart → update/remove in cart → checkout →
see confirmation → view order history → logout.

**Admin:** login (`admin` / `admin123`) → dashboard → add/edit/delete product →
add/edit/delete category → view orders → change order status → view customers.

---

## 13. Common Problems

| Problem | Fix |
|--------|-----|
| "Database connection failed" | Start **MySQL** in XAMPP and import `database.sql`. |
| Page shows PHP code as text | Open the site through `http://localhost/...`, not by double-clicking the file. |
| Images not showing | Make sure the `images` folder was copied. A placeholder is shown automatically if an image is missing. |
| Admin page redirects to login | That is correct – log in at `http://localhost/ecommerce/admin/`. |

---

*This project is for educational purposes only. It is not a real store and takes
no real payments.*
