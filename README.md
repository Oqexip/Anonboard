---

````markdown
# AnonBoard 📝  
Platform diskusi anonim sederhana berbasis **Laravel 11 + TailwindCSS**.  
Pengguna dapat membuat thread, membalas komentar, dan berdiskusi tanpa harus mengungkap identitas asli mereka.  

## ✨ Fitur Utama
- 🔐 **Autentikasi** (registrasi, login, logout)  
- 🗂️ **Board & Thread**  
  - Buat board khusus (misalnya: General, Science, Books, dll)  
  - Buat thread dalam board tertentu  
- 💬 **Post, Comment, & Reply**  
  - Anonim maupun user terdaftar bisa membuat post  
  - Komentar & balasan dengan sistem nested (threaded discussion)  
- ✏️ **Edit & Hapus**  
  - Hanya pemilik post, comment, atau reply yang bisa mengedit/menghapus  
  - Edit hanya bisa dilakukan dalam waktu ≤ 15 menit setelah posting  
- 📎 **Upload Gambar** (opsional di post/thread)  
- 📱 **UI Responsif** dengan TailwindCSS  

## 🛠️ Teknologi
- [Laravel 11](https://laravel.com/) (PHP Framework)  
- [MySQL](https://www.mysql.com/) (Database)  
- [TailwindCSS](https://tailwindcss.com/) (UI styling)  
- [Alpine.js](https://alpinejs.dev/) (interaktivitas ringan)  

## ⚙️ Instalasi
1. Clone repositori
   ```bash
   git clone https://github.com/Oqexip/Anonboard.git
   cd Anonboard
````

2. Install dependency

   ```bash
   composer install
   npm install && npm run build
   ```
3. Buat file `.env` dan sesuaikan konfigurasi database

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Migrasi & seeding database

   ```bash
   php artisan migrate --seed
   ```
5. Jalankan server

   ```bash
   php artisan serve
   ```
   
## 🤝 Kontribusi

Pull request terbuka untuk perbaikan bug, fitur baru, atau dokumentasi.

1. Fork repo ini
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m "Menambahkan fitur X"`)
4. Push ke branch (`git push origin fitur-baru`)
5. Ajukan pull request

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT](LICENSE).

---

💡 Dibuat dengan ❤️ oleh **Oqexip** untuk belajar & berbagi.

```
