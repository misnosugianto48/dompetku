# 💰 Dompetku

**Dompetku** (Indonesian for "My Wallet") is a premium personal finance management web application built with Laravel. Track your income, expenses, accounts, assets, and budgets — all in one secure, unified platform.

## ✨ Core Features

### 📊 Dashboard & Insights (Phase 2 & 3)

- **Interactive Charts** — Real-time Cash Flow and Expense Breakdown visualizations using Chart.js.
- **Net Worth Tracking** — Automated historical wealth scale trajectory with interactive area charts.
- **Budget Monitoring** — Visual progress tracking per category with automated limits and alerts.
- **Quick Add** — Streamlined transaction recording directly from the dashboard.

### 💸 Transaction & Account Management (Phase 1)

- **Multi-Account Support** — Manage Banks, Cash, and E-Wallets with custom identifiers.
- **Transfers** — Seamlessly move funds between accounts with automated balance adjustments.
- **High-Precision Decimals** — Robust handling of currency values using decimal casting.
- **Advanced Filtering** — Search transactions by description, notes, categories, or date ranges.

### 🔄 Automation & Safety (Phase 3 & 4)

- **Authentication & Google OAuth** — Secure private access powered by Laravel Breeze and Google OAuth via Laravel Socialite, featuring auto-linkage and secure registration.
- **Recurring Transactions** — Set up daily, weekly, monthly, or yearly automated entries.
- **Daemon Processing** — Scheduled background commands to process recurring entries silently.
- **CSV Import/Export** — Move your data in and out of the system with robust mapping logic.
- **Automated Reports** — Scheduled PDF financial summaries dispatched directly to your email.

## 🛠 Tech Stack & Standards

- **Framework:** PHP 8.2+, [Laravel 12](https://laravel.com) (Latest)
- **Database:** PostgreSQL (Optimized for Neon/Cloud environments)
- **Frontend:** [Tailwind CSS v4](https://tailwindcss.com/) (Next-gen), Vite 7, Blade, Alpha.js
- **Visualization:** Chart.js
- **Reporting:** DomPDF (PDF Generation)
- **Testing:** [Pest v3](https://pestphp.com/) (Modern testing framework)
- **Formatting:** Laravel Pint (Strict PSR-12+ standards)

## 🔐 Security & Reliability

Dompetku is built with a security-first mindset, adhering to modern industry standards:

- **Authentication:** Protected by standard-grade middleware and secure hashing.
- **Mass Assignment Protection:** All models use `$fillable` whitelisting to prevent unauthorized data injection.
- **CSRF & XSS Protection:** Native Laravel safeguards applied across all forms and views.
- **Database Atomicity:** Critical operations (Transfers, Recurring Entries) are wrapped in **SQL Transactions** to prevent data inconsistency.
- **Input Validation:** Strict `FormRequest` validation for every incoming request.

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- PostgreSQL database

### Installation

1. **Clone & Install**

   ```bash
   git clone https://github.com/misnosugianto48/dompetku.git
   cd dompetku
   composer install
   npm install
   ```

2. **Configure Environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your database credentials, Google OAuth keys, and SMTP settings for reports:
   ```env
   GOOGLE_CLIENT_ID=your-google-client-id
   GOOGLE_CLIENT_SECRET=your-google-client-secret
   GOOGLE_REDIRECT_URI="${APP_URL}/login/google/callback"
   ```

3. **Database Initialization**

   ```bash
   php artisan migrate --seed
   ```

   *Note: Default credentials are provided in `DatabaseSeeder.php`.*

4. **Build & Run**

   ```bash
   npm run build
   php artisan serve
   ```

## 🔄 CI/CD & Deployment Workflow System

Dompetku includes an automated CI/CD pipeline powered by GitHub Actions. Every push to the `main` branch triggers an automated testing and deployment workflow.

### Pipeline Steps (`.github/workflows/deploy.yml`)
1. **Pest Test Execution:** Spawns a PostgreSQL service container, installs dependencies, disables SSL mode (`DB_SSLMODE: disable`), runs database migrations, and executes all Pest feature and unit tests.
2. **Production Build:** Installs production composer dependencies (with `--no-dev --optimize-autoloader`) and compiles assets using Vite (`npm run build`).
3. **Rsync Deployment:** Syncs the codebase to the production shared hosting server using Rsync over SSH, excluding development-only files (e.g. `.git`, `.github`, `node_modules`, `.env`, and `tests/`).
4. **Environment Secrets Injection:** Dynamically injects/updates configuration variables (like Google OAuth credentials) from GitHub Secrets into the production `.env` file.
5. **Post-Deployment Optimizations:** Runs database migrations (`migrate --force`) and caches Laravel configurations, routes, views, and events to ensure maximum performance.

### Required GitHub Secrets
To enable the deployment workflow, configure the following secrets in your GitHub repository (**Settings > Secrets and variables > Actions**):

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `SSH_PRIVATE_KEY` | SSH Private Key (without passphrase) authorized on the server | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `REMOTE_HOST` | Target server IP or domain | `123.45.67.89` |
| `REMOTE_USER` | SSH Username for the server | `u12345678` |
| `REMOTE_TARGET_DIR`| Target directory path on the server | `/home/u12345678/public_html` |
| `REMOTE_PORT` | SSH Port (optional, defaults to `22`) | `2222` |
| `GOOGLE_CLIENT_ID` | Google OAuth Client ID | `12345-abcde.apps.googleusercontent.com` |
| `GOOGLE_CLIENT_SECRET` | Google OAuth Client Secret | `GOCSPX-abcdef...` |
| `GOOGLE_REDIRECT_URI` | Google OAuth Callback URL | `https://yourdomain.com/login/google/callback` |

## 📁 Architecture Overview

- **`app/Actions`** — Encapsulated business logic for complex operations like transaction creation and asset adjustments.
- **`app/Console/Commands`** — Background workers for processing recurring transactions and sending reports.
- **`app/Http/Requests`** — Centralized validation rules for maintaining data integrity.
- **`app/Mail`** — Mailable classes for automated PDF report delivery.
- **`resources/views/components`** — Reusable UI primitives for forms, navigation, and layouts.

## 🤝 License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
