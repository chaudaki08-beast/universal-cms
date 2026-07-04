# Universal CMS — Website Builder Platform

A modular, fully-responsive **Core PHP 8 + MySQL** CMS for building websites of any
category (Hotel, Restaurant, E-commerce, Corporate, Portfolio, Blog, Real Estate,
Services, …). Every element is editable from the admin panel — no code required.
Built to run on **shared cPanel hosting** with no Node.js runtime dependency.

---

## ✨ Features

| Area | What you get |
|------|--------------|
| **Admin Dashboard** | Login/logout, role-based access (Super Admin / Editor / Content Manager), stat widgets, recent activity |
| **Page Builder** | Unlimited pages, drag-to-reorder **sections**, duplicate/delete, slug & SEO per page |
| **Section types** | Hero, Text, Image, Gallery, Cards, Testimonials, FAQ, Pricing, CTA, Contact/Form, Map, Custom HTML |
| **Theme Engine** | Logo, favicon, colors, fonts, border-radius, button style, header/footer layout, layout width, sticky header — all live via CSS variables |
| **Header / Footer CMS** | Editable menus (with dropdowns), social icons, contact bar, footer columns & copyright |
| **Category Templates** | Hotel, Restaurant, E-commerce, Corporate, Portfolio, Blog, Real Estate, Services, Blank — pre-fill sections |
| **E-commerce** | Products (images, price, sale price, SKU, stock, categories), cart, checkout, orders, payment-gateway-ready structure |
| **Blog** | Posts, categories, tags, featured image, author, SEO fields |
| **Media Library** | Upload (multi), folders, search, rename, delete, reusable media picker |
| **Forms Builder** | Build forms with dynamic fields, email notifications, stored submissions, anti-spam honeypot |
| **SEO** | Meta title/description/keywords, OG image, canonical, auto `sitemap.xml` + `robots.txt` |
| **Security** | Password hashing, CSRF tokens, prepared statements, output escaping, secure validated uploads, hardened session, RBAC |

---

## 📦 Requirements

- PHP **8.0+** with `pdo_mysql`, `mbstring`, `fileinfo`, and `gd` (or `imagick`)
- MySQL **5.7+** / MariaDB 10.3+
- Apache with `mod_rewrite` (standard on cPanel)

---

## 🚀 Installation (cPanel)

1. **Upload** the contents of this folder to your hosting. Either:
   - `public_html/` (site at `example.com`), or
   - `public_html/cms/` (site at `example.com/cms/`).
   > If installing in a subfolder, open `.htaccess` and uncomment
   > `RewriteBase /cms/` (use your folder name).

2. **Create a database** in cPanel → *MySQL® Databases*: a database, a user, and
   add the user to the database with **All Privileges**.

3. **Set permissions**: ensure `uploads/`, `storage/`, and `config/` are writable
   (`755`, or `775` if required by your host).

4. **Run the installer**: visit `https://your-domain/` (or `/cms/`). You'll be
   redirected to the setup wizard:
   1. Requirements check
   2. Database connection
   3. Site name, starter **template**, and **admin account**
   4. Install → done

5. **Secure it**: after installation, **delete the `/install` directory**.

6. **Log in** at `https://your-domain/admin` with the admin account you created.

---

## 🗂️ Project Structure

```
cms/
├── index.php              Front controller (single entry point)
├── .htaccess              Pretty-URL routing + security headers
├── install/               Setup wizard (delete after install)
│   ├── index.php
│   ├── schema.sql         All database tables
│   └── seed.sql           Templates, settings, default form/menus
├── config/                Generated config.php (private)
├── app/
│   ├── Core/              Framework: Router, Database (PDO), Auth, Csrf,
│   │                      Session, View, Request, Upload, Mailer, Helpers
│   ├── Controllers/
│   │   ├── Admin/         Dashboard, Pages, Posts, Products, Orders,
│   │   │                  Media, Menus, Forms, Users, Categories, Settings
│   │   └── Front/         Page, Blog, Shop, Cart, Form, Seo renderers
│   ├── Models/            User, Page, Post, Product, Order, Media, …
│   └── Views/
│       ├── layouts/       admin + front layouts
│       ├── admin/         Admin UI (incl. page builder + section field editors)
│       └── front/         Public templates + section partials
├── assets/                Compiled CSS/JS (Bootstrap 5 + Font Awesome via CDN)
├── uploads/               User media (PHP execution disabled)
└── storage/               Internal (logs etc.)
```

---

## 🧩 Extending the CMS

**Add a new section type** (e.g. `video`):
1. Add it to `sectionTypes()` and `defaultData()` in
   `app/Controllers/Admin/PagesController.php`.
2. Create the editor: `app/Views/admin/pages/fields/video.php`.
3. Create the renderer: `app/Views/front/sections/video.php`.
That's it — it appears in the “Add Section” menu automatically.

**Add a new category template**: insert a row into `templates` (slug, name,
category, `blueprint` JSON listing default sections). It shows up in the page
"Create" screen and the installer.

**Wire a payment gateway**: see the marked hook in
`app/Controllers/Front/CartController.php@checkout` — create the order, call your
gateway (Stripe/PayPal/Razorpay), and set the order `status` to `paid` on success.

---

## 🔐 Security Notes

- Passwords use `password_hash()` (bcrypt) with transparent rehashing.
- All DB access is via **prepared statements** (`app/Core/Database.php`).
- Every state-changing request requires a **CSRF token** (`app/Core/Csrf.php`).
- All output is escaped with `e()`; rich HTML fields strip `<script>`.
- Uploads are MIME-validated, extension-whitelisted, randomly renamed, and the
  `uploads/` folder disables PHP execution.
- Keep `APP_DEBUG` **false** in production.
