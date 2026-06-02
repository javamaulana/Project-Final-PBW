<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&height=300&color=gradient&text=FINAL%20PROJEK%20PBW&fontSize=50&descAlignY=53&descAlign=50&fontAlignY=37&desc=Oleh%20Java%20Maulana" alt="Header Final Projek Java" />
</p>

## Tentang Proyek
Selamat datang! Repositori ini berisi kode sumber lengkap untuk proyek akhir mata kuliah **Pemrograman Berbasis Web (PBW)** di Departemen Matematika dan Sains Data, Universitas Andalas. 

Aplikasi yang dibangun bernama **"Jejak Berkas Disdukcapil"**, sebuah sistem informasi pelacakan (*tracking*) proses pengurusan KTP-el baru secara transparan, cepat, dan *real-time*. Aplikasi ini dibuat menggunakan PHP Native Prosedural yang bersih agar performanya ringan, serta Bootstrap 5 untuk memastikan tampilannya rapi dan nyaman dilihat di HP maupun laptop.

## Alur Kerja Sistem (Bagaimana Aplikasi Ini Bekerja?)
Sistem ini dibuat sangat ramah pengguna dengan memotong jalur birokrasi digital yang rumit. Alurnya dibagi menjadi dua sisi:

1. **Sisi Loket Kantor Capil (Petugas/Dinas):**
   * Warga datang secara langsung (*offline*) membawa berkas fisik ke kantor Dukcapil atau MPP.
   * Petugas mengecek kelengkapan berkas secara manual. 
   * Jika berkas lengkap, petugas masuk ke sistem internal via `login.php` menggunakan akun mereka.
   * Petugas mendaftarkan data warga (Nama, NIK, Alamat) lewat form internal.
   * Sistem otomatis menerbitkan **Nomor Registrasi unik** (Contoh: `REG-20260602-123`). Petugas memberikan nomor ini kepada warga sebelum mereka pulang.
   * Setiap kali warga menyelesaikan satu tahapan (seperti foto atau cetak), petugas akan memperbarui (*update*) statusnya di dasbor internal.

2. **Sisi Warga / Masyarakat (Pelacakan Transparan):**
   * Warga tidak perlu repot membuat akun, menghafal *username*, atau mengisi formulir online yang membingungkan.
   * Warga cukup membuka halaman utama website (`index.php`), memasukkan Nomor Registrasi yang mereka terima dari loket, lalu klik **Lacak Status**.
   * Sistem akan langsung menampilkan riwayat perjalanan KTP mereka dalam bentuk *Timeline* vertikal yang komunikatif dan *real-time*.

## Struktur File & Folder
Proyek ini disusun secara modular dan teratur agar kodenya mudah dipahami:
```text
jejak_berkas/
│
├── config/
│   └── database.php      # Pengaturan koneksi database MySQL online/lokal
│
├── dinas/
│   ├── dashboard.php     # Dasbor utama petugas untuk melihat semua antrean warga
│   ├── tambah_warga.php  # Form pendaftaran warga baru saat datang ke loket
│   └── update_status.php # Menu petugas untuk memindahkan tahapan alur layanan KTP
│
├── index.php             # Halaman utama publik tempat warga melacak status KTP
├── login.php             # Gerbang masuk untuk petugas dinas
└── logout.php            # Menghapus sesi login petugas
