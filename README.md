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

- **500 error after upload** — confirm PHP 8.1+ is selected and `.htaccess` uploaded (dot-files are hidden by default in File Manager → Settings → Show hidden files)
- **"Installation failed: SQLSTATE…"** — database name/user/password mismatch, or the user lacks privileges on the database
- **Pretty URLs 404** — set `RewriteBase` as described above
- **Locked out of admin** — wait 15 minutes (lockout) or reset the password hash in the database (`users` table) via phpMyAdmin
- **Errors after going live** — check `app/storage/error.log`

---

Built June 2026 for True Chain Infrastructure Company · TCIC
