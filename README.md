# 🏠 Room Rental Manager

A Laravel-based web application to manage tenants, track electricity and water usage, and generate monthly invoices in PDF format.

---

## 🚀 Features

- 👤 Tenant management (CRUD)
- 📅 Invoice generation (Monthly, per tenant)
- ⚡ Water & electricity tracking
- 🧾 Generate and download invoices as PDFs
- 📊 Admin dashboard (optional)
- 📂 Clean and minimal UI with Tailwind CSS

---

## 🛠️ Requirements

- PHP 8.1+
- Composer
- Laravel 10+
- SQLite / MySQL
- Node.js and npm (for frontend assets)

---

## ⚙️ Installation

```bash
git clone https://github.com/yourusername/room-rental-manager.git
cd room-rental-manager

composer install
cp .env.example .env
php artisan key:generate

# Configure your .env database connection (SQLite/MySQL)
php artisan migrate

npm install && npm run dev
php artisan serve
