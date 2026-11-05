# WordPress Assessments

Two **separate WordPress projects** in one repository, one for each assessment. Each folder is a standalone WordPress site with its own database, theme and deployment README.

| Folder | Assessment | Theme | What it shows |
| --- | --- | --- | --- |
| [`frontend-assessment/`](frontend-assessment/README.md) | WordPress Front-end Development | `estate-showcase` | Bootstrap 5 landing page with a featured listings grid and a Google reviews slider (Open Sans, Font Awesome, SEO) |
| [`backend-assessment/`](backend-assessment/README.md) | WordPress Development (back-end) | `jobs-board` | "Jobs" custom post type with Job Title, Salary and Location fields, a jobs archive and a single job view, without plugins |

Both themes are lightweight classic themes written for these tasks. Neither project uses any plugins.

## What is in git

Each project folder is a WordPress root, but only the work itself is committed:

```
wordpress-assessments/
├── README.md                         ← this file
├── frontend-assessment/
│   ├── README.md                     ← run locally + deploy
│   ├── setup/                        ← demo content script, local router
│   └── wp-content/themes/estate-showcase/
└── backend-assessment/
    ├── README.md                     ← run locally + deploy
    ├── setup/
    └── wp-content/themes/jobs-board/
```

WordPress core, `wp-config.php`, uploads and the bundled default themes/plugins are excluded by each project's `.gitignore`. Download core into the folder and follow that project's README.

## Quick start

Requirements: PHP 7.4+, MySQL 5.7+ or MariaDB 10.4+, WordPress 6.4+ (built and tested on WordPress 7.1.4 / PHP 8.3).

For **each** project folder:

1. Put WordPress core in the folder (`wp core download --skip-content`).
2. Create its database and `wp-config.php`, then install WordPress.
3. Activate its theme and run `wp eval-file setup/demo-content.php`.
4. Serve it, e.g. `php -S localhost:8091 setup/router.php` for the front end and `php -S localhost:8092 setup/router.php` for the back end.

The full steps, plus how to deploy each theme to a live site, are in [frontend-assessment/README.md](frontend-assessment/README.md) and [backend-assessment/README.md](backend-assessment/README.md).
