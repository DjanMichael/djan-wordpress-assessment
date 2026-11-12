# Front-end Assessment: Estate Showcase theme

A WordPress landing page built from the two designs:

- **Design 1:** "Our featured listing", a three-column grid of property cards.
- **Design 2:** "Google Reviews", six reviews on a three-column slider.

Theme: `wp-content/themes/estate-showcase`

![Theme screenshot](wp-content/themes/estate-showcase/screenshot.jpg)

## Requirements

| Requirement | How it is met |
| --- | --- |
| Built with Bootstrap 5 | Bootstrap **5.3.3**, loaded in `functions.php`. The layout uses the Bootstrap grid, cards, buttons and carousel. |
| Standard Bootstrap navbar | `header.php` uses `navbar navbar-expand-lg` with the collapse toggler. `inc/class-estate-showcase-navwalker.php` outputs the WordPress menu as `nav-item`/`nav-link`. |
| Header and footer | `header.php` and `footer.php`. |
| Responsive on all devices | The listings show 1, 2 or 3 per row. The reviews slider shows 1 per slide on phones, 2 on tablets and 3 on desktop. |
| Font Awesome icons | Font Awesome **6.5.2**. |
| Open Sans Google font | Loaded from Google Fonts. |
| SEO friendly | One `<h1>`, semantic HTML5, meta description, Open Graph tags, JSON-LD structured data, XML sitemap, and image `alt` text with set dimensions. |
