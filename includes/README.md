# BP Landings

Landing pages custom post type with full Elementor support and root-level URLs. Designed for standalone landing pages (state pages, campaign pages) that need their own navigation, styles, and layout.

## Features

- Full Elementor page builder support (Canvas, Full Width templates)
- Root-level URLs — `/colorado/` instead of `/landing/colorado/`
- Two source modes: WordPress (Elementor-built) or Directory (static files)
- Page-like behavior: hierarchical, revisions, template selector
- Admin columns: Image, Title, URL, Date
- Registers as Starter Dashboard (BP Hub) addon

## Post Type

### Landings

Public, hierarchical post type behaving like WordPress pages.

- **Slug**: root-level (no prefix)
- **Supports**: title, editor, thumbnail, page-attributes, revisions, Elementor
- **Templates**: Default, Elementor Canvas, Elementor Full Width, Theme templates
- **Capability type**: page (same permissions as pages)

## ACF Fields

### Source (button group)

Switch between two modes:

- **WordPress** (default) — page built with Elementor, served by WordPress
- **Directory** — points to a static HTML/PHP landing in a root folder on the server

### Directory URL

Only visible when Source = Directory. Full URL of the static landing page (e.g. `https://buspatrol.com/colorado/`).

### Directory Name

Only visible when Source = Directory. Root directory name on the server (e.g. `colorado`, `florida`).

## URL Routing

Landing pages use root-level URLs without any prefix. The routing priority is:

1. WordPress Pages — if a page exists with the slug, it takes priority
2. Leadership profiles — if a leadership post claims the slug, it takes priority
3. Landings — resolved last to avoid conflicts

This means a landing with slug `colorado` is accessible at `/colorado/`.

## Usage

### Creating a WordPress Landing

1. Go to Landings → Add New Landing
2. Enter a title (this becomes the URL slug)
3. Set Source to "WordPress" (default)
4. Click "Save Draft"
5. Click "Edit with Elementor" to build the page
6. Choose a template (Canvas recommended for standalone landings)
7. Publish when ready

### Registering a Static Directory Landing

1. Go to Landings → Add New Landing
2. Enter the landing name (e.g. "Colorado")
3. Set Source to "Directory"
4. Enter the Directory URL (e.g. `https://buspatrol.com/colorado/`)
5. Enter the Directory Name (e.g. `colorado`)
6. Publish

This creates a record in the admin for tracking purposes. The actual page is still served from the static directory.

## Admin List

The landing list shows custom columns:

- **Image** — featured image thumbnail
- **Title** — landing name with edit/view links
- **URL** — for Directory landings, shows the static URL as a clickable link
- **Date** — publish/modified date

## Requirements

- Advanced Custom Fields PRO
- Elementor Pro (for page building)
- Starter Dashboard (optional, for BP Hub addon integration)
