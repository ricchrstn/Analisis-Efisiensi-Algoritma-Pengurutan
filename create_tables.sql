-- create_tables.sql
-- Database setup untuk Analisis Efisiensi Algoritma Pengurutan

CREATE DATABASE IF NOT EXISTS algoritma_pengurutan DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE algoritma_pengurutan;

-- Tabel untuk menyimpan nilai mahasiswa
CREATE TABLE IF NOT EXISTS dataset_nilai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nilai INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_nilai (nilai)
);

-- Tabel untuk menyimpan hasil running waktu kedua algoritma
CREATE TABLE IF NOT EXISTS hasil_running (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ukuran_data INT NOT NULL,
  waktu_selection DOUBLE NOT NULL COMMENT 'Waktu Selection Sort (detik)',
  waktu_insertion DOUBLE NOT NULL COMMENT 'Waktu Insertion Sort (detik)',
  created_at DATETIME NOT NULL,
  INDEX idx_ukuran (ukuran_data),
  INDEX idx_created (created_at)
);