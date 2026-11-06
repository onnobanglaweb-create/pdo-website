# Editor guide

Everything here happens in the WordPress admin. You never need to touch code.

## Where things live

| I want to change… | Go to |
| --- | --- |
| Phone, address, e-mail, registration number, socials | **PDO Manager → Organisation** |
| Anything on the home page | **PDO Manager → Homepage** |
| Anything on one of the six inner pages | **PDO Manager → {page name}** |
| A programme, team member, partner, story, document, news item | The matching top-level menu (**PDO Programs**, **PDO Team**, …) |
| English / Bengali wording of buttons and labels | **PDO Manager → Translations** |
| Who wrote in through a form | **PDO Manager → Submissions** |
| The order of home-page sections | **PDO Manager → Reorder** or **Appearance → Customize → Homepage Sections** |
| Colours, fonts, spacing, logo | **Appearance → Customize** |
| Menu items | **Appearance → Menus** |

## The two kinds of content

**Settings** are the fixed parts of a page: the hero headline, the statistics
strip, the president's message. They live in the PDO Manager screens and have a
box for English and a box for Bengali.

**Records** are things you have many of: programmes, team members, partners,
stories, documents, news. They are ordinary WordPress posts with extra fields,
and they appear automatically wherever the design calls for them.

## Editing a page

1. Open the matching PDO Manager screen.
2. Each page is split into **sections** (Intro, History, Vision, Leadership …).
3. Every section has an **Enable** checkbox. Switch it off and the section
   disappears from the live page — nothing is deleted.
4. Fill the English box; fill the Bengali box when you have the translation. A
   blank Bengali box falls back to the English text, so a half-finished
   translation never produces an empty page.
5. **Save Changes** at the bottom. The green notice at the top confirms it.

### Repeaters

Lists such as statistics, pillars, milestones, values, FAQ entries and
scholarship tiers are **repeaters**:

* **Add row** appends an entry.
* Drag the handle on the left to reorder.
* The eye icon hides a row without deleting it.
* The bin deletes it after a confirmation.

### Images

Every image field offers two routes:

* **Select image** — the normal media library. Preferred: the theme then
  generates responsive sizes.
* **Image URL** — paste an address for an asset hosted elsewhere.

Always fill the **Alt text** box. Screen-reader users and search engines both
rely on it.

### Icons

Icon fields open a searchable picker with about eighty line icons (education,
agriculture, health, climate, finance, social, UI). Type to filter.

## Adding a record

### A programme

**PDO Programs → Add New**

| Field | Purpose |
| --- | --- |
| Title / Content | Name and full description |
| Excerpt | The two-line summary used on cards |
| Featured image | Card and hero image |
| Short name | Compact label for tight layouts |
| Icon, Accent colour | Visual identity of the card |
| SDGs | e.g. `1, 2, 4, 13` |
| Results | The measurable outcome, shown in bold |
| Statistics (repeater) | Value + suffix + label, animated on the single page |
| Achievements (repeater) | Bullet list with a tick |
| Gallery (repeater) | Photographs |
| Documents (repeater) | Links to `PDO Documents` records |
| CTA label / URL | The button at the bottom |
| Category | `PDO Programs → Categories` |
| Display box | Order, visibility, featured flag |

### A team member

Title = the person's name. Fill **Designation**, upload a **Photo**, add
**E-mail**, **Phone** and the **Socials** repeater, and assign a **Team group**
(Executive, Advisory Board, Field Coordinators …). Groups appear as headed
blocks on the About page in the order you set on the term.

### A document

Title, **Description**, **Year**, an **Icon**, and either an uploaded **File**
or an **External URL**. Assign a **Document category** so it lands under the
right filter on the Transparency page. Use **Download label** to override the
button text.

### A news item

Title, Content, Excerpt, Featured image, plus a **Kicker** (the small coloured
line above the headline). Assign one or more **News categories** — they drive
the filter bar on the Media & Insights page.

### An impact story

Title, Content, **Beneficiary**, **Quote** (the pull-quote), **Photo**,
**Location**, **Story date** and a **Tag**. Link it to a programme with the
**Program** field.

## Custom sections

**PDO Sections → Add New** builds a band of content and drops it on any page
without touching code.

1. Choose a **Section type** — hero, cards, stats, split text/image, timeline,
   accordion, gallery, quote, CTA, chart, form, and more.
2. Choose a **Location** — Homepage, About, Impact, Transparency, Media,
   Get Involved, Contact, or **Global** (every page).
3. Choose a **Placement** — before or after the page's own content.
4. Fill the eyebrow, heading, text, image, button and the **Items** repeater.
5. Pick a **Background** (white, light, tint, dark, navy, pattern,
   transparent), an alignment, a column count and the padding.
6. Use **Hide on desktop / tablet / mobile** for device-specific content.

Two section types reuse the generic **Button label** field because they have no
control of their own:

* **Form section** — type `contact`, `volunteer` or `partner` in *Button label*
  to choose which form is embedded. Anything else gives you the contact form.
* **Chart section** — *Button label* becomes the caption inside the donut
  (default `Total`).

## Bengali

The site is bilingual at runtime. Visitors switch with the **EN / বাংলা**
control in the top bar; their choice is remembered in a cookie.

* Every content field has a Bengali twin. Leave it empty and the English text
  is shown.
* Button and label wording lives in **PDO Manager → Translations**, one row per
  string with an English and a Bengali column.
* Menu items have a Bengali label box inside **Appearance → Menus**.
* To preview, append `?lang=bn` to any URL.

## Forms and submissions

Three forms ship ready to use: **Contact**, **Volunteer** and **Partner**. Edit
their fields under **PDO Manager → Contact** and **→ Get Involved** — each row
has a name, label, type, placeholder, required flag and a half/full width.

Submissions arrive in **PDO Manager → Submissions**:

* filter by form type, status or date;
* click a row to read the full message;
* mark **read**, **replied** or **archived**;
* **Export CSV** for the whole filtered set.

An entry is always saved even if the notification e-mail fails, so nothing is
lost when the mail server is down.

## Reordering the home page

**PDO Manager → Reorder** (or **Appearance → Customize → Homepage Sections**).
Drag the rows, or focus a row's handle and use ↑ / ↓. The checkbox hides a
section. Save and reload the front page.

## Good habits

* Write the excerpt yourself — it is what appears on every card.
* Keep headlines under about eight words; the design is built for short,
  confident lines.
* Upload images at least 1600 px wide; the theme generates the smaller sizes.
* Fill alt text every time.
* Use the hide toggle instead of deleting — it keeps the content recoverable.
* After changing permalinks or adding a post type, visit
  **Settings → Permalinks** and save once to flush the rules.
