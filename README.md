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
