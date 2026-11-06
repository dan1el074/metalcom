-- Metalcom: estrutura e conteúdo inicial do site em 06/11/2026.
-- Importar somente em um banco vazio. Não executar sobre o site existente.
-- Imagens locais: enviar public/assets e public/uploads junto com o projeto.
-- Usuários e mensagens recebidas não fazem parte do conteúdo inicial.
-- O primeiro administrador é criado em /install.

SET NAMES utf8mb4;
SET @METALCOM_OLD_SQL_MODE = @@SQL_MODE;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET @METALCOM_OLD_TIME_ZONE = @@TIME_ZONE;
SET time_zone = '-03:00';

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_categories_order` (`active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `description` text NOT NULL,
  `code` varchar(60) DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `view_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_products_category` (`category_id`),
  KEY `idx_products_catalog` (`active`,`category_id`,`sort_order`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product_images_order` (`product_id`,`sort_order`),
  KEY `idx_product_images_featured` (`featured`,`sort_order`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `special_projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_projects_order` (`active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(190) NOT NULL,
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `delivery_method` enum('email','whatsapp') NOT NULL DEFAULT 'whatsapp',
  `metalcom_email_sent` tinyint(1) DEFAULT NULL,
  `customer_email_sent` tinyint(1) DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contact_created` (`created_at`),
  KEY `idx_contact_unread` (`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

INSERT INTO `settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('about_description', 'A Metalcom Marmentini é uma empresa especializada na fabricação de produtos metálicos de alta qualidade. Unimos experiência, tecnologia e um time comprometido para entregar soluções que superam expectativas.', '2026-10-14 16:41:53'),
('about_eyebrow', 'Sobre a Metalcom', '2026-10-14 16:41:53'),
('about_image', 'assets/soldador-metalcom-sem-filtro.png', '2026-11-05 19:53:04'),
('about_title', 'Tradição, tecnologia e compromisso.', '2026-10-14 16:41:53'),
('contact_action', 'whatsapp', '2026-11-06 08:43:15'),
('contact_address', 'Rua Floresta, 139, Barão de Cotegipe, RS, 99740-000', '2026-10-14 16:41:53'),
('contact_email', 'contato@metalcom.com.br', '2026-10-14 16:41:53'),
('contact_facebook', 'https://www.facebook.com/people/Metal%C3%BArgica-Metalcom', '2026-11-05 20:33:29'),
('contact_instagram', 'https://www.instagram.com/metalcom.ind/', '2026-11-05 19:48:24'),
('contact_phone', '(54) 9 9163-4643', '2026-10-14 16:41:53'),
('contact_whatsapp', '5554991634643', '2026-10-14 16:41:53'),
('customer_auto_reply', '1', '2026-11-06 08:36:11'),
('customer_reply_text', 'Olá! Recebemos sua mensagem e a equipe Metalcom entrará em contato em breve.', '2026-10-14 16:41:53'),
('hero_image_1', 'assets/hero-reboque-cavalo.png', '2026-11-06 08:07:16'),
('hero_image_2', 'uploads/9c29e64ae3fe63a24e34c493891174f5.png', '2026-11-05 20:31:12'),
('hero_image_3', 'assets/hero-calefator-ambiente-v2.png', '2026-11-06 08:59:13'),
('login_background_image', 'assets/soldador-metalcom-sem-filtro.png', '2026-11-05 19:53:04'),
('products_description', 'Desenvolvemos e fabricamos produtos metálicos com foco em durabilidade, funcionalidade e design. Conheça nossas principais linhas:', '2026-10-14 16:41:53'),
('products_eyebrow', 'Nossos produtos', '2026-10-14 16:41:53'),
('products_title', 'Soluções robustas para quem exige mais.', '2026-10-14 16:41:53'),
('projects_description', 'Desenvolvemos projetos personalizados de acordo com a sua necessidade. Fale com nossa equipe e transforme sua ideia em realidade.', '2026-10-14 16:41:53'),
('projects_eyebrow', 'Projetos especiais', '2026-10-14 16:41:53'),
('projects_title', 'Tem uma ideia? A gente tira do papel.', '2026-10-14 16:41:53');

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_path`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
('1', 'Reboques', 'reboques', 'Modelos para transporte de animais, cargas e usos diversos.', 'assets/produtos/recortes/categoria-reboques-sem-fundo.png', '10', '1', '2026-10-14 16:41:53', '2026-11-06 08:40:35'),
('2', 'Fogões a Lenha', 'fogoes', 'Eficiência, beleza e conforto para o seu dia a dia.', 'assets/produtos/recortes/categoria-fogoes-sem-fundo.png', '20', '1', '2026-10-14 16:41:53', '2026-11-06 08:40:35'),
('3', 'Linha Gourmet', 'acessorios', 'Design moderno e resistência para seus melhores momentos.', 'assets/produtos/recortes/categoria-gourmet-sem-fundo.png', '40', '1', '2026-10-14 16:41:53', '2026-11-05 20:31:55'),
('4', 'Calefatores', 'calefatores', 'Mais calor, mais economia e mais conforto.', 'assets/produtos/recortes/categoria-calefatores-sem-fundo.png', '60', '1', '2026-10-14 16:41:53', '2026-11-06 08:46:44'),
('5', 'Fornos', 'fornos', 'Alto desempenho e acabamento de primeira linha.', 'assets/produtos/recortes/categoria-fornos-sem-fundo.png', '30', '1', '2026-10-14 16:41:53', '2026-11-06 08:40:35'),
('6', 'Lareiras', 'lareiras', 'Conforto térmico com personalidade e segurança.', 'assets/produtos/recortes/categoria-lareiras-sem-fundo.png', '50', '1', '2026-10-14 16:41:53', '2026-11-05 19:33:54');

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `code`, `image_path`, `sort_order`, `active`, `view_count`, `created_at`, `updated_at`) VALUES
('1', '5', 'Forno Maria-Fumaça Tradicional Inox Interno 304', 'forno-maria-fumaca-tradicional', 'Forno Maria-Fumaça com inox interno. Acompanha grade e forma em inox. Medidas: 600 mm de largura por 900 mm de profundidade.', '555G4S', 'assets/produtos/Forno Maria-Fumaça Tradicional c Inox Interno 304.png', '10', '1', '1', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('2', '5', 'Forno Maria-Fumaça 100% Inox 90 cm', 'forno-maria-fumaca-inox', 'Forno totalmente produzido em inox 304, com 90 cm de profundidade. Acompanha grade e forma.', '', 'uploads/5b436a7f090b39b44d67bdefbe3dff2b.jpg', '20', '1', '0', '2026-10-14 16:41:53', '2026-11-05 18:55:25'),
('3', '5', 'Forno para Embutir Tradicional com Inox Interno 304', 'forno-embutir-tradicional', 'Forno tradicional para embutir com acabamento interno em inox 304.', '555E6B', 'assets/produtos/Forno p Embutir Tradicional c Inox interno 304.png', '30', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('4', '2', 'Fogão N1 Chapa de Vidro', 'fogao-n1-chapa-vidro', 'Fogão N1 com chapa de vidro, apresentado em diferentes ângulos e detalhes de acabamento.', '', 'assets/acervo/fogao-chapa-vidro/ambiente.jpg', '10', '1', '0', '2026-10-14 16:41:53', '2026-11-06 08:07:16'),
('5', '2', 'Fogão N2 Chapa de Vidro', 'fogao-n2-chapa-vidro', 'Fogão N2 com chapa de vidro.', NULL, 'assets/produtos/Fogão N2 Chapa de Vidro.png', '20', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('6', '2', 'Fogão Nº 1 Chapa de Vidro', 'fogao-numero-1-chapa-vidro', 'Fogão Nº 1 com chapa de vidro.', NULL, 'assets/produtos/Fogão N°1 Chapa de Vidro.png', '30', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('7', '2', 'Fogão Nº 2 Chapa de Vidro', 'fogao-numero-2-chapa-vidro', 'Fogão Nº 2 com chapa de vidro.', NULL, 'assets/produtos/Fogão N°2 Chapa de Vidro.png', '40', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('8', '2', 'Fogão em Chapa de Vidro Cerâmica N1 com Inox Interno', 'fogao-ceramica-n1-inox', 'Fogão N1 com chapa de vidro cerâmica e acabamento interno em inox.', '555U9N', 'assets/produtos/Fogão Em Chapa De Vidro Cerâmica N1 c Inox Interno.png', '50', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('9', '4', 'Calefator Vermont', 'calefator-vermont', 'Capacidade para aquecer ambientes de até 100 m³.', NULL, 'assets/produtos/Calefator Vermont.png', '10', '1', '4', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('10', '4', 'Calefator com Chapa de Vidro', 'calefator-chapa-vidro', 'Capacidade para aquecer ambientes de até 250 m³. Disponível com opção de chapa de vidro ou inox.', NULL, 'assets/produtos/Calefator com Chapa de Vidro.png', '20', '1', '2', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('11', '4', 'Calefator Versatti para Canto Aço Fire', 'calefator-versatti-canto', 'Calefator Versatti desenvolvido para instalação em canto.', NULL, 'assets/produtos/Calefator Versatti para canto Aço Fire.png', '30', '1', '1', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('12', '6', 'Lareira de Canto Tradicional', 'lareira-canto-tradicional', 'Lareira tradicional desenvolvida para instalação em canto.', '554A6K', 'assets/produtos/Lareira De Canto Tradicional.png', '10', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('13', '1', 'Reboque para Cavalo, 2 Eixos com Banheiro', 'reboque-cavalo-banheiro', 'Reboque para transporte de cavalos, com dois eixos, preparação para banheiro e frente arredondada. Entregue homologado e pronto para uso em rodovias.', NULL, 'assets/produtos/Reboque para Cavalo, 2 eixos c Banheiro.png', '10', '1', '0', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('14', '1', 'Reboque para Cavalo, 2 Eixos', 'reboque-cavalo-dois-eixos', 'Reboque para transporte de cavalos com dois eixos, frente arredondada e fechamentos em compensado naval e ACM.', NULL, 'assets/produtos/Reboque para Cavalo, 2 eixos.png', '20', '1', '1', '2026-10-14 16:41:53', '2026-10-14 16:41:53'),
('15', '1', 'Reboque para 3 Motos Metalcom', 'reboque-tres-motos', 'Reboque Metalcom desenvolvido para o transporte de até três motos.', '', 'assets/produtos/Reboque para 03 Motos Metalcom.png', '30', '1', '0', '2026-10-14 16:41:53', '2026-11-06 08:21:07'),
('16', '3', 'Kit para Fogão Campeiro', 'kit-fogao-campeiro', 'Conjunto para fogão campeiro com porta em ferro fundido, pegador térmico, gaveta de cinzeiro e grade para lenha.', NULL, 'assets/produtos/kit-fogao-campeiro.png', '10', '0', '0', '2026-10-14 16:41:53', '2026-11-06 08:21:00'),
('18', '2', 'Foggão a Lenha com Chapa de Ferro', 'fogao-chapa-ferro', 'FogÒo a lenha tradicional com chapa de ferro, estrutura robusta e fabricaþÒo Metalcom.', '', 'assets/acervo/fogao-chapa-ferro/principal.jpg', '60', '1', '0', '2026-10-14 17:12:09', '2026-10-14 20:05:37'),
('19', '3', 'Churrasqueira Gourmet', 'churrasqueira-gourmet', 'Churrasqueira metßlica para ßreas gourmet, com acabamento resistente e soluþ§es sob medida.', NULL, 'assets/acervo/churrasqueira/principal.jpg', '20', '1', '0', '2026-10-14 17:12:09', '2026-10-14 17:12:09'),
('20', '1', 'Reboque para Barco', 'reboque-barco', 'Reboque desenvolvido para transportar embarcaþ§es com seguranþa, estabilidade e praticidade.', NULL, 'assets/acervo/reboque-barco/principal.jpeg', '40', '1', '0', '2026-10-14 17:12:09', '2026-10-14 17:12:09'),
('21', '1', 'Reboque Plataforma para Carro', 'reboque-carro', 'Plataforma rodovißria para transporte de veÝculos, produzida com estrutura metßlica reforþada.', NULL, 'assets/acervo/reboque-carro/principal.jpg', '50', '1', '0', '2026-10-14 17:12:09', '2026-10-14 17:12:09'),
('22', '1', 'Reboque Fazendinha', 'reboque-fazendinha', 'Reboque versßtil para o trabalho rural e transporte de cargas, com fabricaþÒo personalizada.', NULL, 'assets/acervo/reboque-fazendinha/principal.jpg', '60', '1', '0', '2026-10-14 17:12:09', '2026-10-14 17:12:09'),
('23', '1', 'Reboque Fechado', 'reboque-fechado', 'Reboque fechado para transporte protegido de cargas e equipamentos.', NULL, 'assets/acervo/reboque-fechado/principal.jpg', '70', '1', '0', '2026-10-14 17:12:09', '2026-10-14 17:12:09'),
('24', '1', 'Trailer Metalcom', 'trailer-metalcom', 'Trailer metßlico com projeto funcional e fabricaþÒo sob medida para diferentes aplicaþ§es.', NULL, 'assets/acervo/trailer/principal.jpg', '80', '1', '0', '2026-10-14 17:12:09', '2026-10-14 17:12:09');

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `sort_order`, `featured`, `created_at`) VALUES
('1', '18', 'assets/acervo/fogao-chapa-ferro/principal.jpg', 'FogÒo a lenha com chapa de ferro', '10', '1', '2026-10-14 17:12:09'),
('2', '18', 'assets/acervo/fogao-chapa-ferro/detalhe.jpg', 'Detalhe do fogÒo com chapa de ferro', '20', '0', '2026-10-14 17:12:09'),
('3', '18', 'assets/acervo/fogao-chapa-ferro/lateral.jpg', 'Vista lateral do fogÒo com chapa de ferro', '30', '0', '2026-10-14 17:12:09'),
('4', '4', 'assets/acervo/fogao-chapa-vidro/principal.png', 'FogÒo Metalcom com chapa de vidro', '10', '1', '2026-10-14 17:12:09'),
('6', '4', 'assets/acervo/fogao-chapa-vidro/aquecido.jpg', 'FogÒo a lenha em ambiente aquecido', '30', '0', '2026-10-14 17:12:09'),
('7', '19', 'assets/acervo/churrasqueira/principal.jpg', 'Churrasqueira Gourmet Metalcom', '10', '1', '2026-10-14 17:12:09'),
('8', '19', 'assets/acervo/churrasqueira/ambiente.jpg', 'Churrasqueira em espaþo gourmet', '20', '0', '2026-10-14 17:12:09'),
('9', '19', 'assets/acervo/churrasqueira/detalhe.jpg', 'Detalhe da churrasqueira Metalcom', '30', '0', '2026-10-14 17:12:09'),
('10', '1', 'assets/acervo/fornos/principal.png', 'Forno Maria-Fumaþa Metalcom', '10', '1', '2026-10-14 17:12:09'),
('11', '1', 'assets/acervo/fornos/instalado.jpg', 'Forno Metalcom instalado', '20', '0', '2026-10-14 17:12:09'),
('12', '1', 'assets/acervo/fornos/detalhe.jpg', 'Detalhe do forno Metalcom', '30', '0', '2026-10-14 17:12:09'),
('13', '12', 'assets/acervo/lareiras/principal.jpeg', 'Lareira Metalcom em ambiente interno', '10', '1', '2026-10-14 17:12:09'),
('14', '12', 'assets/acervo/lareiras/acesa.jpg', 'Lareira Metalcom acesa', '20', '0', '2026-10-14 17:12:09'),
('15', '12', 'assets/acervo/lareiras/ambiente.jpg', 'Lareira em ambiente residencial', '30', '0', '2026-10-14 17:12:09'),
('16', '20', 'assets/acervo/reboque-barco/principal.jpeg', 'Reboque Metalcom para barco', '10', '1', '2026-10-14 17:12:09'),
('17', '20', 'assets/acervo/reboque-barco/lateral.jpeg', 'Vista lateral do reboque para barco', '20', '0', '2026-10-14 17:12:09'),
('18', '20', 'assets/acervo/reboque-barco/modelo.jpg', 'Modelo de reboque para barco', '30', '0', '2026-10-14 17:12:09'),
('19', '21', 'assets/acervo/reboque-carro/principal.jpg', 'Reboque plataforma para carro', '10', '1', '2026-10-14 17:12:09'),
('20', '21', 'assets/acervo/reboque-carro/lateral.jpg', 'Vista lateral da plataforma para carro', '20', '0', '2026-10-14 17:12:09'),
('21', '21', 'assets/acervo/reboque-carro/rampa.jpg', 'Rampa do reboque plataforma', '30', '0', '2026-10-14 17:12:09'),
('22', '13', 'assets/acervo/reboque-cavalo/principal.png', 'Reboque moderno para cavalos', '10', '1', '2026-10-14 17:12:09'),
('23', '13', 'assets/acervo/reboque-cavalo/frente.jpeg', 'Frente do reboque para cavalos', '20', '0', '2026-10-14 17:12:09'),
('24', '13', 'assets/acervo/reboque-cavalo/lateral.jpeg', 'Vista lateral do reboque para cavalos', '30', '0', '2026-10-14 17:12:09'),
('25', '13', 'assets/acervo/reboque-cavalo/aberto.jpeg', 'Reboque para cavalos com acesso aberto', '40', '0', '2026-10-14 17:12:09'),
('26', '22', 'assets/acervo/reboque-fazendinha/principal.jpg', 'Reboque fazendinha Metalcom', '10', '1', '2026-10-14 17:12:09'),
('27', '22', 'assets/acervo/reboque-fazendinha/modelo.jpg', 'Modelo de reboque para trabalho rural', '20', '0', '2026-10-14 17:12:09'),
('28', '22', 'assets/acervo/reboque-fazendinha/lateral.jpg', 'Vista lateral do reboque fazendinha', '30', '0', '2026-10-14 17:12:09'),
('29', '23', 'assets/acervo/reboque-fechado/principal.jpg', 'Reboque fechado Metalcom', '10', '1', '2026-10-14 17:12:09'),
('30', '23', 'assets/acervo/reboque-fechado/lateral.jpg', 'Vista lateral do reboque fechado', '20', '0', '2026-10-14 17:12:09'),
('31', '15', 'assets/acervo/reboque-moto/principal.jpg', 'Reboque Metalcom para motos', '20', '1', '2026-10-14 17:12:09'),
('32', '15', 'assets/acervo/reboque-moto/modelo.jpg', 'Modelo de reboque para motos', '30', '0', '2026-10-14 17:12:09'),
('33', '15', 'assets/acervo/reboque-moto/detalhe.jpg', 'Detalhe do reboque para motos', '10', '0', '2026-10-14 17:12:09'),
('34', '24', 'assets/acervo/trailer/principal.jpg', 'Trailer Metalcom', '10', '1', '2026-10-14 17:12:09'),
('35', '24', 'assets/acervo/trailer/lateral.jpg', 'Vista lateral do trailer Metalcom', '20', '0', '2026-10-14 17:12:09'),
('36', '24', 'assets/acervo/trailer/traseira.jpg', 'Vista traseira do trailer Metalcom', '30', '0', '2026-10-14 17:12:09'),
('37', '24', 'assets/acervo/trailer/detalhe.jpg', 'Detalhe do trailer Metalcom', '40', '0', '2026-10-14 17:12:09');

INSERT INTO `special_projects` (`id`, `title`, `image_path`, `alt_text`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
('1', 'Projeto rural', 'uploads/87de7d882842069064f6348a7c4a4b88.jpg', 'Projeto especial para uso rural', '40', '1', '2026-10-14 16:41:53', '2026-11-05 19:52:23'),
('2', 'Espaço gourmet', 'uploads/8993df03a7bf68d19dd30ba27c690f03.jpg', 'Projeto especial para espaço gourmet', '20', '1', '2026-10-14 16:41:53', '2026-11-05 19:52:47'),
('3', 'Aquecimento', 'uploads/3bf7d38392977bd525200be88fb463b5.jpg', 'Projeto especial de aquecimento', '10', '1', '2026-10-14 16:41:53', '2026-11-05 19:52:47'),
('4', 'Reboque personalizado', 'assets/acervo/churrasqueira/ambiente.jpg', 'Projeto especial de reboque', '30', '1', '2026-10-14 16:41:53', '2026-11-06 08:07:16');

COMMIT;

SET time_zone = @METALCOM_OLD_TIME_ZONE;
SET SQL_MODE = @METALCOM_OLD_SQL_MODE;
