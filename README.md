# Product Review Site (PHP + MySQL)

A simple and secure product review web application built in PHP, using MySQL and AJAX.

Users can submit reviews, and an admin can approve or decline them from a protected admin panel.

---

## Features

- Public review submission form (AJAX)
- Admin login system (with login attempts tracking)
- Admin dashboard to approve or decline reviews (AJAX)
- Secure session management and logout
- CSRF protection on all forms
- Uses **PDO** and prepared statements
- Clean, modular structure (partials, config, utilities)
- Tracks IP address for each review
- Abuse protection: login attempt tracking + lockout/rate limiting
- Central Error handling and logging for debugging

---

## Corrently in Progress

- Pagination on the review list, result LIMITing

---

## Database schema

```

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50),
  rating TINYINT CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  approved TINYINT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  ip_address VARCHAR (50) NOT NULL
  );

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,  
  name VARCHAR(50),
  password VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  success TINYINT(1) NOT NULL
);

```

## Project Structure

```

Product_review_site/
├── index.php                       # Main product page
├── admin.php                       # Admin dashboard (protected)
├── admin_login.php                 # Admin login form
├── logout.php                      # Secure session logout
├── config.php                      # DB connection (PDO) + session start
│
├── utils/
│   ├── submit_review.php           # Handles AJAX review submissions
│   ├── admin_actions.php           # Handles AJAX approve/decline
│   ├── admin_login_process.php     # Handles AJAX login attempts
│   ├── auth.php                    # Auth/session helpers
│   ├── protection.php              # Anti-abuse + validation
│   └── functions.php               # Reusable helpers (CSRF, login check)
│
├── partials/
│   ├── header.php                  # Common HTML header
│   └── footer.php                  # Common HTML footer
│   
├── logs/
│   └── app.log                     # Structured JSON error log (gitignored)
│
├── css/
│   └── style.css                   # Generic styling for UI
│
├── CHANGELOG.md
└── README.md

```
