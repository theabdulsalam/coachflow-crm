# CoachFlow CRM

> A professional Lead Management & Follow-up CRM for Coaches, Consultants, Mentors, and Solo Service Businesses.

Built with: **PHP (Core) · MySQL · Bootstrap 5 · Vanilla JS · Chart.js**  
Hosting: cPanel shared hosting compatible — no Composer, no framework required.

---

## Quick Start

### 1. Database Setup
1. Create a MySQL database (e.g. `coachflow_crm`) in phpMyAdmin
2. Import `database.sql` — this creates tables and inserts 15 demo leads

### 2. Configure Connection
Edit `includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'coachflow_crm');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

### 3. Configure BASE_URL
Every page has `define('BASE_URL', '/coachflow-crm/');` at the top.  
If your folder name is different (e.g. the root), change this accordingly:
- Root install: `define('BASE_URL', '/');`
- Subdirectory: `define('BASE_URL', '/my-folder/');`

### 4. Upload & Access
Upload all files to your hosting via FTP/File Manager and visit:
```
https://yourdomain.com/coachflow-crm/
```

---

## Demo Login
| Field    | Value                    |
|----------|--------------------------|
| Email    | admin@coachflow.com      |
| Password | password                 |

---

## Pages

| File             | Purpose                          |
|------------------|----------------------------------|
| `login.php`      | Login with session auth          |
| `dashboard.php`  | Overview stats + charts          |
| `leads.php`      | Full leads table + search/filter |
| `lead_form.php`  | Add / Edit lead (dual mode)      |
| `followups.php`  | Overdue / Today / Upcoming       |
| `reports.php`    | Analytics with 3 chart types     |
| `settings.php`   | Profile + password update        |
| `logout.php`     | Destroy session                  |

---

## Folder Structure
```
/coachflow-crm
├── index.php
├── login.php
├── dashboard.php
├── leads.php
├── lead_form.php
├── followups.php
├── reports.php
├── settings.php
├── logout.php
├── .htaccess
├── database.sql
├── /includes
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
└── /assets
    ├── /css/style.css
    └── /js/main.js
```

---

## Security Features
- PDO prepared statements (SQL injection protection)
- `password_hash` / `password_verify` for passwords
- Session-based authentication on every protected page
- `htmlspecialchars` on all output (XSS protection)
- `.htaccess` blocks direct access to `includes/`

---

## Built by Abdul Salam
Portfolio project — demonstrates custom CRM development for coaches & consultants.
