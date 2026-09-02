# Cleanup and optimizations

What `inc/cleanup.php` disables, why, and when to turn each item back on.

Everything in that file is a trade-off. Read this before enabling the
commented-out sections on a client project.

---

## Enabled by default

These are safe on virtually any project.

### Head cleanup

Removes from `<head>`:

| Tag                       | Why                                                                                 |
| ------------------------- | ----------------------------------------------------------------------------------- |
| `<meta name="generator">` | Exposes the exact WordPress version to attackers scanning for known vulnerabilities |
| RSD link                  | For Windows Live Writer — discontinued in 2017                                      |
| wlwmanifest link          | Same                                                                                |
| Adjacent post links       | Extra DB queries, no SEO benefit                                                    |
| Shortlink                 | `?p=123` URLs, superseded by permalinks                                             |

**Risk:** none.

### Emoji script

WordPress loads ~15 KB of JS on every page to render emoji consistently on
old browsers. Modern browsers handle emoji natively.

**Risk:** none on current browsers. Emoji still display — they just aren't
replaced by Twemoji images.

### oEmbed discovery

Disables the `wp-embed.js` script and oEmbed discovery links.

**Risk:** pasting a YouTube, Twitter, or Spotify URL in the editor no longer
auto-converts it into an embedded player. It renders as a plain link.

**Re-enable if:** the client blogs and embeds media. Comment out
`starter_disable_embeds()`.

### Revision limit

Caps stored revisions at 5 per post. Without a limit, `wp_posts` grows
indefinitely — a content-heavy site can end up with thousands of rows of
dead revisions.

**Risk:** older revisions are no longer recoverable.

**Adjust:** change the return value, or `return -1` for unlimited.

### Login error masking

WordPress tells you whether a username exists ("Unknown username" vs
"Incorrect password"). That's a gift to anyone brute-forcing.

**Risk:** slightly less helpful for legitimate users who forgot which
account they used.

### Comment URL field

Removes the "Website" field from the comment form — it exists almost
entirely to attract link spam.

**Risk:** none for most sites.

---

## Disabled by default (commented out)

Enable these deliberately, per project.

### Block library CSS

```php
// starter_remove_block_css()
```

Removes `wp-block-library`, `wp-block-library-theme` and `global-styles`
stylesheets (~50 KB combined).

**Only enable if:** the editor is used for plain text only — no columns,
buttons, galleries, cover blocks, or any layout block.

**What breaks otherwise:** every Gutenberg layout block loses its styling.
Columns stack, buttons become plain links, galleries become a vertical
stack of images.

**Safer alternative:** leave it on, and let a caching plugin handle
unused-CSS removal per page in production.

### Comments

```php
// starter_disable_comments()
```

Closes comments site-wide, empties existing comment arrays, and removes the
admin menu.

**Enable if:** the site is a brochure site with no blog, or the client
doesn't want to moderate.

**Note:** this hides comments rather than deleting them. Existing comments
stay in the database.

---

## What NOT to disable

You'll find snippets online recommending these. Don't.

### The REST API

```php
// DON'T DO THIS
add_filter('rest_authentication_errors', '__return_true');
```

Gutenberg depends on it. So do most modern plugins, the mobile app, and
anything doing headless. Disabling it breaks the editor.

If you're worried about user enumeration via `/wp-json/wp/v2/users`,
restrict that specific endpoint rather than the whole API.

### jQuery

Some themes dequeue jQuery for performance. WordPress core admin, and many
plugins, still depend on it. Removing it breaks things silently and
unpredictably.

### XML-RPC (usually)

Disabling it is often recommended for security. It's also used by the
WordPress mobile app, Jetpack, and some publishing tools. Check whether the
client uses any of those before disabling.

---

## Verifying

After changes, view the page source and confirm:

- No `<meta name="generator">`
- No emoji script block
- `<head>` is noticeably shorter

Then check the editor still works: create a page with a columns block, a
button, and an image, and confirm it renders correctly on the front end.
