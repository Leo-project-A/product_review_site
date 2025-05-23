# Changelog

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

