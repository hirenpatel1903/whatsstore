-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2022 at 09:05 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `codecanyon_whatsstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_payment_settings`
--

CREATE TABLE `admin_payment_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` double(8,2) NOT NULL DEFAULT 0.00,
  `limit` int(11) NOT NULL DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `products_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `from`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Order Created', NULL, 1, '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(2, 'Status Change', NULL, 1, '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(3, 'Order Created For Owner', NULL, 1, '2022-06-14 00:18:48', '2022-06-14 00:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `email_template_langs`
--

CREATE TABLE `email_template_langs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `lang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_template_langs`
--

INSERT INTO `email_template_langs` (`id`, `parent_id`, `lang`, `subject`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 'ar', 'Order Complete', '<p>مرحبا ،</p><p>مرحبا بك في {app_name}.</p><p>مرحبا ، {order_name} ، شكرا للتسوق</p><p>قمنا باستلام طلب الشراء الخاص بك ، سيتم الاتصال بك قريبا !</p><p>شكرا ،</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(2, 1, 'da', 'Order Complete', '<p>Hej, &nbsp;</p><p>Velkommen til {app_name}.</p><p>Hej, {order_name}, tak for at Shopping</p><p>Vi har modtaget din købsanmodning.</p><p>Tak,</p><p>{app_navn}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(3, 1, 'de', 'Order Complete', '<p>Hello, &nbsp;</p><p>Willkommen bei {app_name}.</p><p>Hi, {order_name}, Vielen Dank für Shopping</p><p>Wir haben Ihre Kaufanforderung erhalten, wir werden in Kürze in Kontakt sein!</p><p>Danke,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(4, 1, 'en', 'Order Complete', '<p>Hello,&nbsp;</p><p>Welcome to {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>We received your purchase request, we\'ll be in touch shortly!</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(5, 1, 'es', 'Order Complete', '<p>Hola, &nbsp;</p><p>Bienvenido a {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>Recibimos su solicitud de compra, ¡estaremos en contacto en breve!</p><p>Gracias,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(6, 1, 'fr', 'Order Complete', '<p>Bonjour, &nbsp;</p><p>Bienvenue dans {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>We reçus your purchase request, we \'ll be in touch incess!</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(7, 1, 'it', 'Order Complete', '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Ciao, {order_name}, Grazie per Shopping</p><p>Abbiamo ricevuto la tua richiesta di acquisto, noi \\ saremo in contatto a breve!</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(8, 1, 'ja', 'Order Complete', '<p>こんにちは &nbsp;</p><p>{app_name}へようこそ。</p></p><p><p>こんにちは、 {order_name}、お客様の購買要求書をお受け取りいただき、すぐにご連絡いたします。</p><p>ありがとうございます。</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(9, 1, 'nl', 'Order Complete', '<p>Hallo, &nbsp;</p><p>Welkom bij {app_name}.</p><p>Hallo, {order_name}, Dank u voor Winkelen</p><p>We hebben uw aankoopaanvraag ontvangen, we zijn binnenkort in contact!</p><p>Bedankt,</p><p>{ app_name }</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(10, 1, 'pl', 'Order Complete', '<p>Hello, &nbsp;</p><p>Witamy w aplikacji {app_name}.</p><p>Hi, {order_name}, Dziękujemy za zakupy</p><p>Otrzymamy Twój wniosek o zakup, wkrótce będziemy w kontakcie!</p><p>Dzięki,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(11, 1, 'ru', 'Order Complete', '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Hi, {order_name}, Спасибо за Шоппинг</p><p>Мы получили ваш запрос на покупку, мы \\ скоро свяжемся!</p><p>Спасибо,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(12, 1, 'pt', 'Order Complete', '<p>Olá,&nbsp;</p><p><span style=\"font-size: 1rem;\">Bem-vindo a {app_name}.</span></p><p><span style=\"font-size: 1rem;\">Oi, {order_name}, Obrigado por Shopping</span></p><p><span style=\"font-size: 1rem;\">Recebemos o seu pedido de compra, nós estaremos em contato em breve!</span><br></p><p><span style=\"font-size: 1rem;\">Obrigado,</span></p><p><span style=\"font-size: 1rem;\">{app_name}</span></p><p><span style=\"font-size: 1rem;\">{order_url}</span><br></p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(13, 2, 'ar', 'Order Status', '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Ваш заказ-{order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(14, 2, 'da', 'Order Status', '<p>Hej, &nbsp;</p><p>Velkommen til {app_name}.</p><p>Din ordre er {order_status}!</p><p>Hej {order_navn}, Tak for at Shopping</p><p>Tak,</p><p>{app_navn}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(15, 2, 'de', 'Order Status', '<p>Hello, &nbsp;</p><p>Willkommen bei {app_name}.</p><p>Ihre Bestellung lautet {order_status}!</p><p>Hi {order_name}, Danke für Shopping</p><p>Danke,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(16, 2, 'en', 'Order Status', '<p>Hello,&nbsp;</p><p>Welcome to {app_name}.</p><p>Your Order is {order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(17, 2, 'es', 'Order Status', '<p>Hola, &nbsp;</p><p>Bienvenido a {app_name}.</p><p>Your Order is {order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(18, 2, 'fr', 'Order Status', '<p>Bonjour, &nbsp;</p><p>Bienvenue dans {app_name}.</p><p>Votre commande est {order_status} !</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(19, 2, 'it', 'Order Status', '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Il tuo ordine è {order_status}!</p><p>Ciao {order_name}, Grazie per Shopping</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(20, 2, 'ja', 'Order Status', '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Il tuo ordine è {order_status}!</p><p>Ciao {order_name}, Grazie per Shopping</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(21, 2, 'nl', 'Order Status', '<p>Hallo, &nbsp;</p><p>Welkom bij {app_name}.</p><p>Uw bestelling is {order_status}!</p><p>Hi {order_name}, Dank u voor Winkelen</p><p>Bedankt,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(22, 2, 'pl', 'Order Status', '<p>Hello, &nbsp;</p><p>Witamy w aplikacji {app_name}.</p><p>Twoje zamówienie to {order_status}!</p><p>Hi {order_name}, Dziękujemy za zakupy</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(23, 2, 'ru', 'Order Status', '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Ваш заказ-{order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(24, 2, 'pt', 'Order Status', '<p>Olá,&nbsp;</p><p><span style=\"font-size: 1rem;\">Bem-vindo a {app_name}.</span></p><p><span style=\"font-size: 1rem;\">Sua Ordem é {order_status}!</span><br></p><p><span style=\"font-size: 1rem;\">Oi {order_name}, Obrigado por Shopping</span><br></p><p><span style=\"font-size: 1rem;\">Obrigado,</span><br></p><p><span style=\"font-size: 1rem;\">{app_name}</span><br></p><p><span style=\"font-size: 1rem;\">{order_url}</span><br></p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(25, 3, 'ar', 'Order Complete', '<p>&lt;p&gt;مرحبا ،&lt;/p&gt;&lt;p&gt;عزيزي { wowner_name }.&lt;/p&gt;&lt;p&gt;هذا هو التأكيد لأمر التأكيد { order_id } في&lt;span style=\"font-size: 1rem;\"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;شكرا&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>', '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(26, 3, 'da', 'Order Complete', '<p>&lt;p&gt;Velkommen,&lt;/p&gt;&lt;p&gt;Kære { owner_name }.&lt;/p&gt;&lt;p&gt;Dette er bekræftelsen af bekræftelsen af bekræftelseskommandoen { order_id } i&lt;span style=\"font-size: 1rem;\"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Tak,&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(27, 3, 'de', 'Order Complete', '<p>&lt;p&gt;Willkommen,&lt;/p&gt;&lt;p&gt;Liebe {owner_name}.&lt;/p&gt;&lt;p&gt;Dies ist die Bestätigung des Bestätigungsbefehls {order_id} in&lt;span style=\"font-size: 1rem;\"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Danke,&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(28, 3, 'en', 'Order Complete', '<p>Hello,&nbsp;</p><p>Dear {owner_name}.</p><p>This is Confirmation Order {order_id} place on&nbsp;<span style=\"font-size: 1rem;\">{order_date}.</span></p><p>Thanks,</p><p>{order_url}</p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(29, 3, 'es', 'Order Complete', '<p>&lt;p&gt;Bienvenido,&lt;/p&gt;&lt;p&gt;Estimado {owner_name}.&lt;/p&gt;&lt;p&gt;Esta es la confirmación del mandato de confirmación {order_id} en&lt;span style=\"font-size: 1rem;\"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Gracias,&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(30, 3, 'fr', 'Order Complete', '<p>&lt;p&gt;Bienvenue,&lt;/p&gt;&lt;p&gt;Cher { owner_name }.&lt;/p&gt;&lt;p&gt;Voici la confirmation de la commande de confirmation { order_id } dans&lt;span style=\"font-size: 1rem;\"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Merci,&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(31, 3, 'it', 'Order Complete', '<p>&lt;p&gt;Benvenuti,&lt;/p&gt;&lt;p&gt;Caro {owner_name}.&lt;/p&gt;&lt;p&gt;Questa è la conferma del comando di conferma {order_id} in&lt;span style=\"font-size: 1rem;\"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Grazie,&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(32, 3, 'ja', 'Order Complete', '<p>&lt;p&gt;ようこそ&lt;/p&gt;&lt;p&gt;Dear {owner_name}。&lt;/p&gt;&lt;p&gt;これは、&lt;span style=\"font-size:1rem;\"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;{order_url}&lt;/span&gt;の確認コマンド {order_id} の確認です。&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(33, 3, 'nl', 'Order Complete', '<p>&lt;p&gt;Welkom,&lt;/p&gt;&lt;p&gt;Beste { owner_name }.&lt;/p&gt;&lt;p&gt;Dit is de bevestiging van de bevestigingsopdracht { order_id } in&lt;span style=\"font-size: 1rem;\"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Bedankt,&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(34, 3, 'pl', 'Order Complete', '<p>&lt;p&gt;Witamy,&lt;/p&gt;&lt;p&gt;Szanowny Panie {owner_name }.&lt;/p&gt;&lt;p&gt;To jest potwierdzenie komendy potwierdzenia {order_id } w&lt;span style=\"font-size: 1rem;\"&gt;{order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Thanks,&lt;/p&gt;&lt;p&gt;{order_url }&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(35, 3, 'ru', 'Order Complete', '<p>&lt;p&gt;Добро пожаловать,&lt;/p&gt;&lt;p&gt;Уважаемый { owner_name }.&lt;/p&gt;&lt;p&gt;Это подтверждение команды подтверждения { order_id } в&lt;span style=\"font-size: 1rem;\"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Спасибо,&lt;/p&gt;&lt;p&gt;{ urder_url }&lt;/p&gt;<br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(36, 3, 'pt', 'Order Complete', '<p>Olá,&nbsp;</p><p><span style=\"font-size: 1rem;\">Querido {owner_name}.</span><br></p><p><span style=\"font-size: 1rem;\">Este é o Confirmação Order {order_id} place on {order_date}.</span><br></p><p><span style=\"font-size: 1rem;\">Obrigado,</span><br></p><p><span style=\"font-size: 1rem;\">{order_url}</span><br></p>', '2022-06-14 00:18:49', '2022-06-14 00:18:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_16_144239_create_plans_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_09_28_102009_create_settings_table', 1),
(6, '2020_04_12_095629_create_coupons_table', 1),
(7, '2020_04_12_120749_create_user_coupons_table', 1),
(8, '2020_05_02_075614_create_email_templates_table', 1),
(9, '2020_05_02_075630_create_email_template_langs_table', 1),
(10, '2020_05_02_075647_create_user_email_templates_table', 1),
(11, '2020_05_21_065337_create_permission_tables', 1),
(12, '2021_02_02_085506_create_stores_table', 1),
(13, '2021_02_02_094240_create_user_stores_table', 1),
(14, '2021_02_03_093659_create_product_categories_table', 1),
(15, '2021_02_03_110342_create_product_taxes_table', 1),
(16, '2021_02_03_112228_create_shippings_table', 1),
(17, '2021_02_04_034943_create_products_table', 1),
(18, '2021_02_08_063716_create_product_images_table', 1),
(19, '2021_02_13_053126_create_orders_table', 1),
(20, '2021_02_15_071203_create_user_details_table', 1),
(21, '2021_02_26_061007_create_visits_table', 1),
(22, '2021_03_04_110817_create_plan_orders_table', 1),
(23, '2021_03_23_094310_create_product_variant_options_table', 1),
(24, '2021_04_03_063418_create_locations_table', 1),
(25, '2021_04_10_034521_create_product_coupons_table', 1),
(26, '2021_06_03_101323_create_admin_payment_settings', 1),
(27, '2021_06_25_041037_create_custom_massage_table', 1),
(28, '2021_08_24_035614_create_sessions_table', 1),
(29, '2021_08_25_051549_create_add_new_field_table', 1),
(30, '2021_08_28_044714_create_add_details_field_table', 1),
(31, '2021_09_04_051718_create_add_store_custom_field_table', 1),
(32, '2021_10_07_053759_create_addnew_field_product__table', 1),
(33, '2021_11_18_051204_create_plan_requests_table', 1),
(34, '2021_12_25_052802_add_requested_plan_to_users_table', 1),
(35, '2021_12_25_053246_add_meta_data_to_stores_table', 1),
(36, '2022_01_17_081407_create__customers_table', 1),
(37, '2022_01_17_082025_add_field_in_order_table', 1),
(38, '2022_01_17_082236_create_purchased_products_table', 1),
(39, '2022_01_18_072109_create_store_payment_settings_table', 1),
(40, '2022_03_11_035756_add_twilio_to_stores_table', 1),
(41, '2022_03_29_085523_add_is_store_enable_to_stores_table', 1),
(42, '2022_03_30_035029_add_decimal_number_to_stores_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_month` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_year` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_address_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `price` double(8,2) DEFAULT NULL,
  `coupon` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_json` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_data` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `txn_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `subscription_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_frequency` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double(8,2) DEFAULT NULL,
  `duration` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_stores` int(11) NOT NULL DEFAULT 0,
  `max_products` int(11) NOT NULL DEFAULT 0,
  `enable_custdomain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `enable_custsubdomain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `shipping_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `price`, `duration`, `max_stores`, `max_products`, `enable_custdomain`, `enable_custsubdomain`, `shipping_method`, `image`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Free Plan', 0.00, 'Unlimited', 2, 5, 'on', 'on', 'on', 'free_plan.png', 'For companies that need a robust full-featured time tracker.', '2022-06-14 00:18:49', '2022-06-14 00:18:49');

-- --------------------------------------------------------

--
-- Table structure for table `plan_orders`
--

CREATE TABLE `plan_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_number` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_month` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_year` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_id` int(11) NOT NULL,
  `price` double(8,2) DEFAULT NULL,
  `price_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `txn_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_requests`
--

CREATE TABLE `plan_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `duration` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_categorie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double(8,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `SKU` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_tax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_value_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_value_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_value_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_value_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_display` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `downloadable_prodcut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enable_product_variant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `variants_json` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_coupons`
--

CREATE TABLE `product_coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enable_flat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'off',
  `discount` double(8,2) NOT NULL DEFAULT 0.00,
  `flat_discount` double(8,2) DEFAULT 0.00,
  `limit` int(11) NOT NULL DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_images` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_taxes`
--

CREATE TABLE `product_taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` double(8,2) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_options`
--

CREATE TABLE `product_variant_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double(25,2) DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchased_products`
--

CREATE TABLE `purchased_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shippings`
--

CREATE TABLE `shippings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT 0,
  `shipping_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domains` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enable_storelink` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enable_subdomain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subdomain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enable_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_variable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `storejs` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `currency_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `currency_symbol_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'pre',
  `currency_symbol_space` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'without',
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `youtube` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `google_analytic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_pixel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decimal_number` int(11) NOT NULL DEFAULT 2,
  `enable_shipping` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'on',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_twilio_enabled` varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twilio_sid` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twilio_token` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twilio_from` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_number` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_stripe_enabled` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `stripe_key` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_secret` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_paypal_enabled` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `paypal_mode` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_client_id` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_secret_key` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_driver` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_host` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_port` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_username` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_password` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_encryption` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_from_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_from_name` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_store_enabled` int(11) NOT NULL DEFAULT 1,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `enable_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enable_telegram` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `telegrambot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegramchatid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `name`, `email`, `store_theme`, `domains`, `enable_storelink`, `enable_subdomain`, `subdomain`, `enable_domain`, `content`, `item_variable`, `about`, `tagline`, `slug`, `lang`, `storejs`, `currency`, `currency_code`, `currency_symbol_position`, `currency_symbol_space`, `whatsapp`, `facebook`, `instagram`, `twitter`, `youtube`, `google_analytic`, `facebook_pixel`, `meta_data`, `footer_note`, `meta_description`, `decimal_number`, `enable_shipping`, `address`, `city`, `state`, `zipcode`, `country`, `logo`, `invoice_logo`, `is_twilio_enabled`, `twilio_sid`, `twilio_token`, `twilio_from`, `notification_number`, `is_stripe_enabled`, `stripe_key`, `stripe_secret`, `is_paypal_enabled`, `paypal_mode`, `paypal_client_id`, `paypal_secret_key`, `mail_driver`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`, `mail_from_address`, `mail_from_name`, `is_store_enabled`, `is_active`, `created_by`, `enable_whatsapp`, `whatsapp_number`, `enable_telegram`, `telegrambot`, `telegramchatid`, `custom_field_title_1`, `custom_field_title_2`, `custom_field_title_3`, `custom_field_title_4`, `created_at`, `updated_at`) VALUES
(1, 'My WhatsStore', 'owner@example.com', 'style-grey-body.css', NULL, 'on', NULL, NULL, 'off', 'Hi,\nWelcome to {store_name},\nYour order is confirmed & your order no. is {order_no}\nYour order detail is:\nName : {customer_name}\nAddress : {billing_address} , {shipping_address}\n~~~~~~~~~~~~~~~~\n{item_variable}\n~~~~~~~~~~~~~~~~\nQty Total : {qty_total}\nSub Total : {sub_total}\nDiscount Price : {discount_amount}\nShipping Price : {shipping_amount}\nTax : {item_tax}\nTotal : {item_total}\n~~~~~~~~~~~~~~~~~~\nTo collect the order you need to show the receipt at the counter.\nThanks {store_name}', '{sku} : {quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}', NULL, 'WhatsStore', 'my-whatsstore', 'en', NULL, '$', 'USD', 'pre', 'without', '#', '#', '#', '#', '#', NULL, NULL, NULL, '© 2020 WhatsStore. All rights reserved', NULL, 2, 'on', 'india', NULL, NULL, NULL, NULL, 'logo.png', NULL, NULL, NULL, NULL, NULL, NULL, 'off', NULL, NULL, 'off', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 'off', NULL, 'off', NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-14 00:18:48', '2022-06-14 00:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `store_payment_settings`
--

CREATE TABLE `store_payment_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_store` int(11) DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `plan` int(11) NOT NULL DEFAULT 1,
  `requested_plan` int(11) NOT NULL DEFAULT 0,
  `plan_expire_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'light',
  `plan_is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `lang`, `current_store`, `avatar`, `type`, `plan`, `requested_plan`, `plan_expire_date`, `created_by`, `mode`, `plan_is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@example.com', NULL, '$2y$10$2YlWqSUBdo4jDhyegTPAB.hmu9OvvKyyrwEwiSd40TFhXtVs9MD26', NULL, 'en', NULL, NULL, 'super admin', 1, 0, NULL, 0, 'light', 1, '2022-06-14 00:18:48', '2022-06-14 00:18:48'),
(2, 'Owner', 'owner@example.com', NULL, '$2y$10$9hAnAIyxNh45v6/FE073AuTLyxfuqZV46fw8pYJqT3BHnn33sqnfi', NULL, NULL, 1, NULL, 'Owner', 1, 0, NULL, 1, 'light', 1, '2022-06-14 00:18:48', '2022-06-14 00:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_coupons`
--

CREATE TABLE `user_coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` int(11) NOT NULL,
  `coupon` int(11) NOT NULL,
  `order` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_field_title_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_field_title_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_instruct` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` int(11) NOT NULL DEFAULT 0,
  `shipping_id` int(11) NOT NULL DEFAULT 0,
  `billing_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_email_templates`
--

CREATE TABLE `user_email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_email_templates`
--

INSERT INTO `user_email_templates` (`id`, `template_id`, `user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(2, 2, 1, 1, '2022-06-14 00:18:49', '2022-06-14 00:18:49'),
(3, 3, 1, 1, '2022-06-14 00:18:49', '2022-06-14 00:18:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_stores`
--

CREATE TABLE `user_stores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `permission` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_stores`
--

INSERT INTO `user_stores` (`id`, `user_id`, `store_id`, `permission`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Owner', 1, '2022-06-14 00:18:48', '2022-06-14 00:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referer` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `useragent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headers` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visitor_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_payment_settings`
--
ALTER TABLE `admin_payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_payment_settings_name_created_by_unique` (`name`,`created_by`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template_langs`
--
ALTER TABLE `email_template_langs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plans_name_unique` (`name`);

--
-- Indexes for table `plan_orders`
--
ALTER TABLE `plan_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_orders_order_id_unique` (`order_id`);

--
-- Indexes for table `plan_requests`
--
ALTER TABLE `plan_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_coupons`
--
ALTER TABLE `product_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_taxes`
--
ALTER TABLE `product_taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_variant_options`
--
ALTER TABLE `product_variant_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchased_products`
--
ALTER TABLE `purchased_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_name_created_by_unique` (`name`,`created_by`);

--
-- Indexes for table `shippings`
--
ALTER TABLE `shippings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_payment_settings`
--
ALTER TABLE `store_payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_payment_settings_name_store_id_created_by_unique` (`name`,`store_id`,`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_email_templates`
--
ALTER TABLE `user_email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_stores`
--
ALTER TABLE `user_stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_visitable_type_visitable_id_index` (`visitable_type`,`visitable_id`),
  ADD KEY `visitor_visitor_type_visitor_id_index` (`visitor_type`,`visitor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_payment_settings`
--
ALTER TABLE `admin_payment_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `email_template_langs`
--
ALTER TABLE `email_template_langs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plan_orders`
--
ALTER TABLE `plan_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_requests`
--
ALTER TABLE `plan_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_coupons`
--
ALTER TABLE `product_coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_taxes`
--
ALTER TABLE `product_taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variant_options`
--
ALTER TABLE `product_variant_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchased_products`
--
ALTER TABLE `purchased_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shippings`
--
ALTER TABLE `shippings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_payment_settings`
--
ALTER TABLE `store_payment_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_coupons`
--
ALTER TABLE `user_coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_email_templates`
--
ALTER TABLE `user_email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_stores`
--
ALTER TABLE `user_stores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
