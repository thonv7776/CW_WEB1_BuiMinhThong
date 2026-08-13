# Student Q&A — COMP1841 Coursework

A PHP/MySQL CRUD web application built for **COMP1841 – Web Programming 1** (University of Greenwich, 2025/26). It is a small, self-contained "student Stack Overflow": students register, log in, and post questions tagged to a module, with an optional screenshot. Admins manage users, modules, and contact messages through a separate admin area.

Built with **PHP PDO** (no MySQLi) and **MySQL**, following the referential-integrity and prepared-statement requirements set out in the coursework brief.

## Features

**Public site**
- View all questions (list, newest first, with author, module, date, image)
- Register with email verification (6-digit OTP sent via PHPMailer)
- Log in / log out (session-based)
- Ask a question — title, content, module (dropdown), optional screenshot
- Edit / delete your own questions only
- Contact form (validated, stored in the database)

**Admin area** (separate login, `admin-login.php`)
- Manage users (add / edit / delete), blocked by foreign key if a user still has questions
- Manage modules (add / edit / delete), blocked by foreign key if a module still has questions
- View and delete contact messages
- Activity log — every admin login/logout and content change is recorded

**Security / validation**
- PDO prepared statements throughout (`PDO::ATTR_EMULATE_PREPARES => false`)
- Passwords hashed with `password_hash()` (bcrypt) — never stored in plain text
- Image uploads validated by extension **and** real image bytes (`getimagesize()`), capped at 2 MB, stored under a randomly generated filename
- Server-side validation on every form; HTML5 `required` / `type="email"` on the client side
- Foreign keys use `ON DELETE RESTRICT` (questions → users/modules) and `ON DELETE SET NULL` (activity log → admins)

## Tech stack

| Layer | Technology |
|---|---|
| Server language | PHP (PDO, no MySQLi) |
| Database | MySQL / MariaDB (InnoDB, utf8mb4) |
| Front end | HTML5, CSS3 (no framework) |
| Email | PHPMailer 6.9 (SMTP, via Composer) |
| Local environment | WAMP / XAMPP |

## Project structure

```
cw_web1/
├── config/
│   └── config.php          # DB credentials, paths, .env loader
├── includes/
│   ├── db-connect.php       # PDO connection
│   ├── db-functions.php     # all queries (prepared statements)
│   ├── mailer.php           # PHPMailer wrapper (OTP emails)
│   ├── require-admin.php    # guards admin-only pages
│   ├── require-login.php    # guards student-only pages
│   ├── upload.php           # image upload validation + storage
│   └── validate.php         # shared validation helpers
├── public/                  # web root — every page is requested directly
│   ├── index.php, questions.php, questions-add.php, ...
│   └── admin-*.php          # admin area
├── templates/                # HTML fragments included by public/*.php
├── cw_web1.sql               # schema + demo data
├── composer.json             # PHPMailer dependency
├── .env.example               # SMTP config template
└── .env                       # SMTP config (gitignored — create your own)
```

## Setup (local, WAMP/XAMPP)

1. **Clone / copy the project** into your server's web root, e.g. `C:\wamp64\www\cw_web1\` (Apache document root must point at the `public/` folder inside it, or use the included `.htaccess`).

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Create the database:** open phpMyAdmin (or the MySQL CLI) and import `cw_web1.sql`. This creates the `cw_web1` database with all 6 tables and demo data.

4. **Configure the database connection:** edit `config/config.php` if your MySQL credentials differ from the WAMP defaults (`root` / no password):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'cw_web1');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
   Also update `BASE_URL` to match where you placed the project.

5. **Configure email (for OTP registration):** copy `.env.example` to `.env` and fill in real SMTP details (a Gmail App Password works well — see the comments in `.env.example`). If `.env` is missing, the site still runs; only the OTP email step will fail.

6. **Open the site:**
   - Public site: `http://localhost/cw_web1/public/`
   - Admin login: `http://localhost/cw_web1/public/admin-login.php`

## Demo / marking credentials

**Admin login** (seeded by `cw_web1.sql`):
```
Username: admin
Password: admin123
```

Student accounts can be created via the public **Register** page (email OTP required), or you can log in as one of the demo users already seeded in `cw_web1.sql`.


