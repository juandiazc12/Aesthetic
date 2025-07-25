-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-07-2025 a las 22:17:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aesthectic`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachmentable`
--

CREATE TABLE `attachmentable` (
  `id` int(10) UNSIGNED NOT NULL,
  `attachmentable_type` varchar(255) NOT NULL,
  `attachmentable_id` int(10) UNSIGNED NOT NULL,
  `attachment_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

CREATE TABLE `attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `original_name` text NOT NULL,
  `mime` varchar(255) NOT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `size` bigint(20) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `path` text NOT NULL,
  `description` text DEFAULT NULL,
  `alt` text DEFAULT NULL,
  `hash` text DEFAULT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `attachments`
--

INSERT INTO `attachments` (`id`, `name`, `original_name`, `mime`, `extension`, `size`, `sort`, `path`, `description`, `alt`, `hash`, `disk`, `user_id`, `group`, `created_at`, `updated_at`) VALUES
(1, '7beaadee511eda532bf5e7240cf37faa64912ebf', '8cb2a360-6eff-4d5d-ad25-a92a361dfa69.jpg', 'image/jpeg', 'jpg', 94031, 0, '2025/07/09/', NULL, NULL, '17959ba1a46998081d77da22acd138acf8c52c41', 'public', 1, NULL, '2025-07-10 04:30:00', '2025-07-10 04:30:00'),
(2, 'c09530e77f3f7a91c11a6a2d2454eb86fbe556d9', '3RTvke5w8MK1Hzj7wWJ6G.jpg', 'image/jpeg', 'jpg', 67277, 0, '2025/07/10/', NULL, NULL, '010b1991be256f4395e82486ea892ea93493742e', 'public', 1, NULL, '2025-07-11 04:04:37', '2025-07-11 04:04:37'),
(3, '8df8d28db5c3732709d0c2f96289d02e74b541e1', 'download.jpg', 'image/jpeg', 'jpg', 92802, 0, '2025/07/10/', NULL, NULL, '6aefb545de197839b406907c01a6be7945109e33', 'public', 1, NULL, '2025-07-11 04:05:00', '2025-07-11 04:05:00'),
(4, '6b5cf2789fd3e685a3ce820b2cf0451306a8c659', 'WhatsApp Image 2025-02-05 at 6.21.56 PM.jpeg', 'image/jpeg', 'jpeg', 161736, 0, '2025/07/11/', NULL, NULL, '0942aa3db86b5d6bc2b3b202aa2ef8443a3ee434', 'public', 1, NULL, '2025-07-11 06:38:06', '2025-07-11 06:38:06'),
(5, '49f5782043cd6a4100b3a089db1a83b7d2b32790', 'Corte Clásico.jpg', 'image/jpeg', 'jpg', 40889, 0, '2025/07/11/', NULL, NULL, '16e9ef4207b84e11ae1e95704e87c83cdf6bdcb4', 'public', 1, NULL, '2025-07-11 06:38:53', '2025-07-11 06:38:53'),
(6, '4d86ca329ca786828246c8c12f3e0200d58f5d11', 'avatar.png', 'image/png', 'png', 5870, 0, '2025/07/11/', NULL, NULL, '6d9f2748a7ef84164dc66ae4c11e3166b7b6e9fd', 'public', 1, NULL, '2025-07-11 06:40:34', '2025-07-11 06:40:34'),
(7, '49f5782043cd6a4100b3a089db1a83b7d2b32790', 'Corte Clásico.jpg', 'image/jpeg', 'jpg', 40889, 0, '2025/07/11/', NULL, NULL, '16e9ef4207b84e11ae1e95704e87c83cdf6bdcb4', 'public', 1, NULL, '2025-07-12 07:31:06', '2025-07-12 07:31:06'),
(8, '7db301940221914324169bea13babee942849909', 'Microdermoabrasión Facial.jpg', 'image/jpeg', 'jpg', 374582, 0, '2025/07/12/', NULL, NULL, '22615d15e417640c76207a89777653ece126b936', 'public', 1, NULL, '2025-07-12 09:15:11', '2025-07-12 09:15:11'),
(9, '49f5782043cd6a4100b3a089db1a83b7d2b32790', 'Corte Clásico.jpg', 'image/jpeg', 'jpg', 40889, 0, '2025/07/11/', NULL, NULL, '16e9ef4207b84e11ae1e95704e87c83cdf6bdcb4', 'public', 1, NULL, '2025-07-12 09:17:38', '2025-07-12 09:17:38'),
(10, '49f5782043cd6a4100b3a089db1a83b7d2b32790', 'Corte Clásico.jpg', 'image/jpeg', 'jpg', 40889, 0, '2025/07/11/', NULL, NULL, '16e9ef4207b84e11ae1e95704e87c83cdf6bdcb4', 'public', 1, NULL, '2025-07-12 09:20:05', '2025-07-12 09:20:05'),
(11, '49f5782043cd6a4100b3a089db1a83b7d2b32790', 'Corte Clásico.jpg', 'image/jpeg', 'jpg', 40889, 0, '2025/07/11/', NULL, NULL, '16e9ef4207b84e11ae1e95704e87c83cdf6bdcb4', 'public', 1, NULL, '2025-07-12 09:23:07', '2025-07-12 09:23:07'),
(12, 'e6f65919b87175c285a8094818213d93a919c706', 'Depilación con Cera.jpg', 'image/jpeg', 'jpg', 5465, 0, '2025/07/12/', NULL, NULL, '18eb848a994fd89444cd7eca98e776319188a474', 'public', 1, NULL, '2025-07-12 10:08:49', '2025-07-12 10:08:49'),
(13, '04de48235ebcd32bbbbfe30d98d86db6f9a90464', 'Pedicure Spa.jpg', 'image/jpeg', 'jpg', 105516, 0, '2025/07/12/', NULL, NULL, '5e559dc0ddf0eb6ba871bf9e4cd93aa6f99d3a46', 'public', 1, NULL, '2025-07-12 10:10:10', '2025-07-12 10:10:10'),
(14, '545d0bb4a2a5ea558b74e980c6a91cc3de483b65', 'sonrisa-estilista-peluqueria-retrato-hombre-negro-empresario-recortador-barba-trabajador-profesional-salon-cara-persona-masculina-felicidad-pequenas-empresas-salon-belleza_590464-193499.avif', 'image/avif', 'avif', 24034, 0, '2025/07/16/', NULL, NULL, 'c77e6cf5d3331938b0634a8f3ab909c1f96f56e2', 'public', 1, NULL, '2025-07-17 02:17:18', '2025-07-17 02:17:18'),
(15, '545d0bb4a2a5ea558b74e980c6a91cc3de483b65', 'sonrisa-estilista-peluqueria-retrato-hombre-negro-empresario-recortador-barba-trabajador-profesional-salon-cara-persona-masculina-felicidad-pequenas-empresas-salon-belleza_590464-193499.avif', 'image/avif', 'avif', 24034, 0, '2025/07/16/', NULL, NULL, 'c77e6cf5d3331938b0634a8f3ab909c1f96f56e2', 'public', 1, NULL, '2025-07-17 02:17:25', '2025-07-17 02:17:25'),
(16, '4432d9eb4f5129557a842dc3c036d1dad128cc9b', 'linda-mujer-peluquero-estilista-femenina_88465-1952.avif', 'image/avif', 'avif', 17525, 0, '2025/07/16/', NULL, NULL, '9035c077898268f07b7371b0c2671678c72d3cdf', 'public', 1, NULL, '2025-07-17 02:23:47', '2025-07-17 02:23:47'),
(17, '545d0bb4a2a5ea558b74e980c6a91cc3de483b65', 'sonrisa-estilista-peluqueria-retrato-hombre-negro-empresario-recortador-barba-trabajador-profesional-salon-cara-persona-masculina-felicidad-pequenas-empresas-salon-belleza_590464-193499.avif', 'image/avif', 'avif', 24034, 0, '2025/07/16/', NULL, NULL, 'c77e6cf5d3331938b0634a8f3ab909c1f96f56e2', 'public', 1, NULL, '2025-07-17 02:24:06', '2025-07-17 02:24:06'),
(18, 'e5b08acfd6874790252038de50870009052f77d8', 'png-clipart-hairdresser-beauty-parlour-personal-stylist-hairdryer-blue-people-thumbnail.png', 'image/png', 'png', 13005, 0, '2025/07/16/', NULL, NULL, 'd25b407ab5236f57f15ea3f75c98e742887b7bf6', 'public', 1, NULL, '2025-07-17 02:26:53', '2025-07-17 02:26:53'),
(19, '27449531723c8b2cec6be634b74a14d812f7ef8f', 'uñas.png', 'image/png', 'png', 19588, 0, '2025/07/16/', NULL, NULL, '3934eb364b37a6e3d3cc2add8d619f5e58e23132', 'public', 1, NULL, '2025-07-17 02:32:39', '2025-07-17 02:32:39'),
(20, '4572737a093c74386c53a855b94f3bd2329f76fc', 'aaaaaaaaa.png', 'image/png', 'png', 15888, 0, '2025/07/16/', NULL, NULL, '06889db07a3fb103ee22935a07b657b64201986c', 'public', 1, NULL, '2025-07-17 02:34:35', '2025-07-17 02:34:35'),
(21, '5c20d8acfa2c7ea23a43d30adac3d886e6201fd1', 'Corte de Cabello Hombre Básico.png', 'image/png', 'png', 149224, 0, '2025/07/16/', NULL, NULL, '0565960f3837e4687a552c362f4954f0d5232c32', 'public', 1, NULL, '2025-07-17 02:38:16', '2025-07-17 02:38:16'),
(22, '9b1fdfdcee5c248acd269425350545c02b6f4adf', 'Corte de cabello con barba.png', 'image/png', 'png', 53180, 0, '2025/07/16/', NULL, NULL, '46476e899b6f97b247c5da9ababd23f6457a587a', 'public', 1, NULL, '2025-07-17 02:42:53', '2025-07-17 02:42:53'),
(23, 'c391efc51211c1dbdbc7906706631e63fe10ff75', 'Corte Hombre con Mascarilla.jpg', 'image/jpeg', 'jpg', 9290, 0, '2025/07/16/', NULL, NULL, '44568443437a53234fa4f7fc0ff4c935eccf6ecb', 'public', 1, NULL, '2025-07-17 02:44:10', '2025-07-17 02:44:10'),
(24, '8f967aaa39204dee661a6adaca19494387c72861', 'Corte Hombre con Mascarilla.jpg', 'image/jpeg', 'jpg', 27827, 0, '2025/07/16/', NULL, NULL, 'ee8fdc656ea1b23de26af233a6ef13639f6162ee', 'public', 1, NULL, '2025-07-17 02:45:25', '2025-07-17 02:45:25'),
(25, '19f7aadf31b3e852b3fa98bda1068005b7eded42', 'Corte Niño.jpg', 'image/jpeg', 'jpg', 73668, 0, '2025/07/16/', NULL, NULL, 'd9b94ffccc4b686c0422d3f2b4bdd6dde420d2dc', 'public', 1, NULL, '2025-07-17 02:46:18', '2025-07-17 02:46:18'),
(26, 'd5b429e3dce08ef86cfba4d372e1f22ff89ea0c5', 'Corte de Cabello Mujer Básico.jpg', 'image/jpeg', 'jpg', 58593, 0, '2025/07/16/', NULL, NULL, '8d43dca4e97653e700f978f5bce546e5fc646ca0', 'public', 1, NULL, '2025-07-17 02:47:17', '2025-07-17 02:47:17'),
(27, 'a5fd4e7abe1c52d8f9317d9dc6433dce3b3549d8', 'Corte Mujer + Secado.jpg', 'image/jpeg', 'jpg', 28406, 0, '2025/07/16/', NULL, NULL, '26d0f2ae5f3de5a009d950efd66c6d8474a063d7', 'public', 1, NULL, '2025-07-17 02:48:34', '2025-07-17 02:48:34'),
(28, 'f51f96549dc3a6f19884c38e6f41738c7f9730e3', 'Corte Mujer + Plancha.jpg', 'image/jpeg', 'jpg', 430406, 0, '2025/07/16/', NULL, NULL, '8a914989dc3349d8cee96b6f1c103280ccd341a6', 'public', 1, NULL, '2025-07-17 02:49:36', '2025-07-17 02:49:36'),
(29, '8db61ff141701cf3bdea99a37d946c8da71df6a0', 'Corte Mujer + Mascarilla Capilar.jpg', 'image/jpeg', 'jpg', 52949, 0, '2025/07/16/', NULL, NULL, '27afe13be1a019c15097efa210e1aa7a6163d82d', 'public', 1, NULL, '2025-07-17 02:50:55', '2025-07-17 02:50:55'),
(30, '122cbb9b3ede3303f8aca5e8c1510550c664f604', 'Peinado Básico.jpg', 'image/jpeg', 'jpg', 62108, 0, '2025/07/16/', NULL, NULL, '61f912844e359dff12a8c4f57ee92b6747e231c2', 'public', 1, NULL, '2025-07-17 02:52:28', '2025-07-17 02:52:28'),
(31, '153bb25e8ff7284d773a991292afdccbf0e095be', 'Peinado para Evento.png', 'image/png', 'png', 62052, 0, '2025/07/16/', NULL, NULL, '9534d96275c4760d73baa51af51a2d36b9c3790c', 'public', 1, NULL, '2025-07-17 02:53:40', '2025-07-17 02:53:40'),
(32, '27833e72642a87f68b991f94153c8c5c595bab7a', 'Manicure Tradicional.png', 'image/png', 'png', 42894, 0, '2025/07/16/', NULL, NULL, 'b45dd02f4ec5c4ef8bcf132a53c523ad7fa7b526', 'public', 1, NULL, '2025-07-17 02:56:17', '2025-07-17 02:56:17'),
(33, 'e24f9605981260262f7d4fb3b82e61c61ce9be15', 'Manicure con Diseño.jpg', 'image/jpeg', 'jpg', 51761, 0, '2025/07/16/', NULL, NULL, '103d07774cf1b1840da5f92aa147b9230acc4ea7', 'public', 1, NULL, '2025-07-17 03:01:09', '2025-07-17 03:01:09'),
(34, 'aaaebc0887ad0a564936e16f17c1fc064aa7e47d', 'Manicure Semipermanente.jpg', 'image/jpeg', 'jpg', 45750, 0, '2025/07/16/', NULL, NULL, '6fbfdeabe7de5b78342c815c5b2896af57e832d4', 'public', 1, NULL, '2025-07-17 03:03:41', '2025-07-17 03:03:41'),
(35, '9dd0e34d99aa89f6e746711b7a07d7a5950d5c75', 'Pedicure Tradicional.png', 'image/png', 'png', 120004, 0, '2025/07/16/', NULL, NULL, '09c2e4bc22534e11c7d49cb163e9bbb683d148b1', 'public', 1, NULL, '2025-07-17 03:05:01', '2025-07-17 03:05:01'),
(36, '0526ce051da5489989613c379950483410eda9ad', 'Pedicure Spa.jpg', 'image/jpeg', 'jpg', 83491, 0, '2025/07/16/', NULL, NULL, 'db73ccd1d73ebaf5bade7f013e5d0c0c8add1d82', 'public', 1, NULL, '2025-07-17 03:06:58', '2025-07-17 03:06:58'),
(37, '8d57399e3f83ba39f3b6279bb64f0b740132afbe', 'Depilación Facial.png', 'image/png', 'png', 48257, 0, '2025/07/16/', NULL, NULL, 'c45e48e49bd7b9aed955ee663257fdadbdc320ca', 'public', 1, NULL, '2025-07-17 03:08:18', '2025-07-17 03:08:18'),
(38, 'cb44718aeac33be66724750bcbaa09012fd34dc2', 'depilacion-corporal.jpg', 'image/jpeg', 'jpg', 82460, 0, '2025/07/16/', NULL, NULL, '8e0dae5e335de31763cfb9587eb72596079ea309', 'public', 1, NULL, '2025-07-17 03:09:09', '2025-07-17 03:09:09'),
(39, 'c2f4f85c2f7cc0e8c51861a5f088b3964e3d5de1', 'Limpieza Facial Básica.png', 'image/png', 'png', 435655, 0, '2025/07/16/', NULL, NULL, '2b3aba5b496fd846589e848475e0ffb5af7cfcf6', 'public', 1, NULL, '2025-07-17 03:10:35', '2025-07-17 03:10:35'),
(40, 'eb384c983d9efce2dfd3fac4e1dbb66607cf197d', 'Limpieza Facial con Exfoliación.jpg', 'image/jpeg', 'jpg', 67765, 0, '2025/07/16/', NULL, NULL, '25e83246eacbb5e5248d70618a109d1b16d00483', 'public', 1, NULL, '2025-07-17 03:11:34', '2025-07-17 03:11:34'),
(41, '2d74d031869248fe16a47c5b8e8b8d26222198c3', 'Limpieza Facial con Mascarilla de Colágeno.jpg', 'image/jpeg', 'jpg', 134849, 0, '2025/07/16/', NULL, NULL, 'a55e03ea699a5024046c0db9a51dfec5523afa68', 'public', 1, NULL, '2025-07-17 03:12:24', '2025-07-17 03:12:24'),
(42, 'c7d4476042f003088518205080efbe2d7cac833d', 'Tratamiento Capilar Hidratante.jpg', 'image/jpeg', 'jpg', 389595, 0, '2025/07/16/', NULL, NULL, 'acb718cf8f8db75593e326eae45f4894c22b5a8e', 'public', 1, NULL, '2025-07-17 03:13:34', '2025-07-17 03:13:34'),
(43, '89b100fec8386fc0de4f6a02196af77c871ff034', 'Tratamiento Anticaspa.jpg', 'image/jpeg', 'jpg', 98436, 0, '2025/07/16/', NULL, NULL, 'bdd2e9575327a585c672b9211417bb43dc2ea5e3', 'public', 1, NULL, '2025-07-17 03:14:46', '2025-07-17 03:14:46'),
(44, 'b221464c77d62ab96071124ff8a957a162f64f80', 'Tinte Completo.png', 'image/png', 'png', 172276, 0, '2025/07/16/', NULL, NULL, '6646c228801a0de0423812cb8bbcec68d5717195', 'public', 1, NULL, '2025-07-17 03:16:19', '2025-07-17 03:16:19'),
(45, '6a333fcef5f0c1e3f01262b6457e601c03e98243', 'Mechas Balayage.jpeg', 'image/jpeg', 'jpeg', 78170, 0, '2025/07/16/', NULL, NULL, 'b5c7cafcc6a395e96c0a7be2be1c6c4392f9dfa1', 'public', 1, NULL, '2025-07-17 03:17:24', '2025-07-17 03:17:24'),
(46, 'ba66b8630fd383bf3551591aa4b9dee1982703f0', 'Matizante.jpg', 'image/jpeg', 'jpg', 211321, 0, '2025/07/16/', NULL, NULL, '2d415edceb6cf6b0c89c38dd7fecb7f41b6bf53f', 'public', 1, NULL, '2025-07-17 03:18:55', '2025-07-17 03:18:55'),
(47, '0f587d99b364570b03784200b0799f01f4e714f4', 'Uñas Acrílicas.jpg', 'image/jpeg', 'jpg', 1053889, 0, '2025/07/16/', NULL, NULL, 'b7cd8d18aa75f41e65b8a3f4d452f7b757519d90', 'public', 1, NULL, '2025-07-17 03:20:54', '2025-07-17 03:20:54'),
(48, 'ab376ec592a7f012b06ee82c7ee1a1aa465f3795', 'Barbería Premium.jpg', 'image/jpeg', 'jpg', 10546, 0, '2025/07/16/', NULL, NULL, 'ee4939bf5879590fe858e8e99b2e901fce6469f6', 'public', 1, NULL, '2025-07-17 03:21:53', '2025-07-17 03:21:53'),
(49, '3b25f7233bb241717988f83cc62110a5eb7aff9d', 'Diseño de Cejas con Hilo.jpg', 'image/jpeg', 'jpg', 11399, 0, '2025/07/16/', NULL, NULL, 'a2b7f09949aa2f69c7ef37a4cdc555b36d151972', 'public', 1, NULL, '2025-07-17 03:22:56', '2025-07-17 03:22:56'),
(50, '834d985aa27729e3dd0ad7f6a146a080bab4d70a', 'Maquillaje para Evento.jpg', 'image/jpeg', 'jpg', 122311, 0, '2025/07/16/', NULL, NULL, 'd34b5dc3db4d528d70d29d2e25f5b6da0a3f08df', 'public', 1, NULL, '2025-07-17 03:23:46', '2025-07-17 03:23:46'),
(51, '28ee65ae88a75fcd867027c9880c8373d3afee03', 'Alisado Permanente.png', 'image/png', 'png', 29385, 0, '2025/07/16/', NULL, NULL, 'fa450c8a7ba50011473ff95579923c89f9cafbdd', 'public', 1, NULL, '2025-07-17 03:25:33', '2025-07-17 03:25:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `professional_id` bigint(20) UNSIGNED NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` enum('pending','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_preference_id` varchar(255) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `service_id`, `professional_id`, `scheduled_at`, `status`, `cancelled_at`, `completed_at`, `notes`, `total_amount`, `payment_preference_id`, `payment_id`, `payment_status`, `payment_details`, `payment_method`, `payment_completed_at`, `created_at`, `updated_at`) VALUES
(52, 1, 37, 4, '2025-07-22 17:00:00', 'completed', NULL, '2025-07-22 22:37:50', NULL, 150000.00, NULL, 'cash_1753218458096', 'paid', NULL, 'efectivo', '2025-07-22 21:07:41', '2025-07-22 21:07:41', '2025-07-22 22:37:50'),
(53, 1, 36, 7, '2025-07-22 17:00:00', 'completed', NULL, '2025-07-23 01:49:24', NULL, 60000.00, NULL, 'cash_1753218514960', 'paid', NULL, 'efectivo', '2025-07-22 21:08:39', '2025-07-22 21:08:39', '2025-07-23 01:49:24'),
(54, 1, 35, 6, '2025-07-22 17:00:00', 'completed', NULL, '2025-07-22 22:01:15', NULL, 12000.00, NULL, 'cash_1753218561544', 'paid', NULL, 'efectivo', '2025-07-22 21:09:25', '2025-07-22 21:09:25', '2025-07-22 22:01:15'),
(55, 1, 37, 8, '2025-07-23 07:00:00', 'cancelled', '2025-07-22 21:15:55', NULL, NULL, 150000.00, NULL, 'cash_1753218882352', 'paid', NULL, 'efectivo', '2025-07-22 21:14:45', '2025-07-22 21:14:45', '2025-07-22 21:15:55'),
(56, 1, 26, 6, '2025-07-25 08:00:00', 'cancelled', '2025-07-22 21:17:28', NULL, NULL, 45000.00, NULL, 'cash_1753219007600', 'paid', NULL, 'efectivo', '2025-07-22 21:16:55', '2025-07-22 21:16:55', '2025-07-22 21:17:28'),
(57, 1, 35, 6, '2025-07-26 16:00:00', 'cancelled', '2025-07-22 21:19:24', NULL, NULL, 12000.00, NULL, 'cash_1753219137264', 'paid', NULL, 'efectivo', '2025-07-22 21:19:02', '2025-07-22 21:19:02', '2025-07-22 21:19:24'),
(58, 1, 35, 6, '2025-07-23 07:00:00', 'cancelled', '2025-07-22 21:22:06', NULL, NULL, 12000.00, NULL, 'cash_1753219295104', 'paid', NULL, 'efectivo', '2025-07-22 21:21:42', '2025-07-22 21:21:42', '2025-07-22 21:22:06'),
(59, 1, 35, 6, '2025-07-24 07:00:00', 'cancelled', '2025-07-22 21:34:34', NULL, NULL, 12000.00, NULL, 'cash_1753219999808', 'paid', NULL, 'efectivo', '2025-07-22 21:33:37', '2025-07-22 21:33:37', '2025-07-22 21:34:34'),
(60, 1, 37, 4, '2025-07-27 07:00:00', 'pending', NULL, NULL, NULL, 150000.00, NULL, 'cash_1753223462089', 'paid', NULL, 'efectivo', '2025-07-22 22:31:11', '2025-07-22 22:31:11', '2025-07-23 01:06:20'),
(61, 1, 37, 4, '2025-07-23 10:00:00', 'cancelled', '2025-07-22 22:38:45', NULL, NULL, 150000.00, NULL, 'cash_1753223534976', 'paid', NULL, 'efectivo', '2025-07-22 22:32:18', '2025-07-22 22:32:18', '2025-07-22 22:38:45'),
(62, 1, 9, 5, '2025-07-23 07:00:00', 'cancelled', '2025-07-23 01:29:26', NULL, NULL, 30000.00, NULL, 'cash_1753224143873', 'paid', NULL, 'efectivo', '2025-07-22 22:43:00', '2025-07-22 22:43:00', '2025-07-23 01:29:26'),
(63, 1, 37, 7, '2025-07-23 07:00:00', 'cancelled', '2025-07-23 01:36:25', NULL, NULL, 150000.00, NULL, 'cash_1753226169089', 'paid', NULL, 'efectivo', '2025-07-22 23:16:13', '2025-07-22 23:16:13', '2025-07-23 01:36:25'),
(64, 1, 37, 4, '2025-07-24 12:00:00', 'cancelled', '2025-07-23 01:48:07', NULL, NULL, 150000.00, NULL, 'cash_1753226267890', 'paid', NULL, 'efectivo', '2025-07-22 23:17:54', '2025-07-22 23:17:54', '2025-07-23 01:48:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('aesthectic_cache_5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1753386201),
('aesthectic_cache_5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1753386201;', 1753386201);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `address`, `city`, `state`, `postal_code`, `country`, `birth_date`, `profile_picture`, `status`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Jhon Deivis', 'Rivas Bermduez', 'rdeivis260502@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '$2y$12$RPFSXUnTLSvCWsmdiv/JCextWffxVcBjr6EhsPB/Cg/jbxLFzWRja', 'iEAHepp3fSxPMCJ9dU7AyZby5mKvHDarQG0X0ytiZeRxny58Q7YZcExw9eeA', '2025-07-10 04:33:06', '2025-07-24 19:46:23'),
(2, 'Valentina', 'Moreno Ríos', 'valentina.moreno22@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '$2y$12$uuaPn6xclicxC5WUSJBnMOPT1QURQkV2SWiswZyqg1LYwcXnRdoX.', NULL, '2025-07-17 03:33:56', '2025-07-17 03:33:56'),
(3, 'Sebastián', 'Torres Martínez', 'sebastiantorres98@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '$2y$12$xxIgqsyHBoR97VcOsZY9kOMD/80eOc2JngM1YGQgRmCN2VcoIl1MK', NULL, '2025-07-17 03:34:30', '2025-07-17 03:34:30'),
(4, 'Camila', 'Hernández López', 'camila.hernandez.lopez@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '$2y$12$0BukWCwJPAyyN9rHTrOdBeFzO4Qa42KqkSp9KUfZFAkyRZ8N9Z7zW', NULL, '2025-07-17 03:35:09', '2025-07-17 03:35:09'),
(5, 'Andrés Felipe', 'Gutiérrez', 'andres.felipe.gtz@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '$2y$12$kN0I5hC/v0q9Ii3L1XeOvuE3TJVpoTxJJMDMMkzOj7bRKV4epMn7K', NULL, '2025-07-17 03:36:06', '2025-07-17 03:36:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2015_04_12_000000_create_orchid_users_table', 1),
(5, '2015_10_19_214424_create_orchid_roles_table', 1),
(6, '2015_10_19_214425_create_orchid_role_users_table', 1),
(7, '2016_08_07_125128_create_orchid_attachmentstable_table', 1),
(8, '2017_09_17_125801_create_notifications_table', 1),
(9, '2024_10_20_201933_create_services_table', 1),
(10, '2024_10_20_202308_create_customers_table', 1),
(11, '2024_10_20_202320_create_bookings_table', 1),
(12, '2024_XX_XX_create_bin_lookups_table', 1),
(13, '2025_07_06_191045_create_service_profesional_table', 1),
(14, '2025_07_09_040609_add_cancelled_at_to_bookings_table', 1),
(15, '2025_07_10_212228_create_service_lists_table', 2),
(16, '2025_07_13_175014_add_completed_at_to_bookings_table', 3),
(17, '2025_07_23_222447_create_ratings_table', 4),
(18, '2025_07_24_100434_create_password_reset_tokens_table', 5),
(19, '2025_07_24_103630_add_remember_token_to_customers_table', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('200aa7c7-a3d3-4d8c-a2c3-c63e66938ad1', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\User', 6, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 24\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T19:10:27.305270Z\"}', NULL, '2025-07-22 19:10:27', '2025-07-22 19:10:27'),
('3a67a414-315b-41dc-a236-22dbdcba0505', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\Customer', 1, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 30\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T20:35:44.613573Z\"}', NULL, '2025-07-22 20:35:44', '2025-07-22 20:35:44'),
('3a8feb99-6490-44e5-b99c-d9bf742b5bdd', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\User', 4, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Alisado Permanente\' programada el 23\\/07\\/2025 07:00\",\"booking_id\":49,\"timestamp\":\"2025-07-22T19:09:55.945674Z\"}', NULL, '2025-07-22 19:09:55', '2025-07-22 19:09:55'),
('5b138b2d-5496-4830-921d-8608de5dd5ad', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\Customer', 1, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 23\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T19:03:14.263147Z\"}', NULL, '2025-07-22 19:03:14', '2025-07-22 19:03:14'),
('6ec0eeba-c9da-4f38-a624-272030c3e06e', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\Customer', 1, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 24\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T19:10:27.036933Z\"}', NULL, '2025-07-22 19:10:27', '2025-07-22 19:10:27'),
('7238e746-a3f9-4e07-87c3-7ade19fa8a9f', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\Customer', 1, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Alisado Permanente\' programada el 29\\/07\\/2025 07:00\",\"booking_id\":49,\"timestamp\":\"2025-07-22T19:04:37.139807Z\"}', NULL, '2025-07-22 19:04:37', '2025-07-22 19:04:37'),
('96658476-7218-47cb-873a-80d8866b2ae3', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\Customer', 1, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Alisado Permanente\' programada el 23\\/07\\/2025 07:00\",\"booking_id\":49,\"timestamp\":\"2025-07-22T19:09:55.837519Z\"}', NULL, '2025-07-22 19:09:55', '2025-07-22 19:09:55'),
('ad649e89-ba5a-47cc-90c2-609506f57ec1', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\User', 6, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 30\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T20:35:44.692965Z\"}', NULL, '2025-07-22 20:35:44', '2025-07-22 20:35:44'),
('b6c13692-d759-4d20-9786-3ab9fcaf981d', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\User', 6, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 23\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T20:30:01.480544Z\"}', NULL, '2025-07-22 20:30:01', '2025-07-22 20:30:01'),
('d3ca825d-8bc3-47d2-a995-5c8d9a73ea48', 'App\\Notifications\\BookingUpdatedOrCancelled', 'App\\Models\\Customer', 1, '{\"title\":\"Cita editada\",\"message\":\"El cliente  ha editada una cita para el servicio \'Dise\\u00f1o de Cejas con Hilo\' programada el 23\\/07\\/2025 07:00\",\"booking_id\":50,\"timestamp\":\"2025-07-22T20:30:01.214465Z\"}', NULL, '2025-07-22 20:30:01', '2025-07-22 20:30:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `professional_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '{\"platform.systems.roles\":\"1\",\"manage-services\":\"1\",\"platform.systems.users\":\"1\",\"platform.index\":\"1\",\"platform.systems.attachment\":\"1\"}', '2025-07-10 02:18:31', '2025-07-13 23:21:13'),
(2, 'profesional', 'profesional', '{\"platform.systems.roles\":\"0\",\"manage-services\":\"0\",\"platform.systems.users\":\"0\",\"platform.index\":\"1\",\"platform.systems.attachment\":\"1\"}', '2025-07-10 04:29:28', '2025-07-20 02:59:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_users`
--

CREATE TABLE `role_users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`) VALUES
(1, 1),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `image`, `price`, `duration`, `status`, `preferences`, `user_id`, `created_at`, `updated_at`) VALUES
(8, 'Corte de Cabello Hombre Básico', 'Corte clásico (solo cabeza)', '/storage/2025/07/16/5c20d8acfa2c7ea23a43d30adac3d886e6201fd1.png', 20000.00, '30', 'active', NULL, 1, '2025-07-17 02:38:52', '2025-07-17 02:40:03'),
(9, 'Corte Hombre con Barba', 'Corte y diseño de barba', '/storage/2025/07/16/9b1fdfdcee5c248acd269425350545c02b6f4adf.png', 30000.00, '45', 'active', NULL, 1, '2025-07-17 02:43:21', '2025-07-17 02:43:21'),
(10, 'Corte Hombre con Mascarilla', 'Corte + limpieza facial', '/storage/2025/07/16/8f967aaa39204dee661a6adaca19494387c72861.jpg', 35000.00, '50', 'active', NULL, 1, '2025-07-17 02:44:34', '2025-07-17 02:45:29'),
(11, 'Corte Niño', 'Corte especial para niños menores de 10 años', '/storage/2025/07/16/19f7aadf31b3e852b3fa98bda1068005b7eded42.jpg', 18000.00, '25', 'active', NULL, 1, '2025-07-17 02:46:39', '2025-07-17 02:46:39'),
(12, 'Corte de Cabello Mujer Básico', 'Corte recto o en capas sin peinados', '/storage/2025/07/16/d5b429e3dce08ef86cfba4d372e1f22ff89ea0c5.jpg', 25000.00, '45', 'active', NULL, 1, '2025-07-17 02:47:42', '2025-07-17 02:47:42'),
(13, 'Corte Mujer + Secado', 'Corte más secado con blower', '/storage/2025/07/16/a5fd4e7abe1c52d8f9317d9dc6433dce3b3549d8.jpg', 40000.00, '60', 'active', NULL, 1, '2025-07-17 02:48:45', '2025-07-17 02:48:45'),
(14, 'Corte Mujer + Plancha', 'Corte y planchado profesional', '/storage/2025/07/16/f51f96549dc3a6f19884c38e6f41738c7f9730e3.jpg', 50000.00, '75', 'active', NULL, 1, '2025-07-17 02:49:50', '2025-07-17 02:49:50'),
(15, 'Corte Mujer + Mascarilla Capilar', 'Corte, mascarilla y cepillado', '/storage/2025/07/16/8db61ff141701cf3bdea99a37d946c8da71df6a0.jpg', 60000.00, '80', 'active', NULL, 1, '2025-07-17 02:51:13', '2025-07-17 02:51:13'),
(16, 'Peinado Básico', 'Trenzas, ondas o recogido simple', '/storage/2025/07/16/122cbb9b3ede3303f8aca5e8c1510550c664f604.jpg', 20000.00, '30', 'active', NULL, 1, '2025-07-17 02:52:43', '2025-07-17 02:52:43'),
(17, 'Peinado para Evento', 'Peinado profesional para bodas o fiestas', '/storage/2025/07/16/153bb25e8ff7284d773a991292afdccbf0e095be.png', 50000.00, '60', 'active', NULL, 1, '2025-07-17 02:53:59', '2025-07-17 02:53:59'),
(18, 'Manicure Tradicional', 'Corte, limado, pintura', '/storage/2025/07/16/27833e72642a87f68b991f94153c8c5c595bab7a.png', 9000.00, '25', 'active', NULL, 1, '2025-07-17 02:56:47', '2025-07-17 02:56:47'),
(19, 'Manicure con Diseño', 'Incluye diseños personalizados', '/storage/2025/07/16/e24f9605981260262f7d4fb3b82e61c61ce9be15.jpg', 12000.00, '45', 'active', NULL, 1, '2025-07-17 03:01:38', '2025-07-17 03:01:38'),
(20, 'Manicure Semipermanente', 'Duración larga, más brillo', '/storage/2025/07/16/aaaebc0887ad0a564936e16f17c1fc064aa7e47d.jpg', 35000.00, '60', 'active', NULL, 1, '2025-07-17 03:04:00', '2025-07-17 03:04:00'),
(21, 'Pedicure Tradicional', 'Corte, limado, pintura de pies', '/storage/2025/07/16/9dd0e34d99aa89f6e746711b7a07d7a5950d5c75.png', 15000.00, '40', 'active', NULL, 1, '2025-07-17 03:05:39', '2025-07-17 03:05:39'),
(22, 'Pedicure Spa', 'Exfoliación, hidratación y pintura', '/storage/2025/07/16/0526ce051da5489989613c379950483410eda9ad.jpg', 22000.00, '60', 'active', NULL, 1, '2025-07-17 03:07:30', '2025-07-17 03:07:30'),
(23, 'Depilación Facial', 'Cejas, bozo, mentón', '/storage/2025/07/16/8d57399e3f83ba39f3b6279bb64f0b740132afbe.png', 10000.00, '15', 'active', NULL, 1, '2025-07-17 03:08:33', '2025-07-17 03:08:33'),
(24, 'Depilación Corporal', 'Piernas, axilas, brazos', '/storage/2025/07/16/cb44718aeac33be66724750bcbaa09012fd34dc2.jpg', 35000.00, '45', 'active', NULL, 1, '2025-07-17 03:09:32', '2025-07-17 03:09:32'),
(25, 'Limpieza Facial Básica', 'Limpieza de impurezas', '/storage/2025/07/16/c2f4f85c2f7cc0e8c51861a5f088b3964e3d5de1.png', 30000.00, '40', 'active', NULL, 1, '2025-07-17 03:10:48', '2025-07-17 03:10:48'),
(26, 'Limpieza Facial con Exfoliación', 'Limpieza profunda + exfoliante', '/storage/2025/07/16/eb384c983d9efce2dfd3fac4e1dbb66607cf197d.jpg', 45000.00, '60', 'active', NULL, 1, '2025-07-17 03:11:49', '2025-07-17 03:11:49'),
(27, 'Limpieza Facial con Mascarilla de Colágeno', 'Rejuvenecimiento facial', '/storage/2025/07/16/2d74d031869248fe16a47c5b8e8b8d26222198c3.jpg', 60000.00, '75', 'active', NULL, 1, '2025-07-17 03:12:41', '2025-07-17 03:12:41'),
(28, 'Tratamiento Capilar Hidratante', 'Nutre e hidrata profundamente', '/storage/2025/07/16/c7d4476042f003088518205080efbe2d7cac833d.jpg', 40000.00, '60', 'active', NULL, 1, '2025-07-17 03:13:47', '2025-07-17 03:13:47'),
(29, 'Tratamiento Anticaspa', 'Limpia y previene caspa', '/storage/2025/07/16/89b100fec8386fc0de4f6a02196af77c871ff034.jpg', 30000.00, '45', 'active', NULL, 1, '2025-07-17 03:15:00', '2025-07-17 03:15:00'),
(30, 'Tinte Completo', 'Color uniforme para todo el cabello', '/storage/2025/07/16/b221464c77d62ab96071124ff8a957a162f64f80.png', 80000.00, '90', 'active', NULL, 1, '2025-07-17 03:16:31', '2025-07-17 03:16:31'),
(31, 'Mechas / Balayage', 'Técnica moderna de coloración', '/storage/2025/07/16/6a333fcef5f0c1e3f01262b6457e601c03e98243.jpeg', 100000.00, '120', 'active', NULL, 1, '2025-07-17 03:18:03', '2025-07-17 03:18:03'),
(32, 'Matizante', 'Refuerzo del color para rubios', '/storage/2025/07/16/ba66b8630fd383bf3551591aa4b9dee1982703f0.jpg', 30000.00, '30', 'active', NULL, 1, '2025-07-17 03:19:23', '2025-07-17 03:19:23'),
(33, 'Uñas Acrílicas', 'Esculpidas y decoradas', '/storage/2025/07/16/0f587d99b364570b03784200b0799f01f4e714f4.jpg', 75000.00, '90', 'active', NULL, 1, '2025-07-17 03:21:10', '2025-07-17 03:21:10'),
(34, 'Barbería Premium', 'Corte, barba, cejas y vapor', '/storage/2025/07/16/ab376ec592a7f012b06ee82c7ee1a1aa465f3795.jpg', 45000.00, '60', 'active', NULL, 1, '2025-07-17 03:22:08', '2025-07-17 03:22:08'),
(35, 'Diseño de Cejas con Hilo', 'Definición precisa con hilo', '/storage/2025/07/16/3b25f7233bb241717988f83cc62110a5eb7aff9d.jpg', 12000.00, '20', 'active', NULL, 1, '2025-07-17 03:23:14', '2025-07-17 03:23:14'),
(36, 'Maquillaje para Evento', 'Profesional para eventos especiales', '/storage/2025/07/16/834d985aa27729e3dd0ad7f6a146a080bab4d70a.jpg', 60000.00, '60', 'active', NULL, 1, '2025-07-17 03:23:59', '2025-07-17 03:23:59'),
(37, 'Alisado Permanente', 'Cambio estructural del cabello', '/storage/2025/07/16/28ee65ae88a75fcd867027c9880c8373d3afee03.png', 150000.00, '120', 'active', NULL, 1, '2025-07-17 03:25:57', '2025-07-20 02:41:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_lists`
--

CREATE TABLE `service_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `service_lists`
--

INSERT INTO `service_lists` (`id`, `name`, `created_at`, `updated_at`) VALUES
(13, 'Corte de Cabello Hombre Básico', '2025-07-17 02:00:44', '2025-07-17 02:00:44'),
(14, 'Corte Hombre con Barba', '2025-07-17 02:00:55', '2025-07-17 02:00:55'),
(15, 'Corte Hombre con Mascarilla', '2025-07-17 02:01:06', '2025-07-17 02:01:06'),
(16, 'Corte Niño', '2025-07-17 02:01:17', '2025-07-17 02:01:17'),
(17, 'Corte de Cabello Mujer Básico', '2025-07-17 02:01:28', '2025-07-17 02:01:28'),
(18, 'Corte Mujer + Secado', '2025-07-17 02:01:37', '2025-07-17 02:01:37'),
(19, 'Corte Mujer + Plancha', '2025-07-17 02:01:44', '2025-07-17 02:01:44'),
(20, 'Corte Mujer + Mascarilla Capilar', '2025-07-17 02:01:54', '2025-07-17 02:01:54'),
(21, 'Peinado Básico', '2025-07-17 02:02:03', '2025-07-17 02:02:03'),
(22, 'Peinado para Evento', '2025-07-17 02:02:11', '2025-07-17 02:02:11'),
(23, 'Manicure Tradicional', '2025-07-17 02:02:19', '2025-07-17 02:02:19'),
(24, 'Manicure con Diseño', '2025-07-17 02:02:43', '2025-07-17 02:02:43'),
(25, 'Manicure Semipermanente', '2025-07-17 02:02:51', '2025-07-17 02:02:51'),
(26, 'Pedicure Tradicional', '2025-07-17 02:03:00', '2025-07-17 02:03:00'),
(27, 'Pedicure Spa', '2025-07-17 02:03:10', '2025-07-17 02:03:10'),
(28, 'Depilación Facial', '2025-07-17 02:03:21', '2025-07-17 02:03:21'),
(29, 'Depilación Corporal', '2025-07-17 02:03:31', '2025-07-17 02:03:31'),
(30, 'Limpieza Facial Básica', '2025-07-17 02:03:39', '2025-07-17 02:03:39'),
(31, 'Limpieza Facial con Exfoliación', '2025-07-17 02:03:48', '2025-07-17 02:03:48'),
(32, 'Limpieza Facial con Mascarilla de Colágeno', '2025-07-17 02:03:57', '2025-07-17 02:03:57'),
(33, 'Tratamiento Capilar Hidratante', '2025-07-17 02:04:06', '2025-07-17 02:04:06'),
(34, 'Tratamiento Anticaspa', '2025-07-17 02:04:15', '2025-07-17 02:04:15'),
(35, 'Tinte Completo', '2025-07-17 02:04:24', '2025-07-17 02:04:24'),
(36, 'Mechas / Balayage', '2025-07-17 02:04:34', '2025-07-17 02:04:34'),
(37, 'Matizante', '2025-07-17 02:04:45', '2025-07-17 02:04:45'),
(38, 'Uñas Acrílicas', '2025-07-17 02:04:55', '2025-07-17 02:04:55'),
(39, 'Barbería Premium', '2025-07-17 02:05:03', '2025-07-17 02:05:03'),
(40, 'Diseño de Cejas con Hilo', '2025-07-17 02:05:16', '2025-07-17 02:05:16'),
(41, 'Maquillaje para Evento', '2025-07-17 02:05:26', '2025-07-17 02:05:26'),
(42, 'Alisado Permanente', '2025-07-17 02:05:33', '2025-07-17 02:05:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_list_user`
--

CREATE TABLE `service_list_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_list_id` bigint(20) UNSIGNED NOT NULL,
  `professional_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `service_list_user`
--

INSERT INTO `service_list_user` (`id`, `service_list_id`, `professional_id`, `created_at`, `updated_at`) VALUES
(18, 16, 5, NULL, NULL),
(19, 14, 5, NULL, NULL),
(20, 13, 5, NULL, NULL),
(21, 39, 5, NULL, NULL),
(22, 42, 4, NULL, NULL),
(23, 20, 4, NULL, NULL),
(24, 19, 4, NULL, NULL),
(25, 18, 4, NULL, NULL),
(26, 17, 4, NULL, NULL),
(27, 29, 4, NULL, NULL),
(28, 28, 4, NULL, NULL),
(29, 17, 6, NULL, NULL),
(30, 29, 6, NULL, NULL),
(31, 40, 6, NULL, NULL),
(32, 30, 6, NULL, NULL),
(33, 31, 6, NULL, NULL),
(34, 32, 6, NULL, NULL),
(35, 24, 7, NULL, NULL),
(36, 25, 7, NULL, NULL),
(37, 23, 7, NULL, NULL),
(38, 41, 7, NULL, NULL),
(39, 27, 7, NULL, NULL),
(40, 26, 7, NULL, NULL),
(41, 21, 7, NULL, NULL),
(42, 22, 7, NULL, NULL),
(43, 38, 7, NULL, NULL),
(44, 37, 8, NULL, NULL),
(45, 35, 8, NULL, NULL),
(46, 34, 8, NULL, NULL),
(47, 33, 8, NULL, NULL),
(48, 36, 8, NULL, NULL),
(49, 42, 7, NULL, NULL),
(50, 42, 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_professional`
--

CREATE TABLE `service_professional` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `professional_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `service_professional`
--

INSERT INTO `service_professional` (`id`, `service_id`, `professional_id`, `created_at`, `updated_at`) VALUES
(8, 8, 5, NULL, NULL),
(9, 9, 5, NULL, NULL),
(10, 11, 5, NULL, NULL),
(11, 12, 4, NULL, NULL),
(12, 12, 6, NULL, NULL),
(13, 13, 4, NULL, NULL),
(14, 14, 4, NULL, NULL),
(15, 15, 4, NULL, NULL),
(16, 16, 7, NULL, NULL),
(17, 17, 7, NULL, NULL),
(18, 18, 7, NULL, NULL),
(19, 19, 7, NULL, NULL),
(20, 20, 7, NULL, NULL),
(21, 21, 7, NULL, NULL),
(22, 22, 7, NULL, NULL),
(23, 23, 4, NULL, NULL),
(24, 24, 4, NULL, NULL),
(25, 24, 6, NULL, NULL),
(26, 25, 6, NULL, NULL),
(27, 26, 6, NULL, NULL),
(28, 27, 6, NULL, NULL),
(29, 28, 8, NULL, NULL),
(30, 29, 8, NULL, NULL),
(31, 30, 8, NULL, NULL),
(32, 31, 8, NULL, NULL),
(33, 32, 8, NULL, NULL),
(34, 33, 7, NULL, NULL),
(35, 34, 5, NULL, NULL),
(36, 35, 6, NULL, NULL),
(37, 36, 7, NULL, NULL),
(38, 37, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `photo`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `permissions`) VALUES
(1, NULL, 'JuanDiaz1', 'Juandiaz@admin.com', NULL, '$2y$12$Q66e6LXMo4GXZBidERLIb.E2sCPgpmEXX5rf9.N8bUio/fc1145xy', 'Vq0aaZo13LuufO9abdWMiifmOZGIAHxXd4ksPGweGEL7wYEHuPdCdTEooTDC', '2025-07-10 02:05:50', '2025-07-10 02:18:59', '[]'),
(4, '/storage/2025/07/16/4432d9eb4f5129557a842dc3c036d1dad128cc9b.avif', 'Laura Gómez', 'LauraGomez@profesional.com', NULL, '$2y$12$fx1D6XLHi0Q3ek0FG0Q4u.R780/W7lDQODYiz8nYpY9Kxscs3.c7.', 'QAEMz4SB8OIrIpqVPoJMvCMhReLRsZhFwZReHeaS78fYbZhxamRUY2Zp8mTF', '2025-07-17 02:17:37', '2025-07-17 02:23:53', '[]'),
(5, '/storage/2025/07/16/545d0bb4a2a5ea558b74e980c6a91cc3de483b65.avif', 'Carlos Torres', 'CarlosTorres@profesional.com', NULL, '$2y$12$1mmqzru0eeLYHTqcDEiTQe5H5A6PuHPx/ja5yau7ZLWyI5oYRJGFC', 'FqPG3UKtJOPHdwVfclMuNICh6EX2rdE4nt1vpIwdrC8mI86gbBLWPT4X8nKw', '2025-07-17 02:25:22', '2025-07-17 02:25:22', '[]'),
(6, '/storage/2025/07/16/e5b08acfd6874790252038de50870009052f77d8.png', 'Mariana Pérez', 'MarianaPerez@profesional.com', NULL, '$2y$12$N0qxusn.SnRZFxnw5KQc9uP2Rs66bCHcTiU0nrLs/bU2nIyEY487i', 'cgnJtAFSVSpgjLz812SAyOPYRwg6fZ3LSkg4wEi5qajIElut8VglzW4yxugp', '2025-07-17 02:28:16', '2025-07-17 02:28:16', '[]'),
(7, '/storage/2025/07/16/27449531723c8b2cec6be634b74a14d812f7ef8f.png', 'Andrés Rojas', 'AndresRojas@profesional.com', NULL, '$2y$12$9ipkQACdGN8j7HqZTZ8/8ethGYEqj/jv1HIIXBii6hVRLaPREfml.', NULL, '2025-07-17 02:34:07', '2025-07-17 02:34:07', '[]'),
(8, '/storage/2025/07/16/4572737a093c74386c53a855b94f3bd2329f76fc.png', 'Luisa Martínez', 'LuisaMartinez@profesional.com', NULL, '$2y$12$UB4YBWxLe9Q01XJ0.PTmju2K.gzpezZrPxovBfOHGKppApJGCZHdG', 'QawGg9XbuWZt5usM8MVzPGL34P44HvwfVZCu4VWcqRNReEsd2c2ASU7IkUuJ', '2025-07-17 02:35:53', '2025-07-17 02:35:53', '[]');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `attachmentable`
--
ALTER TABLE `attachmentable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachmentable_attachmentable_type_attachmentable_id_index` (`attachmentable_type`,`attachmentable_id`),
  ADD KEY `attachmentable_attachment_id_foreign` (`attachment_id`);

--
-- Indices de la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `bookings_service_id_foreign` (`service_id`),
  ADD KEY `bookings_professional_id_foreign` (`professional_id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_booking_id_foreign` (`booking_id`),
  ADD KEY `ratings_customer_id_foreign` (`customer_id`),
  ADD KEY `ratings_professional_id_foreign` (`professional_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indices de la tabla `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_users_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `service_lists`
--
ALTER TABLE `service_lists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_lists_name_unique` (`name`);

--
-- Indices de la tabla `service_list_user`
--
ALTER TABLE `service_list_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_list_user_service_list_id_foreign` (`service_list_id`),
  ADD KEY `service_list_user_professional_id_foreign` (`professional_id`);

--
-- Indices de la tabla `service_professional`
--
ALTER TABLE `service_professional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_professional_service_id_professional_id_unique` (`service_id`,`professional_id`),
  ADD KEY `service_professional_professional_id_foreign` (`professional_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `attachmentable`
--
ALTER TABLE `attachmentable`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `service_lists`
--
ALTER TABLE `service_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `service_list_user`
--
ALTER TABLE `service_list_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `service_professional`
--
ALTER TABLE `service_professional`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `attachmentable`
--
ALTER TABLE `attachmentable`
  ADD CONSTRAINT `attachmentable_attachment_id_foreign` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `role_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `service_list_user`
--
ALTER TABLE `service_list_user`
  ADD CONSTRAINT `service_list_user_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_list_user_service_list_id_foreign` FOREIGN KEY (`service_list_id`) REFERENCES `service_lists` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `service_professional`
--
ALTER TABLE `service_professional`
  ADD CONSTRAINT `service_professional_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_professional_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
