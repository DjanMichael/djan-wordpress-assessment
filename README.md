# WordPress Assessments

Two **separate WordPress projects** in one repository, one for each assessment. Each folder is a standalone WordPress site with its own database, theme and README.

| Folder | Assessment | Theme | What it shows |
| --- | --- | --- | --- |
| [`frontend-assessment/`](frontend-assessment/README.md) | WordPress Front-end Development | `estate-showcase` | Bootstrap 5 landing page with a featured listings grid and a Google reviews slider (Open Sans, Font Awesome, SEO) |
| [`backend-assessment/`](backend-assessment/README.md) | WordPress Development (back-end) | `jobs-board` | "Jobs" custom post type with Job Title, Salary and Location fields, a jobs archive and a single job view, without plugins |

---

## Run it on your computer

Each project has a one-step installer, so you don't need WP-CLI, Composer, Node or Docker.

### What you need

- **PHP 7.4 or newer** on the command line. The `mysqli`, `zip` and `openssl` extensions are on by default in most installs. Check with `php -v`.
- **MySQL 5.7+ or MariaDB**, running.
- **Internet access**, because the installer downloads WordPress from wordpress.org.

If you don't have PHP and MySQL yet:
- **Windows:** [Laragon](https://laragon.org/) or [XAMPP](https://www.apachefriends.org/).
- **macOS:** `brew install php mysql && brew services start mysql`, or [MAMP](https://www.mamp.info/).

### Install and start (about 2 minutes)

**1. Get the code**

```bash
git clone https://github.com/DjanMichael/djan-wordpress-assessment.git
cd djan-wordpress-assessment
```

**2. Front-end assessment**

```bash
cd frontend-assessment
php setup/install.php
php -S localhost:8091 setup/router.php
```

Open **<http://localhost:8091>**. Leave this terminal running.

**3. Back-end assessment.** In a second terminal, from the `djan-wordpress-assessment` folder:

```bash
cd backend-assessment
php setup/install.php
php -S localhost:8092 setup/router.php
```

Open **<http://localhost:8092/jobs/>**.

To stop a site, press `Ctrl+C` in its terminal. To start it again later, run just the `php -S …` line.

The installer:
- downloads the latest WordPress and checks its SHA-1, leaving the theme folder alone;
- creates the database (`wpa_frontend` or `wpa_backend`) and `wp-config.php`;
- installs WordPress, activates the theme and adds the demo content.

It only touches that project folder and its database, and you can safely run it again.

### WP Admin login

Each site has its own local admin account:

| | Front-end | Back-end |
| --- | --- | --- |
| WP Admin | <http://localhost:8091/wp-admin/> | <http://localhost:8092/wp-admin/> |
| Username | `admin` | `admin` |
| Password | a random password shown at the end of the install | a random password shown at the end of the install |

The password is on the installer's last line, for example:

```
WP Admin:  http://localhost:8091/wp-admin/  (user: admin, password: 3f9c2a7d41be)
```

- **To choose your own password**, add it to the install command: `php setup/install.php --admin-pass=YOUR_PASSWORD`. Add `--admin-user=NAME` for a different username.
- **If you forget it**, run the installer again from that project's folder with a new one: `php setup/install.php --admin-pass=NEW_PASSWORD`. This sets the new password. It also re-applies the demo settings (site title, homepage, menu) but doesn't delete or duplicate any content. Without `--admin-pass`, a re-run leaves the login as it is.

### MySQL isn't "root" with no password?

Give the installer your details:

| Setup | Command |
| --- | --- |
| MAMP | `php setup/install.php --db-user=root --db-pass=root --db-port=8889` |
| Anything else | `php setup/install.php --db-host=127.0.0.1 --db-port=3306 --db-user=YOUR_USER --db-pass=YOUR_PASSWORD` |

`php setup/install.php --help` lists every option, including the database name, URL/port, admin login, and `--zip` for using a WordPress zip you already have.

### Port 8091 or 8092 already in use?

Install on a different port, then serve on that same port:

```bash
php setup/install.php --url=http://localhost:8095
php -S localhost:8095 setup/router.php
```

### Prefer Local, XAMPP htdocs or an existing WordPress site?

Use a separate WordPress site for each assessment:

1. Create a new WordPress site, e.g. in [Local](https://localwp.com/).
2. Copy the theme folder into that site's `wp-content/themes/`:
   - front end: `frontend-assessment/wp-content/themes/estate-showcase`
   - back end: `backend-assessment/wp-content/themes/jobs-board`
3. Activate it in **Appearance → Themes**.
4. Add the demo content. In Local, right-click the site → **Open site shell**, then run:
   ```bash
   wp eval-file "<path-to-repo>/frontend-assessment/setup/demo-content.php"
   ```
   Use `backend-assessment/...` for the back end. The script also sets the demo site title. Alternatively, follow the manual steps in the project README:
   - **Front end:** create a page with the *Featured Listings* template and set it as the homepage.
   - **Back end:** set Settings → Permalinks to "Post name", save, then add jobs under Jobs → Add New Job.

---

## What to look at

### Front-end assessment: <http://localhost:8091>

- **Navbar:** the standard Bootstrap 5 navbar. Narrow the window below 992 px to get the hamburger menu.
- **"Our featured listing"** (Design 1): three property cards with price, status badge, details, favourite button and availability.
- **"Google Reviews"** (Design 2): six reviews on a slider. Resize the window to see **3 per slide on desktop, 2 on tablets and 1 on phones**. Use the dots or swipe; autoplay pauses on hover.
- **View source:**
  - one `<h1>`, the meta description, Open Graph tags and JSON-LD;
  - Bootstrap 5.3.3 and Font Awesome 6.5.2, loaded with integrity hashes;
  - Open Sans from Google Fonts.
- **Code:** `frontend-assessment/wp-content/themes/estate-showcase/`. The [project README](frontend-assessment/README.md) maps each requirement to its file.

### Back-end assessment: <http://localhost:8092>

- **WP Admin → Jobs:** the list shows Job Title, Salary and Location columns.
- **Jobs → Add New Job:** the *Job details* fields sit under the title. Publish, edit, then trash or delete a job.
- **Front end:**
  - **<http://localhost:8092/jobs/>** is the archive, listing every job with all its fields;
  - click a job for the single view.
- **Code:** `backend-assessment/wp-content/themes/jobs-board/`:
  - `inc/jobs.php` has the post type, fields, meta box and saving;
  - `archive-job.php` and `single-job.php` are the two views.

---

## Troubleshooting

| Problem | Fix |
| --- | --- |
| `php` is not recognised | PHP isn't on your PATH. Use Laragon's terminal (Menu → Terminal) or XAMPP's Shell button, or call PHP by its full path, e.g. `C:\xampp\php\php.exe setup/install.php`. |
| `Can't connect to MySQL` | Start MySQL (Laragon/XAMPP control panel, or `brew services start mysql`), or pass your details with the `--db-*` options above. |
| `zip extension is missing` / `mysqli extension is missing` | Enable `extension=zip` / `extension=mysqli` in `php.ini`. On XAMPP that file is `C:\xampp\php\php.ini`. |
| Forgot the WP Admin password | From that project's folder: `php setup/install.php --admin-pass=NEW_PASSWORD`. |
| WordPress download fails (proxy or firewall) | Download <https://wordpress.org/latest.zip> yourself and run `php setup/install.php --zip=path/to/latest.zip`. |
| `/jobs/` returns 404 under Apache or nginx | WP Admin → Settings → Permalinks → **Save Changes**. |
| Start again from scratch | Drop that project's database, delete its `wp-config.php`, and run the installer again. |

---

## What is in git

Each project folder is a WordPress root, but only the work itself is committed:

```
djan-wordpress-assessment/
├── README.md                         ← this file
├── frontend-assessment/
│   ├── README.md                     ← details + deploying to a live site
│   ├── setup/                        ← install.php, demo-content.php, router.php
│   └── wp-content/themes/estate-showcase/
└── backend-assessment/
    ├── README.md
    ├── setup/
    └── wp-content/themes/jobs-board/
```

Each project's `.gitignore` keeps out WordPress core, `wp-config.php`, uploads and the bundled default themes/plugins. The installer adds core when you run it.

Built and tested on WordPress 7.1.4 with PHP 8.3 and MySQL 8. The themes need WordPress 6.4+ and PHP 7.4+.
