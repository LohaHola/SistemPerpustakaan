Sistem Perpustakaan
------------------------------------
Instruksi singkat:

curhatan pembuat kode:
wahai teman-teman sekalian
sesungguhya saat saya buat kode ini hanya saya dan tuhan yang ngerti
tapi setelah saya gaya-gayaan mau nambah fitur keamanan extra dan lupa nge commit hanya tuhan yang tau
sumpah aku harusnya ga ngubah sesuatu yang sudah berjalan :V
AI pun kek taik setiap kali kutanya apa yang salah malah dibuat kode dengan struktur baru aseemm

1. Pastikan XAMPP (Apache + MySQL) berjalan.
2. Buat database 'perpustakaan_db' dan buat tabel:
   - akun (id_akun PK AUTO_INCREMENT, username UNIQUE, password, role, nama_depan, nama_belakang, email, nomor_telpon)
   - buku (id_buku PK AUTO_INCREMENT, judul, penulis, stok)
   - peminjaman (id_pinjam PK AUTO_INCREMENT, id_akun FK -> akun.id_akun, id_buku FK -> buku.id_buku, tanggal_pinjam DATETIME, durasi_pinjam INT)
   - pengunjung (id_kunjungan PK AUTO_INCREMENT, id_akun FK -> akun.id_akun, tanggal_masuk DATETIME)

3. Copy folder perpustakaan_oop ke C:\xampp\htdocs\ (atau sesuai root server)
4. Akses via browser: http://localhost/APSI/SistemPerpustakaan.com
5. Daftar akun baru, login, dan coba fitur tambah buku (role komite), pinjam, profil.

Catatan:
Jika file tidak memiliki tree seperti berikut pasti ada kesalahan.
SistemPerpustakaan/
│
├── config/
│   └── database.php
│
├── classes/
│   ├── Akun.php
│   ├── Buku.php
│   ├── Peminjaman.php
│   ├── Pengunjung.php
│   ├── KomitePerpustakaan.php
│   └── PengunjungBiasa.php
│
├── layout/
│   ├── header.php
│   └── footer.php
│
├── assets/
│   ├── style.css
│   ├── logo.png
│   └── profile_icon.png
│
├── index.php
├── register.php
├── home.php
├── pinjam.php
├── kembalikan.php
├── update_buku.php
├── daftar_peminjaman.php
├── profil.php
├── faq.php
├── admin_panel.php
├── logout.php
└── README.txt


