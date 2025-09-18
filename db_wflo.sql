-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 02 Sep 2025 pada 07.37
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_wflo`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_orders`
--

CREATE TABLE `job_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `shift_no` int(11) NOT NULL,
  `arrival_time` time NOT NULL,
  `job_order_no` varchar(255) NOT NULL,
  `truck_no` varchar(255) NOT NULL,
  `palka_no` varchar(255) DEFAULT NULL,
  `hose_no` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `job_orders`
--

INSERT INTO `job_orders` (`id`, `date`, `shift_no`, `arrival_time`, `job_order_no`, `truck_no`, `palka_no`, `hose_no`, `created_at`, `updated_at`) VALUES
(1, '2025-09-02', 1, '12:23:00', '121211212121212', 'Truck-1', 'Palka-A', 'Hose-1', '2025-09-01 22:23:08', '2025-09-01 22:24:19'),
(2, '2025-09-02', 1, '12:25:00', '111', 'Truck-1', 'Palka-A', 'Hose-2', '2025-09-01 22:25:05', '2025-09-01 22:25:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2025_08_28_212745_create_job_orders_table', 1),
(4, '2025_08_28_212831_create_time_lists_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `time_lists`
--

CREATE TABLE `time_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_order_id` bigint(20) UNSIGNED NOT NULL,
  `truck_no` varchar(255) NOT NULL,
  `plugging` time DEFAULT NULL,
  `open_valve` time DEFAULT NULL,
  `close_valve` time DEFAULT NULL,
  `unplugging` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `time_lists`
--

INSERT INTO `time_lists` (`id`, `job_order_id`, `truck_no`, `plugging`, `open_valve`, `close_valve`, `unplugging`, `created_at`, `updated_at`) VALUES
(1, 1, 'Truck-1', '05:00:00', '01:00:00', '02:00:00', '02:00:00', '2025-09-01 22:23:09', '2025-09-01 22:24:19'),
(2, 2, 'Truck-1', NULL, NULL, NULL, NULL, '2025-09-01 22:25:05', '2025-09-01 22:25:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'operator1', 'operator1@wflo.com', NULL, '$2y$10$TcJB6tpPeSFu6Rkhu1p.I.mq/eYSiiN3jiB0xClKLdk1w0FVD0M1.', NULL, '2025-09-02 05:19:28', '2025-09-01 22:22:27'),
(2, 'operator2', 'operator2@wflo.com', NULL, '$2y$10$gjq1GrRxhgah5uvPWPQVc.EOkfaTnQ5v8CUmVnqu5ykW7UJMtiOzy', NULL, '2025-09-02 05:19:28', '2025-09-01 22:22:27'),
(3, 'operator3', 'operator3@wflo.com', NULL, '$2y$10$4Z9E8Jzm1l9kXW45vAFceOBWLFvlbrrc3pBZ8snlN2bnhgRZmWkFm', NULL, '2025-09-02 05:19:28', '2025-09-01 22:22:27'),
(4, 'operator4', 'operator4@wflo.com', NULL, '$2y$10$SHr9Xm10QGe9Z/8q1PsU.u2x2ZyA5MHUxg/67Rynr8JWD42d6.EgG', NULL, '2025-09-02 05:19:28', '2025-09-01 22:22:27'),
(5, 'operator5', 'operator5@wflo.com', NULL, '$2y$10$GaTy/xXuPHgBvAraMygRKOv/6NEubUpxc1HjLVYcKY6CGeN6N52w.', NULL, '2025-09-02 05:19:28', '2025-09-01 22:22:27'),
(6, 'operator6', 'operator6@wflo.com', NULL, '$2y$10$gRG7J2JkD9Mv2TS8lC7DpOpMkvgNU2N.6p/RFGI7glpsxVENJMBf.', NULL, '2025-09-02 05:19:28', '2025-09-01 22:22:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `job_orders`
--
ALTER TABLE `job_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_orders_job_order_no_unique` (`job_order_no`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `time_lists`
--
ALTER TABLE `time_lists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `time_lists_job_order_id_unique` (`job_order_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `job_orders`
--
ALTER TABLE `job_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `time_lists`
--
ALTER TABLE `time_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `time_lists`
--
ALTER TABLE `time_lists`
  ADD CONSTRAINT `time_lists_job_order_id_foreign` FOREIGN KEY (`job_order_id`) REFERENCES `job_orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
