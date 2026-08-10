# True Chain Infrastructure Company — Corporate Website

A complete, self-contained PHP corporate website with a full content management
system. No frameworks, no Composer, no build step: upload, run the installer,
done. Built for GoDaddy shared hosting (any Apache + PHP 8.0+ host works).

---

## What you get

**Public website**
- Home with managed hero slider, group overview, statistics band, companies grid, ecosystem flow and call-to-action
- About the Group, Our Companies (plus a detail page per company), Leadership, Contact (working form), Privacy Policy (NDPA 2023 aligned), Terms of Use
- Fully responsive, SEO-ready (per-page meta, Open Graph, sitemap.xml, robots.txt), favicon and brand assets included

**Admin panel** (`/admin`)
- Dashboard with statistics and latest messages
- Pages: create/edit pages with a visual editor or raw HTML, menu placement, draft/publish, per-page SEO
- Hero Slider: slides with headline, text, two buttons, background image, ordering
- Static Blocks: editable content pieces used around the site (home sections, footer text)
- Companies: the group companies, their descriptions, icons and the website links the corporate site routes visitors to
- Leadership: profiles with photos or automatic monogram avatars
- Media Library: upload images safely (type-checked, renamed, isolated)
- Messages: contact form inbox with read/unread, reply-by-email, optional email notification
- Administrators: multiple admin accounts, lockout protection
- Settings: site name, logo, favicon, contact details, social links, SEO defaults, analytics snippet, maintenance mode

**Security, built in**
- Passwords hashed with bcrypt; login throttling and temporary lockouts
- CSRF tokens on every form; session hardening (HttpOnly, SameSite, fingerprint binding, idle timeout)
- All database access via prepared statements (no SQL injection)
- Output escaping throughout (no XSS from visitor input)
- Upload validation by real file content, randomized names, script execution disabled in `/uploads`
- `/app` directory fully blocked from the web; security headers; honeypot + rate limiting on the contact form

---

## Deploying to Railway (10 minutes)

Railway builds the included `Dockerfile` and runs the site on Apache + PHP 8.3.
There is **no installer to run**: the service reads its configuration from
environment variables and creates the database schema and starting content by
itself on first boot.

### 1. Create the service
1. Railway → **New Project** → **Deploy from GitHub repo** → pick this repository
2. Railway detects `Dockerfile` and builds it (`railway.json` pins this)

### 2. Add the database
1. In the project → **New** → **Database** → **Add MySQL**
   (choose MySQL, *not* PostgreSQL — the site's schema is written for MySQL)
2. Open the **web service** → **Variables** → **Add Variable Reference** and add
   `MYSQL_URL` from the MySQL service

### 3. Set the administrator variables
On the web service → **Variables**:

| Variable | Value |
|---|---|
| `ADMIN_EMAIL` | the address you will sign in with |
| `ADMIN_PASSWORD` | 10+ characters — used once, to create the account |
| `ADMIN_NAME` | e.g. `Osamede Evbakhavbokun` |
| `APP_KEY` | any long random string (keeps admins signed in across deploys) |

### 4. Keep uploaded media across deploys
The container filesystem is rebuilt on every deploy, so **without this step
every image uploaded in the media library disappears when you next deploy**.

Web service → **Settings** → **Volumes** → **Add Volume**, mount path
`/var/www/html/uploads`.

### 5. Go live
1. **Settings** → **Networking** → **Generate Domain** (or add your own)
2. Open the URL — the site is already seeded with content
3. Sign in at `/admin` with the `ADMIN_EMAIL` / `ADMIN_PASSWORD` above

TLS, HTTPS redirection and the `PORT` binding are handled by Railway and the
container entrypoint; nothing in `.htaccess` needs editing.

### Environment variables

| Variable | Default | Purpose |
|---|---|---|
| `MYSQL_URL` / `DATABASE_URL` | — | Full connection URL; the usual Railway route |
| `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD` | — | Connection parts, if you prefer them to a URL |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | — | Same, for non-Railway hosts |
| `DB_DRIVER` | `mysql` | Set to `sqlite` to run without a database server |
| `SQLITE_PATH` | `app/storage/tcic.sqlite` | Point at a mounted volume when using SQLite |
| `DB_PREFIX` | `tcic_` | Table prefix |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME` | — | First-boot administrator account |
| `APP_KEY` | derived | Session-binding secret; set it explicitly |
| `APP_DEBUG` | `false` | `true` shows stack traces — never on a live site |
| `SESSION_NAME`, `SESSION_IDLE` | `TCICSESS`, `3600` | Session cookie name, admin idle timeout |

Once seeded, `ADMIN_PASSWORD` is no longer consulted — change the password in
**Admin → Administrators** and remove the variable.

---

## Deploying to GoDaddy (10 minutes)

### 1. Choose a PHP version
In cPanel open **Select PHP Version** and choose **PHP 8.1** or newer.

### 2. Create the database
1. cPanel → **MySQL Databases**
2. Create a database (e.g. `tcic_site`)
3. Create a database user with a strong password
4. Add the user to the database with **All privileges**
5. Note the database name, username and password (GoDaddy prefixes both with your account name, e.g. `youraccount_tcic_site`)

### 3. Upload the site
1. cPanel → **File Manager** → `public_html`
2. Upload `tcic-website.zip` (or the contents of this `website/` folder)
3. Extract so that `index.php`, `install.php`, `.htaccess`, `app/`, `assets/`, `uploads/` sit directly inside `public_html`

### 4. Run the installer
1. Visit `https://yourdomain.com/install.php`
2. Requirements check should be all green
3. Enter the database details from step 2 (host: `localhost`)
4. Create your administrator account (10+ character password)
5. Click **Install website**

### 5. After installing
1. **Delete `install.php`** in File Manager (the installer locks itself, but removing it is best practice)
2. Enable SSL (cPanel → SSL/TLS, GoDaddy certificates are usually one click), then open `.htaccess` and remove the `#` from the two "Force HTTPS" lines
3. Sign in at `https://yourdomain.com/admin` and make it yours:
   - **Settings → Contact details**: real phone, email, address
   - **Companies**: set each company's real website URL and flip status to "Live" as platforms launch
   - **Settings → Social media**: your profiles
   - **Settings → SEO**: confirm defaults; add analytics when ready

### Installing in a subfolder?
Works automatically. If pretty URLs misbehave, open `.htaccess` and set
`RewriteBase /your-subfolder/`.

---

## Everyday editing

| You want to… | Go to |
|---|---|
| Change the logo or favicon | Admin → Settings → Branding |
| Change home page banners | Admin → Hero Slider |
| Edit home page text sections | Admin → Static Blocks |
| Edit About / Privacy / Terms | Admin → Pages |
| Add a brand-new page to the menu | Admin → Pages → New page → tick "Show in main menu" |
| Point "Registry" to its live site | Admin → Companies → edit → Website URL + status Live |
| Read contact form messages | Admin → Messages |
| Add another admin user | Admin → Administrators |
| Take the site offline briefly | Admin → Settings → Advanced → Maintenance mode |

**Editor tip:** content seeded by the installer uses designed HTML. Those
screens open in the **HTML** tab by default to protect the layout; simple text
edits are safe in either tab.

---

## Local preview (optional)

With PHP installed on your computer:

```bash
cd website
php -S localhost:8080 dev-router.php
```

Open http://localhost:8080, run the installer once (SQLite works for local
testing if MySQL isn't handy), and browse. `dev-router.php` is ignored by
Apache in production.

---

## Customisation notes

- Brand colours live at the top of `assets/css/site.css` (`--blue: #17579E`, `--navy: #0B1B33`, …) — sampled from the official logo
- Fonts: Sora (headings) + Inter (body) via Google Fonts, configured in `app/views/partials/header.php`
- Hero artwork: three brand-styled SVGs in `assets/img/` (`hero-1.svg` … `hero-3.svg`); replace from the media library at any time
- Email notifications use PHP `mail()`, which GoDaddy supports out of the box; enable in Settings → Contact details

## Troubleshooting

- **The installer keeps appearing instead of the website** — the site is not installed yet: `app/config.php` does not exist, so every URL is redirected to `install.php`. Complete the installer once and the redirect stops. (If you already installed, `app/config.php` was deleted or the `app/` folder was not uploaded completely.)
- **"PDO MySQL driver — Fail"** — the `pdo_mysql` extension is off for your PHP version. cPanel → **Select PHP Version** → **Extensions** → tick `pdo_mysql` (shown as `nd_pdo_mysql` on some plans) → save → reload `install.php`. If you cannot enable it, set the database type to **SQLite** and install now; the site runs fully on SQLite.
- **500 error after upload** — confirm PHP 8.1+ is selected and `.htaccess` uploaded (dot-files are hidden by default in File Manager → Settings → Show hidden files)
- **"Installation failed: SQLSTATE…"** — database name/user/password mismatch, or the user lacks privileges on the database
- **Pretty URLs 404** — set `RewriteBase` as described above
- **Locked out of admin** — wait 15 minutes (lockout) or reset the password hash in the database (`users` table) via phpMyAdmin
- **Errors after going live** — check `app/storage/error.log`

---

Built June 2026 for True Chain Infrastructure Company · TCIC
