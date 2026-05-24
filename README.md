# Student Portal

This is a PHP/MySQL based Student Portal Web Application. 
It supports student registration, image upload, viewing a dashboard of registered students, and updating their admission status.

## Features
- Dynamic state/LGA fetching from JSON.
- Responsive, modern user interface.
- Automatic database and table creation upon running the app.

## Requirements
- PHP 7.4+ or 8.x
- MySQL / MariaDB (e.g. via XAMPP)
- PDO Extension Enabled

## Installation & Setup

1. **Folder Placement:**
   Place this entire `studentportal` folder inside your web server's document root (e.g., `htdocs` for XAMPP, `www` for WAMP/MAMP).

2. **Database Configuration:**
   - The application is configured to automatically create the database (`student_portal`) and the required `students` table when you visit any page.
   - It uses the default MySQL credentials: 
     - **Host:** `localhost`
     - **Username:** `root`
     - **Password:** `""` (empty string)
   - If your database server uses a different username or password, please update them in `config.php`.

3. **Alternative Manual Database Setup:**
   If you prefer to set up the database manually, you can import the provided `db_setup.sql` file via phpMyAdmin.

4. **Running the App:**
   - Open your browser and navigate to: `http://localhost/studentportal/`
   - You should see the homepage. From there, you can register new students or access the Admin Dashboard.
