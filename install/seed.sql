-- ============================================================
--  Universal CMS — Seed data (templates, menus, settings)
--  Run after schema.sql. Admin user + home page are created
--  by the installer with the values you provide.
-- ============================================================

-- Default menus -------------------------------------------------
INSERT INTO `menus` (`slug`,`name`) VALUES
  ('primary','Primary Navigation'),
  ('footer','Footer Menu')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Category templates with section blueprints (JSON) -------------
INSERT INTO `templates` (`slug`,`name`,`category`,`description`,`blueprint`) VALUES
('blank','Blank Page','general','Start from scratch.',
 '[{"type":"hero","title":"Hero"},{"type":"text","title":"Intro"}]'),

('hotel','Hotel & Resort','hotel','Rooms, amenities, booking, gallery, location, reviews.',
 '[{"type":"hero","title":"Hero"},{"type":"cards","title":"Rooms"},{"type":"text","title":"Amenities"},{"type":"gallery","title":"Gallery"},{"type":"map","title":"Location"},{"type":"testimonials","title":"Reviews"},{"type":"cta","title":"Book Now"}]'),

('restaurant','Restaurant','restaurant','Menu, food gallery, reservation, chef.',
 '[{"type":"hero","title":"Hero"},{"type":"pricing","title":"Menu"},{"type":"gallery","title":"Food Gallery"},{"type":"text","title":"Chef"},{"type":"contact","title":"Reservation"}]'),

('ecommerce','E-commerce Store','ecommerce','Featured products, categories, promo.',
 '[{"type":"hero","title":"Hero"},{"type":"cards","title":"Featured Products"},{"type":"cards","title":"Categories"},{"type":"cta","title":"Shop Now"}]'),

('corporate','Corporate / Business','corporate','About, services, team, projects, contact.',
 '[{"type":"hero","title":"Hero"},{"type":"text","title":"About"},{"type":"cards","title":"Services"},{"type":"cards","title":"Team"},{"type":"gallery","title":"Projects"},{"type":"contact","title":"Contact"}]'),

('portfolio','Portfolio','portfolio','Showcase work and skills.',
 '[{"type":"hero","title":"Hero"},{"type":"text","title":"About"},{"type":"gallery","title":"Work"},{"type":"cards","title":"Skills"},{"type":"contact","title":"Hire Me"}]'),

('blog','Blog','blog','Article-focused homepage.',
 '[{"type":"hero","title":"Hero"},{"type":"text","title":"Welcome"},{"type":"cta","title":"Subscribe"}]'),

('realestate','Real Estate','realestate','Listings, search, agents.',
 '[{"type":"hero","title":"Hero"},{"type":"cards","title":"Listings"},{"type":"text","title":"Why Us"},{"type":"cards","title":"Agents"},{"type":"contact","title":"Enquiry"}]'),

('services','Services / Agency','services','Service offerings and process.',
 '[{"type":"hero","title":"Hero"},{"type":"cards","title":"Services"},{"type":"text","title":"Process"},{"type":"pricing","title":"Pricing"},{"type":"faq","title":"FAQ"},{"type":"cta","title":"Get Started"}]')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `blueprint`=VALUES(`blueprint`);

-- A default contact form ---------------------------------------
INSERT INTO `forms` (`name`,`slug`,`fields`,`success_message`) VALUES
('Contact Form','contact',
 '[{"name":"name","label":"Your Name","type":"text","required":true},{"name":"email","label":"Email","type":"email","required":true},{"name":"phone","label":"Phone","type":"text","required":false},{"name":"message","label":"Message","type":"textarea","required":true}]',
 'Thank you! We will get back to you shortly.')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Global settings (theme + site) -------------------------------
INSERT INTO `settings` (`group`,`key`,`value`) VALUES
('general','site_name','My Website'),
('general','site_tagline','Built with Universal CMS'),
('general','logo',''),
('general','favicon',''),
('general','currency_symbol','$'),
('general','contact_email',''),
('general','contact_phone',''),
('general','contact_address',''),
('theme','primary_color','#2563eb'),
('theme','secondary_color','#0f172a'),
('theme','accent_color','#f59e0b'),
('theme','body_font','Inter'),
('theme','heading_font','Poppins'),
('theme','border_radius','10'),
('theme','button_style','solid'),
('theme','layout_width','1200'),
('theme','header_layout','classic'),
('theme','sticky_header','1'),
('theme','footer_layout','columns'),
('seo','meta_title','My Website'),
('seo','meta_description','Welcome to my website.'),
('seo','meta_keywords',''),
('seo','og_image',''),
('social','facebook',''),
('social','instagram',''),
('social','twitter',''),
('social','linkedin',''),
('social','youtube','')
ON DUPLICATE KEY UPDATE `value`=`settings`.`value`;
