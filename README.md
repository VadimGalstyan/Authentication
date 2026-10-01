# Social Posts Platform

A full-featured social posting platform — user accounts, posts with rich
metadata, nested comments, likes, moderation, and admin tooling — built
**entirely in pure PHP, with no framework**. No Laravel, no Symfony, no
Composer packages for routing or ORM. Just PHP, PDO, and MySQL, written by
hand from the ground up as a learning project, with security and
architecture handled deliberately rather than provided by a framework.

## Why pure PHP

This project was built without a framework on purpose, as a way to actually
learn what frameworks normally do for you: routing, authentication,
authorization, CSRF protection, query building, file uploads, and so on were
all implemented from scratch here. Every `PDO` prepared statement, every
session check, every permission lookup is explicit, hand-written code — not
a configuration file or an auto-generated scaffold. That makes the codebase
larger than an equivalent Laravel app would be, but every part of it is
something that was reasoned through and understood, not just generated.

## Tech stack

| Layer            | Technology                                          |
|-------------------|------------------------------------------------------|
| Language          | PHP (procedural + a handful of small utility classes) |
| Database          | MySQL, accessed via PDO with prepared statements      |
| Frontend          | Plain HTML + CSS (no JS framework, no build step)     |
| Sessions/Auth      | PHP native sessions (`$_SESSION`)                     |
| File uploads       | PHP's built-in `$_FILES` handling + `fileinfo` extension for MIME validation |

No Composer dependencies are required to run this project — it is
self-contained PHP that runs on any standard PHP + MySQL + Apache stack
(e.g. XAMPP).

## Feature overview

### Authentication & accounts
- Registration with validated, unique email and hashed passwords (`password_hash`)
- Email verification flow (verification token, resend verification, "check your email" page)
- Login with session creation, session ID regeneration on login (session fixation protection)
- Logout (session destroyed and cookie cleared)
- Forgot password / reset password via emailed, single-use token
- Failed login attempts are logged and rate-limited (see Security, below)

### Roles & permissions
- Three roles: **user**, **moderator**, **admin**, stored in a `roles` table
- A separate `permissions` table and a `role_permissions` pivot table connect
  roles to specific permissions (`create_post`, `moderate_posts`,
  `moderate_comments`, `view_deleted_posts`, etc.)
- Authorization checks throughout the app use **permission checks**
  (`can('moderate_posts')`) rather than hardcoded role names
  (`if ($role === 'admin')`), so what each role can do is configurable
  through the database, not buried in `if` statements across the codebase
- An admin page lets an admin change any user's role

### User profiles
- First name, last name, phone, location, date of birth, bio, profile picture
- A user can view and edit their own profile; private fields (phone, date of
  birth) are hidden when viewing someone else's profile
- Profile picture upload with MIME-type and file-size validation, with the
  option to remove the current picture
- A profile page shows that user's own published posts

### Posts
- Title, content, status (**draft** / **published** / **archived**,
  stored via a `post_status` lookup table), one category per post, any
  number of tags, and up to 5 photos
- Post photos are stored per-user, in `uploads/posts/<user_id>/`, with
  randomly generated filenames (never the user's original filename) and
  strict MIME/extension/size validation on upload
- Creating or editing a post with tags and photos is wrapped in a database
  **transaction** — if any step fails partway (tag insert, image insert),
  the whole operation rolls back, so a post is never left half-created
- Only the post's own author can edit it; the author, the post's owner, or
  a moderator/admin can delete it (see Moderation, below)
- Deleting a post is a **soft delete** (a `deleted_at` timestamp, not a real
  row deletion) — the post's photos, likes, and comments are preserved so
  an admin can restore it later

### Categories & tags
- **Categories**: a post belongs to exactly one category (`category_id`,
  `NOT NULL`). Only an admin can create, rename, or delete categories.
  Deleting a category automatically moves its posts to a permanent
  "Uncategorized" fallback category, rather than leaving posts without a
  category or blocking the delete outright
- **Tags**: a post can have many tags, and a tag can belong to many posts,
  via a `post_tag` pivot table. Tag names are normalized (lowercase,
  trimmed) so the same tag is never duplicated under slightly different
  spellings. Only an admin can create, rename, or delete tags

### Comments
- Two-level nesting: top-level comments and one level of replies
  (self-referencing `parent_id` column), enforced both in the UI (no
  "reply to a reply" button exists) and on the server (rejected even if
  requested directly)
- Only logged-in, **email-verified** users can comment, and only on
  **published** posts (not drafts or archived posts)
- A comment's own author can edit or delete it; the author of the *post*
  can also delete any comment on their own post; a moderator or admin can
  delete any comment anywhere
- Deleting a top-level comment soft-deletes its replies too, so nothing is
  ever left orphaned or visible without its parent
- Comments are soft-deleted (`deleted_at`), same as posts

### Likes
- A user can like or unlike a post (a simple toggle)
- Enforced as "one like per user per post" **at the database level**, via a
  composite primary key `(user_id, post_id)` on the `post_likes` table —
  not only checked in PHP, so a race condition or application bug can't
  create a duplicate like
- A user cannot like a draft or archived post

### Feed (`/posts.php`)
- **Search** by title and content
- **Filter** by category, tag, and author
- **Sort** by newest, most liked, or most commented
- **Pagination**, with the current search/filter/sort state preserved
  across page links
- Every filter/sort/page value from the URL is validated against a strict
  whitelist or checked with `is_numeric()` before it ever reaches a SQL
  query, and all values are passed through prepared-statement placeholders
  — never concatenated into SQL — so the feed cannot be used to inject
  SQL regardless of what's put in the URL
- Built to avoid the "N+1 query" problem: likes, comments, tags, and images
  for an entire page of posts are each fetched in one additional query
  using `WHERE post_id IN (...)`, rather than one query per post per
  field — the query count stays fixed no matter how many posts are shown

### Moderation & reporting
- Any logged-in user can report a post or comment, with a reason; a user
  cannot report their own content, and cannot report the same item twice
  (enforced with a unique database constraint as well as a PHP check)
- A moderator page lists reported posts and reported comments, who
  reported them, and why — gated by the `moderate_posts` /
  `moderate_comments` permissions specifically, not role names

### Admin tools
- Manage user roles
- Manage categories and tags (create, rename, delete)
- View and restore soft-deleted posts
- View a paginated activity log, filterable by user

### Activity log
Important actions are recorded in a dedicated `activity_log` table,
including: post created / updated / deleted, comment deleted, comment
deleted by a moderator, role changed, failed login attempts, email
verification resent, and password resets. Each entry records who did it,
what kind of thing it affected, and the IP address involved.

## Security

- **CSRF protection**: every form that changes data (create/edit/delete
  post, create/edit/delete comment, like/unlike, profile update, role
  change, report submission, category/tag management) includes a random,
  session-bound token, verified server-side with `hash_equals()` before
  anything runs. A missing or incorrect token is rejected outright.
- **Rate limiting**: stored in a database table (not just the session, so
  it can't be bypassed by starting a new session), covering comments
  (max 5/minute), posts (max 10/day), and failed logins
  (max 5 per 15 minutes, tracked by **both** email and IP address, so the
  limit holds even before a user is identified).
- **SQL injection protection**: every query uses PDO prepared statements
  with bound parameters; no user input is ever concatenated directly into
  SQL.
- **Password security**: passwords are hashed with PHP's `password_hash()`
  and verified with `password_verify()` — plaintext passwords are never
  stored or logged.
- **File upload validation**: uploaded images are checked against both
  their extension and their actual MIME type (via PHP's `fileinfo`
  extension), capped in size, and saved under randomly generated filenames
  to prevent filename collisions or path manipulation.
- **Authorization checks are permission-based**, not role-name checks,
  throughout the moderation and admin features.

## Project structure
/Controllers/ - page logic (one file per route: auth, posts, comments, admin, etc.)
/Views/ - HTML templates rendered by Controllers
/Models/ - database-access classes (User, Role, etc.)
/classes/ - shared utility classes (Csrf, ActivityLogger, RateLimiter)
/functions/ - authorization helpers (can(), hasRole(), requireLogin(), requirePermission(), etc.)
/config/ - database connection and app constants
/assets/ - CSS
/uploads/
/profiles/ - user profile pictures
/posts/<user_id>/ - post photos, organized per user
/database/ - schema.sql and the MySQL Workbench diagram


## Setup

1. Create a MySQL database and import `database/schema.sql`.
2. Set your database credentials in `config/db.php`.
3. Make sure `uploads/profiles/` and `uploads/posts/` exist and are
   writable by the web server.
4. Enable the `fileinfo` PHP extension in `php.ini` (used to validate
   uploaded file types) — uncomment `extension=fileinfo` and restart
   Apache.
5. Point your web server at the project root and open `index.php`.

## Roles summary

| Role      | Can do |
|-----------|--------|
| User      | create posts, comment, reply, like, report content, manage their own posts/comments/profile |
| Moderator | everything a user can, plus delete any post or comment, and view reported content |
| Admin     | everything above, plus manage user roles, categories, and tags, and view/restore deleted posts and the activity log |