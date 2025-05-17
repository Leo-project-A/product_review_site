# Product Review Site (PHP + MySQL)

A simple and secure product review web application built in PHP, using MySQL and AJAX.

Users can submit reviews, and an admin can approve or decline them from a protected admin panel.

---

## Features

- Public review submission form (AJAX)
- Admin login system
- Admin dashboard to approve or decline reviews (AJAX)
- Secure session management and logout
- CSRF protection on all forms
- Uses **PDO** and prepared statements
- Clean, modular structure (partials, config, utilities)
- Tracks IP address for each review

---

## Project Structure

/Product_review_site/
│
├── index.php # Main product page
├── admin.php # Admin dashboard (protected)
├── admin_login.php # Admin login form
├── logout.php # Secure session logout
│
├── functions.php # Reusable helpers (CSRF, login check)
│
├── utils/
│ ├── submit_review.php # Handles AJAX review submissions
│ ├── admin_actions.php # Handles AJAX approve/decline
│ └── config.php # DB connection (PDO) + session start
│
├── partials/
│ ├── header.php # Common HTML header
│ └── footer.php # Common HTML footer
│
├── css/
│ └── style.css # generic styling for UI
│
├── README.md
