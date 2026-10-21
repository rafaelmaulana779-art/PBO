# Mini Application OOP — Peminjaman Perangkat Laboratorium

**Nama:** Rafael Maulana Prasetya  
**NIM:** 20251020056  
**Program Studi:** Teknik Informatika

## 1. Masalah dan domain
Aplikasi ini melanjutkan domain Sistem Peminjaman Perangkat Praktikum Laboratorium dari tugas Pertemuan 2/3. Petugas perlu mengetahui jenis perangkat, menjaga status ketersediaan, memproses peminjaman/pengembalian, dan menolak perangkat yang rusak atau sedang dipinjam. Contoh yang digunakan adalah mouse wireless dan router praktikum.

## 2. Kandidat class
1. `Perangkat` — abstract class untuk data dan aturan umum perangkat.
2. `PerangkatInput` — representasi perangkat input seperti mouse.
3. `PerangkatJaringan` — representasi perangkat jaringan seperti router.
4. `Peminjaman` — mengelola transaksi dan relasi dengan perangkat.
5. `DapatDipinjam` — interface yang menetapkan kontrak `pinjam()` dan `kembalikan()`.

## 3. Relasi dan alasan desain
- **Inheritance:** `PerangkatInput` dan `PerangkatJaringan` mewarisi `Perangkat` karena berbagi data dan aturan umum, tetapi memiliki kategori serta catatan penggunaan berbeda.
- **Abstract class:** `Perangkat` tidak dibuat langsung; subclass harus menyediakan `getKategori()` dan `getCatatanPenggunaan()`.
- **Interface:** `DapatDipinjam` menetapkan operasi yang harus tersedia untuk objek yang dapat dipinjam.
- **Composition/has-a:** `Peminjaman` memiliki referensi ke satu objek `Perangkat` dan menggunakan objek itu saat memproses pinjam/kembali.
- **Overriding:** kedua subclass mengimplementasikan `getKategori()` dan `getCatatanPenggunaan()` dengan hasil masing-masing.
- **Polymorphism:** array berisi objek berbeda yang diperlakukan sebagai `Perangkat`; pemanggilan method yang sama menghasilkan hasil sesuai jenis objek aktual.

## 4. Encapsulation dan validasi
Property inti dibuat `private`; informasi dibaca lewat getter dan perubahan kondisi/status dilakukan melalui method. Validasi memastikan kode/nama tidak kosong, kondisi hanya `Baik`, `Rusak Ringan`, atau `Rusak`, perangkat rusak tidak dapat dipinjam, perangkat yang sudah dipinjam tidak dapat dipinjam ulang, dan pengembalian hanya dilakukan untuk transaksi aktif. Kesalahan ditangani dengan exception yang memiliki pesan jelas.

## 5. Skenario pengujian
1. Peminjaman normal berhasil.
2. Peminjaman ulang ditolak.
3. Pengembalian normal berhasil.
4. Perangkat rusak ditolak saat dipinjam.
5. Nilai kondisi yang tidak valid ditolak.
6. Nama perangkat kosong ditolak.

Output aktual tersimpan di `hasil-pengujian.txt`, sedangkan gambar output ada di `screenshot-output.png`.

## 6. Cara menjalankan
Pastikan PHP tersedia, lalu jalankan:
```bash
php 20251020056_Rafael_Maulana_PBO_M5.php
```
Tidak diperlukan database, Laravel, atau framework.

## 7. Kesimpulan
Model ini menggabungkan abstraksi, interface, inheritance, overriding, polymorphism, composition, serta encapsulation. Aturan perubahan state diletakkan pada objek yang bertanggung jawab sehingga status perangkat tidak diubah sembarangan. Struktur ini dapat diperluas dengan class anggota, petugas, atau riwayat tanpa mengubah kontrak dasar perangkat.
