# UEP Student Portal

PHP + MySQL student portal (login, signup, dashboard). Runs on XAMPP.

## Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\uep-portal`.
2. Open XAMPP Control Panel and Start **Apache** and **MySQL**.
3. Go to http://localhost/phpmyadmin, click **Import**, choose `database/uep_portal.sql`, then **Import**.
4. Open http://localhost/uep-portal/ and click **Create an account**.

## Database settings
Edit `includes/config.php` if your MySQL user/password is not the XAMPP default (`root` / empty).

## Edit sample content
`includes/data.php` holds the subjects, tuition rate, grades, offices, requirements and announcements.
