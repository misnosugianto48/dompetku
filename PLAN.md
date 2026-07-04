# 📋 Dompetku — Development Plan

> Prioritized roadmap for personal use improvements.
> Last updated: 2026-07-04

---

## Phase 1: Essential Personal Use Improvements ⭐ (Done)

- [x] **1.1 — Transfer Between Accounts**
  - Add a `transfer` type to transactions
  - Create a dedicated transfer form (source → destination account)
  - Auto-debit source account and credit destination account
  - Show transfers distinctly in transaction list (different badge/color)
- [x] **1.2 — Transaction Edit**
  - Add `edit` / `update` routes and view for transactions
  - Reverse the old account balance change and apply the updated one
  - Preserve asset linkage when editing
- [x] **1.3 — Category Management UI**
  - Add CRUD routes & views for categories (`/categories`)
  - Allow custom icon and color per category
- [x] **1.4 — Transaction Notes & Search**
  - Add a `notes` text column to transactions
  - Add a full-text search bar to the transactions list page
  - Search across description, notes, and category name

---

## Phase 2: Financial Insights 📊 (Done)

- [x] **2.1 — Dashboard Charts**
  - Monthly income vs expense bar chart (last 6 months)
  - Expense breakdown donut chart by category
  - Use Chart.js (adapts automatically to light/dark themes)
- [x] **2.2 — Budget System**
  - New `budgets` table, model + migration + factory + seeder
  - Dashboard widget showing budget progress bars per category
  - Alert/highlight when a category exceeds its budget
- [x] **2.3 — Net Worth Tracker**
  - Dashboard card showing total net worth (accounts + assets)
  - Monthly net worth history line chart

---

## Phase 3: Automation & Convenience ⚡ (Done)

- [x] **3.1 — Recurring Transactions**
  - New `recurring_transactions` table with frequency logic
  - Scheduled artisan command to auto-create transactions
  - UI to create, pause, and delete recurring entries
- [x] **3.2 — Quick Add from Dashboard**
  - Inline income/expense form directly on dashboard
  - Pre-filled with today's date, automatically updates categories based on transaction type, and returns back to the dashboard upon saving
- [x] **3.3 — Duplicate Transaction**
  - "Duplicate" button on each transaction row to pre-fill the create form
- [x] **3.4 — 📧 Automatic Monthly Financial Report (Resend Email Integration)**
  - PDF report containing monthly summaries and metrics
- [x] **3.5 — User Settings**
  - Enable/disable email reports, choose send date, etc.

---

## Phase 4: Data Safety & Multi-Device 🔐 (Done)

- [x] **4.1 — Authentication (Single-User & Google OAuth)**
  - Secure private access powered by Laravel Breeze and Google OAuth via Laravel Socialite
- [x] **4.2 — Data Export & Import**
  - Export transactions as CSV
  - Import transactions from CSV
- [x] **4.3 — Account Edit**
  - Allow renaming, changing type, and manual balance correction

---

## Phase 5: Polish & UX Enhancements ✨ (In Progress)

- [x] **5.1 — Dark Mode**
  - Tailwind dark mode toggle (class-based strategy)
  - Persist preference in `localStorage`
  - Global CSS-based theme overrides for high-contrast on all views and chart elements
- [x] **5.2 — Dashboard Date Range Picker**
  - Add interactive date range picker with presets (This Week, This Month, Last Month, etc.)
- [ ] **5.3 — Mobile Responsive Improvements (Partial)**
  - [x] Bottom navigation bar for mobile with floating primary Add button
  - [ ] Swipe-to-delete on transaction rows
  - [ ] Sticky header with scroll behavior
- [ ] **5.4 — Multi-Currency Support**
  - Add `currency` field to accounts
  - Show amounts in original currency with IDR equivalent
  - Store and use exchange rates

---

## Phase 6: Advanced Features 🚀 (Done)

- [x] **6.1 — Tags / Labels**
  - Tag system for transactions (many-to-many)
  - Filter transactions by tag
- [x] **6.2 — Savings Goals**
  - Create goals with target amount and deadline, and track progress
- [x] **6.3 — Receipt / Attachment Upload**
  - File upload on transaction create/edit (local storage or Cloudinary cloud storage driver)
  - Automated physical file deletion/cleanup on edit or deletion
