# OmongIn 📝  

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)  
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-blue?logo=tailwindcss)  
![License](https://img.shields.io/badge/license-MIT-green)  

**ObrolIn** is a simple anonymous discussion platform built with **Laravel 12 + TailwindCSS**.  
Users can create threads, post comments, and reply without revealing their real identity.  

---

## ✨ Features
- 🔐 **Authentication** (register, login, logout)  
- 🗂️ **Boards & Threads**  
  - Create custom boards (e.g., General, Science, Books, etc.)  
  - Start discussion threads under specific boards  
- 💬 **Posts, Comments & Replies**  
  - Supports both anonymous and registered users  
  - Nested (threaded) comments & replies  
- ✏️ **Edit & Delete**  
  - Only the owner of a post, comment, or reply can edit/delete  
  - Editing is limited to **15 minutes** after posting  
- 📎 **Image Upload** (optional for posts/threads)  
- 📱 **Responsive UI** powered by TailwindCSS  

---

## 🛠️ Tech Stack
- [Laravel 12](https://laravel.com/) (PHP Framework)  
- [MySQL](https://www.mysql.com/) (Database)  
- [TailwindCSS](https://tailwindcss.com/) (UI Styling)  
- [Alpine.js](https://alpinejs.dev/) (Lightweight Interactivity)  

---

## ⚙️ Installation
1. Clone the repository
   ```bash
   git clone https://github.com/Oqexip/Anonboard.git
   cd Anonboard
````

2. Install dependencies

   ```bash
   composer install
   npm install && npm run build
   ```

3. Create `.env` file and configure your database

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations & seeders

   ```bash
   php artisan migrate --seed
   ```

5. Start the development server

   ```bash
   php artisan serve
   ```
---

## 🤝 Contribution

Contributions are welcome!

1. Fork this repository
2. Create a new branch (`git checkout -b feature-name`)
3. Commit your changes (`git commit -m "Add new feature"`)
4. Push to your branch (`git push origin feature-name`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

💡 Built with ❤️ by **Oqexip** as a learning & sharing project.
