# 🎓 Ex-Student Data Portal — Alumni Progression System
### St. John's College, Palayamkottai

A web-based alumni tracking and data collection portal built for **St. John's College, Palayamkottai**. This system enables ex-students (alumni) to register, update their career or academic progression, and allows college staff/admins to view, manage, and download verified alumni profiles.

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation & Setup](#installation--setup)
- [Default Login Credentials](#default-login-credentials)
- [Usage Guide](#usage-guide)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

## 📖 About the Project

The **Ex-Student Data Portal** is an official alumni data management system for St. John's College. It allows:

- **Alumni (ex-students)** to self-register, log in, and update their professional or academic status.
- **Staff/Admins** to view all registered alumni, filter by department, and download individual profile reports.
- The college to maintain a **verified, centralized record** of alumni progression for placement statistics and networking purposes.

---
### 👨‍🎓 Student Portal
- Self-registration with register number, department, batch year, email & phone
- Secure login with hashed password authentication
- Profile update with dynamic status-based fields:
  - **Working** → Company name, salary, ID card / offer letter (Google Drive link)
  - **Higher Studies** → College/university name, course/program name
  - **Self or Business / Other** → Custom description
- Profile photo upload (JPG/PNG stored locally)
- Signature and working proof upload via Google Drive links
- Proof history tracking (previous submissions retained)
- Account status display: `Pending`, `Approved`, `Rejected`

### 🛡️ Admin / Staff Portal
- Separate login for **Admin** (all departments) and **Staff** (department-specific view)
- Overview dashboard with stats:
  - Total registered alumni
  - Working count
  - Higher Studies count
- Student progression table with photo, contact, department, batch, and career path
- View proof documents linked via Google Drive
- **Download individual student profile** as a printable report (`download_student.php`)
- Role-based access control (Admin sees all; Staff sees only their department)


### 🌐 Landing Page
- College-branded home page with hero section and call-to-action
- Statistics strip (500+ Alumni, 85% Working, etc.)
- Info cards: Verified Records, Career Tracking, Secure Data
- Responsive navigation with mobile menu toggle

## 🛠️ Tech Stack

| Layer        | Technology                          |
|--------------|-------------------------------------|
| Frontend     | HTML5, CSS3, JavaScript (Vanilla)   |
| Backend      | PHP 8.x                             |
| Database     | MySQL (via MySQLi)                  |
| Icons        | Font Awesome 6.4                    |
| Server       | Apache (XAMPP)                      |
| File Storage | Local `uploads/` directory          |
| Proof Docs   | Google Drive links (external)       |


## 📁 Project Structure

```
ex student data/
│
├── index.html                  # Landing / Home page
├── student_login.php           # Student login page
├── student_register.php        # Student self-registration
├── student_dashboard.php       # Student profile & proof management
├── logout.php                  # Student session logout
│
├── admin_login.php             # Admin / Staff login page
├── admin_dashboard.php         # Admin overview & student table
├── admin_logout.php            # Admin session logout
│
├── download_student.php        # Generate printable student profile
│
├── db.php                      # Database connection (active)
├── db_connect.php              # Alternate DB connection file
│
├── database.sql                # Initial DB schema (students + staff)
├── add_status_fields.sql       # Migration: adds career columns
├── create_student_proofs_table.sql  # Migration: proof history table
├── add_status_fields.php       # PHP runner for status fields migration
│
├── css/                        # Legacy CSS files
├── assets/
│   ├── css/style.css           # Main stylesheet
│   └── js/script.js            # Main JavaScript
│
└── uploads/                    # Profile photos uploaded by students
```

## 🗄️ Database Schema

### Database: `ex_student_db`

#### `students` table
| Column           | Type          | Description                              |
|------------------|---------------|------------------------------------------|
| `id`             | INT (PK)      | Auto-increment primary key               |
| `register_number`| VARCHAR(50)   | Unique college register number           |
| `name`           | VARCHAR(100)  | Full name                                |
| `department`     | VARCHAR(100)  | e.g., CSE, IT, ECE, MECH, CIVIL, ARTS   |
| `batch_year`     | INT           | e.g., 2021-2025                          |
| `email`          | VARCHAR(100)  | Unique email address                     |
| `phone`          | VARCHAR(20)   | Contact number                           |
| `current_job`    | VARCHAR(100)  | General job/status description           |
| `address`        | TEXT          | Current address                          |
| `photo`          | VARCHAR(255)  | Profile photo path                       |
| `id_photo`       | VARCHAR(255)  | ID card photo path or Drive link         |
| `signature`      | VARCHAR(255)  | Signature image (Drive link)             |
| `working_proof`  | VARCHAR(255)  | Offer letter / ID card (Drive link)      |
| `password_hash`  | VARCHAR(255)  | Bcrypt password hash                     |
| `status`         | ENUM          | `Pending` / `Approved` / `Rejected`      |
| `status_type`    | VARCHAR(50)   | Working / Higher Studies / Other         |
| `company_name`   | VARCHAR(255)  | Company (if Working)                     |
| `salary`         | VARCHAR(100)  | Salary info (if Working)                 |
| `college_name`   | VARCHAR(255)  | College name (if Higher Studies)         |
| `studies_name`   | VARCHAR(255)  | Course/program (if Higher Studies)       |
| `created_at`     | TIMESTAMP     | Registration timestamp            

#### `staff` table
| Column          | Type         | Description              |
|-----------------|--------------|--------------------------|
| `id`            | INT (PK)     | Auto-increment primary key |
| `name`          | VARCHAR(100) | Staff full name          |
| `email`         | VARCHAR(100) | Unique email             |
| `password_hash` | VARCHAR(255) | Bcrypt password hash     |
| `created_at`    | TIMESTAMP    | Account creation time    |

#### `student_proofs` table
| Column         | Type         | Description                        |
|----------------|--------------|------------------------------------|
| `id`           | INT (PK)     | Auto-increment primary key         |
| `student_id`   | INT (FK)     | References `students.id`           |
| `id_photo`     | VARCHAR(255) | ID photo at time of submission     |
| `signature`    | VARCHAR(255) | Signature at time of submission    |
| `working_proof`| VARCHAR(255) | Proof doc at time of submission    |
| `status`       | VARCHAR(20)  | `latest` or `previous`            |
| `uploaded_at`  | TIMESTAMP    | Submission timestamp     

## ⚙️ Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 8.x + Apache + MySQL)
- A modern web browser

### Step 1 — Clone / Copy the Project
```
Place the project folder in:
C:\xampp\htdocs\ex student data\
```

### Step 2 — Start XAMPP Services
Open **XAMPP Control Panel** and start:
- ✅ **Apache**
- ✅ **MySQL**

### Step 3 — Set Up the Database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** to create a database named `ex_student_db`
3. Select the new database and click the **SQL** tab
4. Run the following SQL files **in order**:

```sql
-- Step 3a: Core schema
SOURCE database.sql;

-- Step 3b: Career/status columns
SOURCE add_status_fields.sql;

-- Step 3c: Proof history table
SOURCE create_student_proofs_table.sql;
```

> **Or** import each `.sql` file via phpMyAdmin → Import tab.

### Step 4 — Configure Database Connection

Edit `db_connect.php` (or `db.php`) with your MySQL credentials:

```php
<?php
$conn = new mysqli("localhost", "root", "", "ex_student_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

> Default XAMPP credentials: host=`localhost`, user=`root`, password=`""` (empty)

### Step 5 — Create Admin/Staff Account

Insert a staff record manually via phpMyAdmin SQL tab:

```sql
INSERT INTO staff (name, email, password_hash)
VALUES (
  'Admin',
  'admin@stjohnscollege.edu',
  '$2y$10$REPLACE_WITH_BCRYPT_HASH'
);
```

> Generate a bcrypt hash at: https://bcrypt-generator.com/ (cost factor: 10)

### Step 6 — Run the Application

Open your browser and visit:

```
http://localhost/ex%20student%20data/
```

Or navigate to:
- 🏠 Home: `http://localhost/ex%20student%20data/index.html`
- 🎓 Student Login: `http://localhost/ex%20student%20data/student_login.php`
- 🛡️ Staff Login: `http://localhost/ex%20student%20data/admin_login.php`

## 🔐 Default Login Credentials

> There are no default credentials pre-seeded. You must add staff accounts manually via phpMyAdmin as shown above. Students register themselves via `student_register.php`.

## 📖 Usage Guide

### For Students (Alumni)
1. Go to **Student Login** → click **Register**
2. Fill in your Register Number, Name, Department, Batch Year, Email, Phone, and Password
3. After registration, log in and complete your profile:
   - Select your current status (Working / Higher Studies / Other)
   - Fill in the relevant details and proof links (Google Drive)
   - Upload your profile photo
4. Your profile will show as **Pending** until reviewed by staff

### For Staff / Admin
1. Go to **Admin / Staff Login**
2. Log in with your staff credentials
3. View the dashboard with alumni stats
4. Browse the student progression table
5. Click **Download Profile** to export a student's printable report

## 🗂️ SQL Migration Files

Run these if you are upgrading from an older version of the schema:

| File | Purpose |
|------|---------|
| `database.sql` | Creates `students` and `staff` tables |
| `add_status_fields.sql` | Adds career status columns to `students` |
| `create_student_proofs_table.sql` | Creates `student_proofs` history table |
| `add_subcategory_column.sql` | Adds subcategory columns (if used) |
| `db_update.sql` | Additional column updates |

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a new branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "Add: your feature description"`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

---
## 📄 License

This project was developed as an academic/college management system for **St. John's College, Palayamkottai**. All rights reserved © 2026.

---

<div align="center">
  <strong>St. John's College, 
