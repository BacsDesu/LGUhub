This is `request.php` (originally 6,356 lines in one file) split into a
maintainable folder structure. No logic was changed — only reorganized.

## Structure

```
mcr_v3/
├── index.php               Entry point / router (replaces the old page-routing chain)
├── config/
│   ├── database.php         mysqli connection + auto-migration (CREATE TABLE / ALTER TABLE)
│   └── theme.php             System settings + the theme color palette
├── includes/
│   ├── functions.php        All shared helper functions (isLoggedIn, getSetting, etc.)
│   ├── actions.php          Every POST handler + logout (create request, login, signup,
│   │                         approve/reject user, change password, mark notification read, ...)
│   ├── header.php           <head>, per-theme CSS variables, sidebar + topbar
│   └── footer.php           Closes the layout, loads assets/js/app.js
├── pages/                   One file per page (13 total)
│   ├── login.php, dashboard.php, notifications.php, request_details.php,
│   │   recently_deleted.php, settings.php, create_request.php, view_requests.php,
│   │   documentation.php, departments.php, profile.php, change_password.php, users.php
├── assets/
│   ├── css/style.css        All static CSS (theme-independent rules)
│   ├── js/app.js            Global JS: sidebar toggle, alert auto-hide, keyboard shortcuts
│   └── img/                 Branding images (logo, backgrounds)
├── image/                   Runtime folder for uploaded logo / login background (unchanged path)
├── uploads/                 Runtime folder for request attachments (unchanged path)
└── database/
    ├── lgu_requests_db.sql   Real production DB export — schema + seed data
    └── README.md              Import instructions + known schema differences
```

## How routing works now

`index.php`:
1. Starts the session + output buffer
2. Connects to the DB (`config/database.php`)
3. Loads helper functions (`includes/functions.php`)
4. Resolves `$page` from `?page=`
5. Runs all POST handlers (`includes/actions.php`) — these `exit()` on success just like before
6. Resolves theme colors (`config/theme.php`)
7. Renders `includes/header.php`, then the matching file in `pages/`, then `includes/footer.php`

Guard clauses that used to live in the big `elseif` chain (admin-only for
`settings`/`users`, `id` required for `request_details`, login-required
everywhere else) are now enforced in `index.php` and at the top of the
relevant page file, so behavior matches the original exactly.

## Deploying

Drop the whole `mcr_v3/` folder on your server/XAMPP `htdocs`, point your
browser at `mcr_v3/index.php` (or set it as your document root + `index.php`
as default document). Nothing in the DB schema or table names changed, so
your existing `lgu_requests_db` database continues to work as-is.

For the database itself, see `database/README.md` — you can either import
the real production dump (`database/lgu_requests_db.sql`, recommended) or
let `config/database.php` auto-create empty tables on first run.

## What I did NOT change

- No SQL query text, table names, or column names were altered.
- No business logic, validation, or output was rewritten.
- File paths for user uploads (`image/`, `uploads/`) were kept exactly as
  they were so existing rows in `system_settings` / `request_attachments`
  that reference those paths keep working.
