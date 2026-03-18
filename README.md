# 💰 Dompetku

**Dompetku** (Indonesian for "My Wallet") is a personal finance management web application built with Laravel. Track your income, expenses, accounts, and assets — all in one place.

## ✨ Features

- **Dashboard** — Overview of total balance, monthly income & expenses, asset value, and recent transactions
- **Transaction Management** — Record income & expense transactions with categories
- **Account Management** — Manage multiple accounts (bank, cash, e-wallet, etc.) with custom icons and colors
- **Asset Tracking** — Track assets with purchase price, current price, and price history
- **Reports** — Filter financial reports by period (daily, weekly, monthly, yearly) with category breakdowns
- **PDF Export** — Export financial reports as PDF

## 🛠 Tech Stack

- **Backend:** PHP 8.2, Laravel 12
- **Database:** PostgreSQL (Neon)
- **Frontend:** Blade templates, Tailwind CSS v4, Vite
- **PDF Generation:** barryvdh/laravel-dompdf
- **Testing:** Pest v3

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- PostgreSQL database

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/misnosugianto48/dompetku.git
   cd dompetku
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=your-db-host
   DB_PORT=5432
   DB_DATABASE=dompetku
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```

4. **Run migrations and seed the database**
   ```bash
   php artisan migrate --seed
   ```

5. **Build frontend assets**
   ```bash
   npm run build
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```
   The app will be available at `http://localhost:8000`.

### Development

For hot-reloading during development:

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── TransactionController.php
│   ├── AccountController.php
│   ├── AssetController.php
│   └── ReportController.php
├── Models/
│   ├── Account.php
│   ├── Asset.php
│   ├── AssetPriceHistory.php
│   ├── Category.php
│   └── Transaction.php
resources/views/
├── layouts/
├── dashboard.blade.php
├── transactions/
├── accounts/
├── assets/
└── reports/
```

## 🤝 Contributing

Contributions are welcome and greatly appreciated! Whether it's fixing bugs, adding features, improving documentation, or suggesting ideas — all contributions are valued.

### How to Contribute

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/your-feature-name`)
3. **Commit** your changes (`git commit -m 'feat: add some feature'`)
4. **Push** to the branch (`git push origin feature/your-feature-name`)
5. **Open** a Pull Request

### Contribution Ideas

- 🐛 Bug fixes and improvements
- 🌐 Multi-language / localization support
- 🔐 Authentication & multi-user support
- 📊 Chart visualizations for reports
- 📱 Mobile responsive enhancements
- 🧪 Additional test coverage

> Feel free to open an [issue](https://github.com/misnosugianto48/dompetku/issues) to discuss any changes before submitting a PR.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
