# Installation & go-live

## 1. Requirements

| Thing | Minimum | Notes |
| --- | --- | --- |
| WordPress | 6.4 | Tested to 6.8 |
| PHP | 7.4 | Tested on 8.2; 8.1+ recommended |
| Database | MySQL 5.7 / MariaDB 10.3 | SQLite works via the official `sqlite-database-integration` drop-in |
| Writable | `wp-content/uploads` | For images and document files |

No Composer, npm or build step. Nothing is fetched from a CDN at runtime.

## 2. Install the two packages

### From the ZIPs

1. **Plugins → Add New → Upload Plugin** → `dist/pdo-site-manager.zip` → Install
   → **Activate**.
2. **Appearance → Themes → Add New → Upload Theme** → `dist/pdo-modern.zip` →
   Install → **Activate**.

### From the repository

```bash
cp -R pdo-site-manager /path/to/wp-content/plugins/
cp -R pdo-modern       /path/to/wp-content/themes/
```

Then activate both from the admin.

> Activate the **plugin first**. The theme's templates degrade gracefully
> without it, but the Setup Wizard is what creates the pages the theme expects.

## 3. Run the Setup Wizard

**PDO Manager → Setup Wizard → Run setup**

It is idempotent — running it twice creates nothing new. It will:

* create the seven pages and remember their IDs in `pdo_sm_pages`
  (`home`, `about`, `impact`, `transparency`, `media-insights`,
  `get-involved`, `contact`);
* set **Settings → Reading** to a static front page (the `home` page);
* create the three menus (`Primary Navigation`, `Footer – Quick Navigation`,
  `Footer – Flagship Programs`) and assign them to their locations;
* seed all nine settings groups with the real PDO copy;
* seed the demo records: 5 programmes, 11 team members, 4 partners, 3 stories,
  4 documents, 6 news items and 26 taxonomy terms;
* seed the bilingual interface strings.

## 4. Permalinks

**Settings → Permalinks → Post name**, then **Save Changes**. This also flushes
the rewrite rules for the custom post types:

| Post type | Archive | Single |
| --- | --- | --- |
| `pdo_news` | `/media/` | `/media/{slug}/` |
| `pdo_program` | `/programs/` | `/program/{slug}/` |
| `pdo_story` | `/impact-stories/` | `/impact-story/{slug}/` |
| `pdo_document` | — | `/document/{slug}/` |
| `pdo_team` | — | `/team/{slug}/` |
| `pdo_partner` | — | `/partner/{slug}/` |

Taxonomy archives live at `/media-category/`, `/media-tag/`,
`/program-category/`, `/team-group/`, `/partner-category/`,
`/story-category/` and `/document-category/`.

## 5. Replace the placeholder content

| Where | What to change |
| --- | --- |
| **PDO Manager → Organisation** | Legal name, registration number, address, phone, WhatsApp, e-mail, office hours, socials, map coordinates |
| **Appearance → Customize → Site Identity** | Logo, favicon |
| **PDO Manager → Homepage** | Hero copy, statistics, pillars, president's message, gallery |
| **PDO Programs / Team / Partners / …** | Replace the seeded demo records with the real ones |
| **PDO Manager → Translations** | Review every Bengali string |

Images: upload through the media library and pick them in the settings screens.
Each image field also accepts an external URL for cases where the asset is
hosted elsewhere.

> The Setup Wizard seeds **remote placeholder photographs** so the site looks
> finished on day one. They are hot-linked, not stored in your media library.
> Replace every one of them with your own photography before launch — search
> the settings screens for image fields whose URL starts with `https://` and
> swap in an uploaded attachment.

## 6. Forms and e-mail

Forms post to `admin-post.php` and always store the submission first, so an
e-mail outage never loses an enquiry.

1. **PDO Manager → Organisation → Location** → set **Notification e-mail**.
2. Install an SMTP plugin (most shared hosts block `mail()`); without one the
   visitor sees *"your message has been received … but the e-mail notification
   could not be delivered"* and the entry is still in
   **PDO Manager → Submissions**.
3. Export entries to CSV from the same screen.

Anti-spam is built in: nonce, honeypot field, a 3-second minimum fill time and
a limit of 5 submissions per 10 minutes per visitor.

## 7. Going live checklist

- [ ] Setup Wizard has been run and the seven pages exist
- [ ] Permalinks set to **Post name**
- [ ] Real organisation details saved
- [ ] Logo and favicon uploaded
- [ ] Demo programmes / team / partners / stories replaced
- [ ] Notification e-mail set and SMTP configured
- [ ] Bengali strings reviewed in **Translations**
- [ ] A test submission appears in **Submissions**
- [ ] `?lang=bn` renders the Bengali site
- [ ] Open Graph image set (**PDO Manager → Organisation**) — check with a
      social-preview debugger
- [ ] Caching / CDN enabled at the host level if you use one

## 8. Moving the site

1. **PDO Manager → Tools → Export** → download the JSON (every settings group).
2. On the destination install: activate both packages, **Tools → Import**, then
   run the **Setup Wizard** to rebuild pages and menus.
3. Migrate `wp_posts`, `wp_postmeta`, `wp_terms*` and `{prefix}pdo_submissions`
   with your usual migration tool, and copy `wp-content/uploads`.

## 9. Uninstalling

Deactivating the plugin leaves everything in the database.

Deleting the plugin removes its data **only** when
**PDO Manager → Tools → Delete all data on uninstall** is enabled. That setting
drops `{prefix}pdo_submissions`, every `pdo_sm_*` option and the custom
capabilities. Posts are never deleted automatically — remove them yourself if
you want a clean slate.
