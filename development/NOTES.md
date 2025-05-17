# NOTES – Product Review App

## ✅ Already Learned / Implemented

- ✅ PHP sessions and access control
- ✅ Basic HTML form handling
- ✅ `mysqli` prepared statements for secure DB interactions (migrated to PDO)
- ✅ Input validation and output sanitization with `htmlspecialchars()`
- ✅ Secure password storage with `password_hash()` and `password_verify()`
- ✅ Admin login system with session management
- ✅ Admin dashboard for review moderation (approve/delete)
- ✅ Clean project structure (partials, config, separation of logic)
- ✅ Secure logout (`session_destroy`)
- ✅ Display user and error messages via session
- ✅ Fully working MVP with real data and review system
- ✅ Full PDO refactor (`config.php` singleton connection)
- ✅ AJAX-powered review submission (with CSRF)
- ✅ AJAX-powered admin approve/decline (with DOM updates)
- ✅ jQuery DOM handling with form serialization
- ✅ IP tracking on review submit (via `REMOTE_ADDR`)
- ✅ CSRF protection using session tokens
- ✅ XSS protection (escaping dynamic output in HTML)
- ✅ Code cleanup & review ready

---

## 📌 Must Learn / Add Now (Job Requirements)

- [X] Rewrite admin login with **PDO**
- [X] Learn and use **PDO exception handling**
- [X] **jQuery basics** + DOM manipulation
- [X] **AJAX** form submission (review form)
- [X] **AJAX** admin actions (approve/decline)
- [X] Implement **CSRF protection** using session tokens
- [X] Understand and explain **XSS protection techniques**
- [X] Log user IP on review submission

✅ **All job requirements completed**

---

## ✨ Optional / Phase 2 Ideas

- [ ] Flash message helper function (`set_flash()`, `get_flash()`)
- [ ] Pagination for reviews
- [ ] Multi-product support (product table + product_id in reviews)
- [ ] Display `created_at` timestamps for reviews
- [ ] Admin action display (who approved/declined what)
- [ ] UI polish (modals, animations, button styles)
- [ ] Responsive layout (mobile-first design)
- [ ] Use Bootstrap or Tailwind CSS for layout cleanup
- [ ] Create debug mode toggle (`DEBUG = true` in config)
- [ ] Break `header`, `footer`, `nav` into smaller includes

---

## 🧠 Phase 3: Advanced / Professional Upgrades

- [ ] Use `.env` file for DB credentials (instead of hardcoding)
- [ ] Regenerate session ID after login (prevent session fixation)
- [ ] Rate-limit login attempts (brute force protection)
- [ ] Log login attempts (successful & failed)
- [ ] Log admin actions (approval history, IPs)
- [ ] Rotate CSRF tokens (on every new form)
- [ ] Central error logger (to file or DB)
- [ ] Switch AJAX responses to JSON (instead of plain text)
- [ ] Move all inline JS to external scripts
- [ ] Add form anti-spam (e.g. honeypot, delay timer)
- [ ] Unit test core functions (`csrf_token`, login, etc.)

---


- what is datastream? while reading from files. whats the structure? is it an obj?
- enctype, whats the purpose?
- Linux premissions octal, whats is it? owner/group/other?

- cookies with php. what type of important cookies are there? whats industry standart?
- is there any relavance in the different div, span, p, tags? when to use which?
- what are the default arguments of $_SESSION other than the ones i create along the code.

- whats the difference between procederal code vs OOP. pros cons, when to use which? are there other types of coding?
- is there just sql? mysql? other database types? (ive heard of noSql? is that a thing?)

- what other methods are there, except "POST"? what do they used for? and how?

- whats the :before and :after tags in CSS?
