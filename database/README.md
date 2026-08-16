# Database — `lgu_requests_db`

This folder holds the actual production database export
(`lgu_requests_db.sql`, taken Aug 16, 2026) alongside the app.

## Setting up the database

You have two options:

### Option A — Import the real dump (recommended)
This gives you your real departments, users, requests, and history instead
of an empty database.

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS lgu_requests_db"
mysql -u root -p lgu_requests_db < database/lgu_requests_db.sql
```

Or in phpMyAdmin: create the `lgu_requests_db` database, open the **Import**
tab, and select `database/lgu_requests_db.sql`.

### Option B — Let the app create it for you
If you skip the import, `config/database.php` will auto-create every table
the app needs the first time it runs (`CREATE TABLE IF NOT EXISTS ...`), but
you'll start with an empty system (no departments, no users, no requests) —
you'd need to register an account and re-create your departments by hand.

Either way, `config/database.php` also runs a few defensive `ALTER TABLE`
statements on every request (wrapped in try/catch, so they're safe to run
repeatedly) to keep the schema in sync with what the code expects — this
matters most if you go with Option A, since the dump is a snapshot and the
code has moved slightly ahead of it. See "Known differences" below.

## Known differences between the dump and what the code expects

I compared every `CREATE TABLE` in `lgu_requests_db.sql` against the schema
`config/database.php` builds, and found one real mismatch:

- **`users.status`** — the dump's enum is `('active','inactive','pending')`,
  but `pages/login.php` checks for a `'rejected'` status too. In practice
  this doesn't currently bite: the "reject user" action
  (`includes/actions.php`) deletes the row outright rather than setting
  `status = 'rejected'`, so that value was likely never actually needed —
  but if that behavior ever changes, the column needs to allow it. I added
  an `ALTER TABLE users MODIFY status ENUM(...,'rejected')` to
  `config/database.php` so this is fixed automatically after import, no
  action needed on your part.

Everything else is either an exact match or a harmless superset (e.g. the
dump's `requests.priority` enum includes an unused `'urgent'` option, and
the dump has a few legacy columns/tables — `attachments`, `request_items`,
`request_status_history`, `requests.attachment_path` /
`attachment_name` — that no page or handler in this codebase reads from or
writes to; they're kept in the dump for parity with production but aren't
required for the app to run).
