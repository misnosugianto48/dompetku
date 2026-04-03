# 📋 Dompetku — Development Plan

> Prioritized roadmap for personal use improvements.
> Last updated: 2026-03-18

---

## Phase 1: Essential Personal Use Improvements ⭐

These features fill the biggest daily-use gaps.

### 1.1 — Transfer Between Accounts
- Add a `transfer` type to transactions
- Create a dedicated transfer form (source → destination account)
- Auto-debit source account and credit destination account
- Show transfers distinctly in transaction list (different badge/color)
- Edit amout each account

### 1.2 — Transaction Edit
- Add `edit` / `update` routes and view for transactions
- Reverse the old account balance change and apply the updated one
- Preserve asset linkage when editing

### 1.3 — Category Management UI
- Add CRUD routes & views for categories (`/categories`)
- Allow custom icon and color per category
- Inline add/edit with modal or dedicated page

### 1.4 — Transaction Notes & Search
- Add a `notes` text column to transactions (migration)
- Add a full-text search bar to the transactions list page
- Search across description, notes, and category name

---

## Phase 2: Financial Insights 📊

Add visual charts and budgeting so the dashboard becomes more actionable.

### 2.1 — Dashboard Charts
- Monthly income vs expense bar chart (last 6 months)
- Expense breakdown pie/donut chart by category
- Use Chart.js or ApexCharts (lightweight, no heavy JS framework needed)

### 2.2 — Budget System
- New `budgets` table: `category_id`, `amount`, `period` (monthly/weekly)
- Budget model + migration + factory + seeder
- Dashboard widget showing budget progress bars per category
- Alert/highlight when a category exceeds its budget

### 2.3 — Net Worth Tracker
- Dashboard card showing total net worth (accounts + assets)
- Monthly net worth history (store snapshots or compute from transactions)
- Simple line chart of net worth over time

---

## Phase 3: Automation & Convenience ⚡

Reduce manual work for recurring operations.

### 3.1 — Recurring Transactions
- New `recurring_transactions` table: `frequency` (daily/weekly/monthly/yearly), `next_due_date`, `is_active`
- Scheduled artisan command to auto-create transactions from recurring templates
- UI to create, pause, and delete recurring entries

### 3.2 — Quick Add from Dashboard
- Inline income/expense form directly on dashboard (modal or slide-over)
- Pre-filled with today's date, last-used account

### 3.3 — Duplicate Transaction
- "Duplicate" button on each transaction row
- Pre-fills the create form with copied data (date set to today)

---

## Phase 4: Data Safety & Multi-Device 🔐

### 4.1 — Authentication (Single-User)
- Add Laravel Breeze (lightweight auth scaffolding)
- Protect all routes with `auth` middleware
- Single-user setup: seeder creates one user, disable registration (optional)

### 4.2 — Data Export & Import
- Export all transactions as CSV/Excel
- Import transactions from CSV (with field mapping UI)
- Backup/restore database as JSON dump

### 4.3 — Account Edit
- Add `edit` / `update` routes for accounts
- Allow renaming, changing type, and manual balance correction

---

## Phase 5: Polish & UX Enhancements ✨

### 5.1 — Dark Mode
- Tailwind dark mode toggle (class-based strategy)
- Persist preference in `localStorage`
- Apply dark variants to all views

### 5.2 — Dashboard Date Range Picker
- Add interactive date range picker with presets (This Week, This Month, Last Month, etc.)
- Apply to income/expense summary cards and recent transactions

### 5.3 — Mobile Responsive Improvements
- Bottom navigation bar for mobile
- Swipe-to-delete on transaction rows
- Sticky header with scroll behavior

### 5.4 — Multi-Currency Support
- Add `currency` field to accounts
- Show amounts in original currency with IDR equivalent
- Store and use exchange rates

---

## Phase 6: Advanced Features 🚀

### 6.1 — Tags / Labels
- Tag system for transactions (many-to-many)
- Filter transactions by tag
- Useful for tracking specific purposes (e.g., "vacation", "project X")

### 6.2 — Savings Goals
- Create goals with target amount and deadline
- Link contributions to specific goals
- Visual progress tracker on dashboard

### 6.3 — Receipt / Attachment Upload
- File upload on transaction create/edit
- Store in `storage/app/receipts`
- Thumbnail preview in transaction detail

---

## Suggested Implementation Order

| Priority | Phase | Effort  | Impact |
|----------|-------|---------|--------|
| 🥇 1st   | 1.1 Transfer Between Accounts | Low     | High   |
| 🥇 1st   | 1.2 Transaction Edit           | Low     | High   |
| 🥇 1st   | 1.3 Category Management UI     | Low     | High   |
| 🥈 2nd   | 2.1 Dashboard Charts           | Medium  | High   |
| 🥈 2nd   | 1.4 Transaction Notes & Search | Low     | Medium |
| 🥈 2nd   | 3.2 Quick Add from Dashboard   | Low     | Medium |
| 🥈 2nd   | 3.3 Duplicate Transaction      | Low     | Medium |
| 🥉 3rd   | 2.2 Budget System              | Medium  | High   |
| 🥉 3rd   | 4.1 Authentication             | Medium  | High   |
| 🥉 3rd   | 2.3 Net Worth Tracker          | Medium  | Medium |
| 4th      | 3.1 Recurring Transactions     | Medium  | High   |
| 4th      | 4.2 Data Export & Import       | Medium  | Medium |
| 4th      | 4.3 Account Edit               | Low     | Medium |
| 5th      | 5.1 Dark Mode                  | Medium  | Medium |
| 5th      | 5.2 Dashboard Date Range Picker| Low     | Medium |
| 5th      | 5.3 Mobile Responsive          | Medium  | Medium |
| Later    | 5.4 Multi-Currency             | High    | Medium |
| Later    | 6.1 Tags / Labels              | Medium  | Medium |
| Later    | 6.2 Savings Goals              | Medium  | Medium |
| Later    | 6.3 Receipt Upload             | Medium  | Low    |
