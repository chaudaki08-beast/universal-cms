-- ============================================================
--  Universal CMS — Database Schema
--  MySQL 5.7+ / 8.0  (utf8mb4)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- ---------- Users & RBAC ------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `email`       VARCHAR(190) NOT NULL,
  `password`    VARCHAR(255) NOT NULL,
  `role`        ENUM('super_admin','editor','content_manager') NOT NULL DEFAULT 'content_manager',
  `avatar`      VARCHAR(255) DEFAULT NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `last_login`  DATETIME DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`        VARCHAR(60) NOT NULL,
  `name`        VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`   INT UNSIGNED NOT NULL,
  `ability`   VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_perm_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Templates ---------------------------------------
CREATE TABLE IF NOT EXISTS `templates` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`        VARCHAR(80) NOT NULL,
  `name`        VARCHAR(120) NOT NULL,
  `category`    VARCHAR(80) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `thumbnail`   VARCHAR(255) DEFAULT NULL,
  `blueprint`   LONGTEXT DEFAULT NULL,           -- JSON: default sections
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_templates_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Pages & Sections --------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(190) NOT NULL,
  `slug`          VARCHAR(190) NOT NULL,
  `template_id`   INT UNSIGNED DEFAULT NULL,
  `status`        ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `is_home`       TINYINT(1) NOT NULL DEFAULT 0,
  `show_header`   TINYINT(1) NOT NULL DEFAULT 1,
  `show_footer`   TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title`    VARCHAR(190) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `meta_keywords` VARCHAR(255) DEFAULT NULL,
  `og_image`      VARCHAR(255) DEFAULT NULL,
  `canonical_url` VARCHAR(255) DEFAULT NULL,
  `sort_order`    INT NOT NULL DEFAULT 0,
  `author_id`     INT UNSIGNED DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pages_slug` (`slug`),
  KEY `idx_pages_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_sections` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id`     INT UNSIGNED NOT NULL,
  `type`        VARCHAR(60) NOT NULL,            -- hero, text, image, gallery, cards, testimonials, faq, pricing, cta, contact, map, html
  `title`       VARCHAR(190) DEFAULT NULL,       -- admin label
  `data`        LONGTEXT DEFAULT NULL,           -- JSON: all editable fields for the section
  `settings`    LONGTEXT DEFAULT NULL,           -- JSON: layout/style (bg, padding, align, height...)
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_visible`  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_sections_page` (`page_id`),
  CONSTRAINT `fk_sections_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Menus -------------------------------------------
CREATE TABLE IF NOT EXISTS `menus` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`      VARCHAR(80) NOT NULL,              -- 'primary', 'footer'
  `name`      VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_menus_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id`    INT UNSIGNED NOT NULL,
  `parent_id`  INT UNSIGNED DEFAULT NULL,
  `label`      VARCHAR(120) NOT NULL,
  `url`        VARCHAR(255) NOT NULL DEFAULT '#',
  `target`     VARCHAR(20) NOT NULL DEFAULT '_self',
  `icon`       VARCHAR(60) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_menuitems_menu` (`menu_id`),
  CONSTRAINT `fk_menuitems_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Settings (key/value) ----------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group`  VARCHAR(60) NOT NULL DEFAULT 'general',
  `key`    VARCHAR(120) NOT NULL,
  `value`  LONGTEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Media Library -----------------------------------
CREATE TABLE IF NOT EXISTS `media` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `folder`     VARCHAR(190) NOT NULL DEFAULT '/',
  `name`       VARCHAR(255) NOT NULL,
  `path`       VARCHAR(255) NOT NULL,            -- relative to /uploads
  `mime`       VARCHAR(100) DEFAULT NULL,
  `size`       INT UNSIGNED DEFAULT 0,
  `alt`        VARCHAR(255) DEFAULT NULL,
  `uploaded_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_media_folder` (`folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Blog --------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`       ENUM('post','product') NOT NULL DEFAULT 'post',
  `parent_id`  INT UNSIGNED DEFAULT NULL,
  `name`       VARCHAR(120) NOT NULL,
  `slug`       VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image`      VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug_type` (`slug`,`type`),
  KEY `idx_categories_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(190) NOT NULL,
  `slug`          VARCHAR(190) NOT NULL,
  `excerpt`       VARCHAR(500) DEFAULT NULL,
  `body`          LONGTEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `category_id`   INT UNSIGNED DEFAULT NULL,
  `tags`          VARCHAR(500) DEFAULT NULL,
  `status`        ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `author_id`     INT UNSIGNED DEFAULT NULL,
  `meta_title`    VARCHAR(190) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `published_at`  DATETIME DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_posts_slug` (`slug`),
  KEY `idx_posts_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`    INT UNSIGNED NOT NULL,
  `author`     VARCHAR(120) NOT NULL,
  `email`      VARCHAR(190) DEFAULT NULL,
  `body`       TEXT NOT NULL,
  `status`     ENUM('pending','approved','spam') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comments_post` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- E-commerce --------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(190) NOT NULL,
  `slug`          VARCHAR(190) NOT NULL,
  `sku`           VARCHAR(80) DEFAULT NULL,
  `description`   LONGTEXT DEFAULT NULL,
  `short_description` VARCHAR(500) DEFAULT NULL,
  `price`         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `sale_price`    DECIMAL(12,2) DEFAULT NULL,
  `stock`         INT NOT NULL DEFAULT 0,
  `category_id`   INT UNSIGNED DEFAULT NULL,
  `images`        LONGTEXT DEFAULT NULL,          -- JSON array of media paths
  `variations`    LONGTEXT DEFAULT NULL,          -- JSON
  `status`        ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `featured`      TINYINT(1) NOT NULL DEFAULT 0,
  `meta_title`    VARCHAR(190) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  KEY `idx_products_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customers` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(190) NOT NULL,
  `phone`      VARCHAR(40) DEFAULT NULL,
  `password`   VARCHAR(255) DEFAULT NULL,
  `address`    TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customers_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number`  VARCHAR(40) NOT NULL,
  `customer_id`   INT UNSIGNED DEFAULT NULL,
  `customer_name` VARCHAR(150) DEFAULT NULL,
  `customer_email` VARCHAR(190) DEFAULT NULL,
  `customer_phone` VARCHAR(40) DEFAULT NULL,
  `items`         LONGTEXT DEFAULT NULL,          -- JSON line items
  `subtotal`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `tax`           DECIMAL(12,2) NOT NULL DEFAULT 0,
  `shipping`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total`         DECIMAL(12,2) NOT NULL DEFAULT 0,
  `status`        ENUM('pending','paid','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` VARCHAR(60) DEFAULT NULL,
  `notes`         TEXT DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Forms Builder -----------------------------------
CREATE TABLE IF NOT EXISTS `forms` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(150) NOT NULL,
  `fields`      LONGTEXT DEFAULT NULL,            -- JSON field definitions
  `notify_email` VARCHAR(190) DEFAULT NULL,
  `success_message` VARCHAR(255) DEFAULT 'Thank you! Your submission has been received.',
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_forms_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `form_entries` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id`    INT UNSIGNED NOT NULL,
  `data`       LONGTEXT DEFAULT NULL,             -- JSON submitted values
  `ip`         VARCHAR(45) DEFAULT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_entries_form` (`form_id`),
  CONSTRAINT `fk_entries_form` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- SEO settings (per entity overrides) -------------
CREATE TABLE IF NOT EXISTS `seo_settings` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type`  VARCHAR(40) NOT NULL,            -- page, post, product
  `entity_id`    INT UNSIGNED NOT NULL,
  `meta_title`   VARCHAR(190) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `keywords`     VARCHAR(255) DEFAULT NULL,
  `og_image`     VARCHAR(255) DEFAULT NULL,
  `canonical_url` VARCHAR(255) DEFAULT NULL,
  `no_index`     TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_seo_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
