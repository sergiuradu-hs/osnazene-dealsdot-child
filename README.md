# OsnaZene child theme

Custom WordPress child theme for the OsnaZene site, built on top of the `Dealsdot` parent theme.

This theme contains:

- custom branding and typography overrides in `style.css`
- custom header and footer templates
- reusable WPBakery elements under `wpb_shortcodes/`
- site-specific styling and assets used by the OsnaZene frontend

## Parent theme

This is a child theme of `dealsdot`.

Required:

- WordPress
- the `Dealsdot` parent theme installed in `wp-content/themes/dealsdot`
- WPBakery Page Builder
- Advanced Custom Fields (ACF)

Some theme features also assume WooCommerce is active, because the inherited header/footer still reference cart and shop helpers from the parent theme.

## Local development

This repository includes a Docker-based local WordPress setup.

From the project root:

```bash
docker compose up -d
```

Useful endpoints:

- WordPress: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

If you need WP-CLI:

```bash
docker compose run --rm wpcli "wp theme list"
```

The WordPress files are mounted from `./wp`, so edits to this theme are reflected immediately in the running container.

## Theme structure

```text
dealsdot-child/
├── functions.php
├── header.php
├── footer.php
├── style.css
├── style-rtl.css
├── assets/
│   └── images/
└── wpb_shortcodes/
		├── cta-two-button/
		├── drzava-tax-grid/
		└── osnazena-card/
```

### Key files

- `style.css` — main child theme stylesheet, design tokens, typography, layout, and component styles
- `functions.php` — enqueues the parent theme stylesheet and registers custom WPBakery elements
- `header.php` — custom OsnaZene header with logo, CTA buttons, and primary navigation
- `footer.php` — custom footer driven by ACF option fields and WordPress menus
- `wpb_shortcodes/*` — site-specific reusable content blocks for WPBakery

## Custom functionality

### 1. Custom header

The header replaces the default parent theme output and adds:

- OsnaZene-specific layout classes
- configurable CTA buttons loaded from ACF option fields
- support for the parent theme logo settings and navigation walkers

Expected ACF option group usage includes `header_options` with fields for the two CTA buttons.

### 2. Custom footer

The footer is also overridden in the child theme and pulls its content from ACF option fields.

It expects option data such as:

- `footer_heading`
- `footer_description`
- `footer_button_text`
- `footer_button_url`
- `footer_social_text`
- `footer_social`
- `footer_payment`
- `footer_copyright`

It also renders three WordPress menus by ID (`10`, `11`, `12`), so those menus should exist in the target environment.

### 3. WPBakery elements

Custom elements are loaded automatically from `wpb_shortcodes/*/element.php` and registered during `vc_after_init`.

Available elements:

- `Two Button CTA` (`wpb_cta_two_button`)
	- heading
	- rich text content
	- two button labels and links

- `Država Tax Grid` (`wpb_tax_drzava_nav`)
	- renders a grid of taxonomy terms
	- defaults to taxonomy slug `drzava`
	- supports term ordering via ACF field `order`
	- supports term image fields such as `zastava` and `slika_drzave`

- `Osnažena Card` (`wpb_osnazena_card`)
	- renders a card for a single `osnazena` post
	- supports WPBakery autocomplete lookup
	- reads ACF fields such as `naslovna_slika` and `ime_i_prezime_vlasnice`
	- shows the first `delatnost` taxonomy term as the category label

## Content model assumptions

This theme assumes the site already contains project-specific content types and fields, including:

- custom post type `osnazena`
- taxonomy `drzava`
- taxonomy `delatnost`
- ACF option pages and option fields
- ACF fields attached to terms and posts used by the custom elements

Those data structures are not registered in this child theme, so they must come from plugins, theme options, or custom code elsewhere in the project.

## Working on the theme

Recommended workflow:

1. make style changes in `style.css`
2. update template overrides in `header.php` or `footer.php` when changing global layout
3. update shortcode behavior in the relevant `wpb_shortcodes/<slug>/element.php`
4. update markup in the matching `templates/template.php` file when changing output HTML

## Notes for maintainers

- Keep child-theme logic in this theme instead of modifying the parent theme directly.
- If you add a new WPBakery element, follow the existing structure under `wpb_shortcodes/<slug>/`.
- Prefer using ACF option fields for editor-managed header/footer content.
- Be careful when changing menu IDs in `footer.php`; they are hardcoded.
- The theme imports Google Fonts directly from `style.css`, which may matter for performance or privacy reviews.

## Screenshot

WordPress uses `screenshot.png` in this directory for the theme preview in the admin.

## License

This child theme inherits the licensing context of the parent WordPress/theme ecosystem. Review the parent theme license before redistributing.
