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
