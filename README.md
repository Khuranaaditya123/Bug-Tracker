# 🐞 Bug Tracker System

A full-fledged **Bug Tracker System** built with role-based access for **Admin**, **Developer**, and **Tester** users. Designed to streamline the bug reporting, assignment, and resolution workflow, complete with email-based notifications and OTP-secured password recovery.

## 🚀 Features

### 🔐 Authentication
- Role-based registration: Admin, Developer, Tester
- Secure login system with email verification
- "Forgot Password" flow with OTP verification sent via mail
- Reset password functionality

### 🧑‍💼 Admin Panel
- Assign bugs to developers
- View individual developer dashboards and bug status reports
- Access complete bug resolution reports
- Full control over user access and assignments

### 👨‍💻 Developer Dashboard
- Receive email notifications on bug assignments
- Update bug status: `Open`, `In Progress`, `Fixed`, `Closed`
- Submit reports directly to admin via dashboard

### 🧪 Tester Panel
- Report bugs via the dashboard
- Submit detailed bug reports
- Track the status of reported issues

### 📬 Email Integration
- OTP for password recovery
- Admin-to-Developer email notifications on new bug assignments

---

## 🛠 Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP (or Laravel if applicable)
- **Database**: MySQL
- **Email System**: SMTP with PHP Mailer (or other)


