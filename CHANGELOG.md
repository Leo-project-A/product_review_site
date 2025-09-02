# Changelog

## v1.4.0 – Error Handling Overhaul (code review issues)
**Date:** 29/07/2025 

### Highlights
- Centralized error handling: one global handler for all PHP errors/exceptions
- Structured logging: every error is written to logs/app.log
- Recorded login attempts (username/IP/success/time) and enforced temporary lockouts (HTTP 429) on repeated failures.
- Abuse protection: login attempt tracking + lockout/rate limiting; tighter anti-spam/duplicate-review checks.

### Changes
- Implemented global `set_error_handler` / `set_exception_handler` with environment-aware messaging (verbose in `dev`, generic in `prod`).
- Added `logs/app.log`; for logging errors without breaking the site.
- Added `utils/admin_login_process.php` and updated admin_login.php to use AJAX responses.
- Hardened anti-abuse checks: honeypot + form timing, and duplicate-review detection to block spam.

## v1.3.0 – code review - Security
**Date:** 26/05/2025 

### Changes

- added `auth.php` for centralized authentication and admin session handling
- added `protection.php` for form validation, rate limiting, and anti-spam measures
- Request rate limiting per IP to prevent abuse
- Duplicate review detection based on username
- Honeypot field protection and form timing validation (anti-bot)

- Reworked CSRF token generation and validation logic (now stricter and more modular)
- Generalized login feedback messages for cleaner UX
- Cleaned up `admin_actions.php` for better structure and DRY principles
- Removed logic from `logout.php` (replaced by new session handling in `auth.php`)

## v1.2.0 – code review - Best Practices
**Date:** 25/05/2025 

### Changes

- CSRF tokens are now generated once per page (only when needed) and properly validated across all relevant form submissions.
- Improved HTML escaping using `htmlspecialchars()` to prevent XSS vulnerabilities across all output layers.
- Enhanced string handling for messages injected into the DOM via JavaScript, ensuring no raw HTML is injected.
- Introduced centralized `DATA_RULES` constant in `config.php` to define validation rules (length, pattern, type) for all user input fields.
- Added strict input validation on the server side using a `validate_input_data()` helper, rejecting invalid data before processing or storing.
- System messages are now separated by purpose:
  - Global messages (login/logout success, system errors) are handled using the new `flash()` mechanism.
  - Area-specific messages (e.g., form feedback) are handled through AJAX responses and displayed inline.
  - Debugging errors and internal issues are logged or suppressed from users to avoid exposing sensitive details.

## v1.1.1 – code review - UX & Feedback
**Date:** 24/05/2025 

### Changes
- *Enhanced review submission UX* by adding visible progress indicators:
  - Cursor changes to `wait`
  - Submit button is disabled during AJAX call
- *Improved admin action feedback* for approving/declining reviews:
  - Uses structured JSON responses
  - Confirmation and error messages are clearly styled and displayed

## v1.1.0 – code review - Code Quality / Structure
**Date:** 23/05/2025 

### Highlights
- Improved modular structure and reliability
- Hardened AJAX and form security
- Standardized server responses and input validation

### Changes
- Replaced all `include` statements with `include_once` or `require_once` where appropriate, to prevent duplicate file inclusions.
- All AJAX handlers now return structured JSON responses instead of plain HTML/text.
- Removed forced `http_response_code(200)` from AJAX endpoints and implemented proper HTTP status codes (`200`, `400`, `403`, `500`) based on outcome.
- Fixed `.fail()` in jQuery to respond to actual backend errors correctly.
- Wrapped all critical database operations in `try/catch` blocks for error isolation and graceful fallback.
- Centralized token/session validation using `validate_csrf_token()` and `is_admin_logged_in()`.
- Created `validate_input_data()` function for verifying:
  - `username`
  - `password`
  - `rating`
  - `review_id`
  - `description`
- Applied input validation before all user and admin actions.
- Updated admin login to display a generic error message ("Invalid username or password") to avoid leaking login status details.

