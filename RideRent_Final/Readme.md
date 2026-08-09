
# 🚗 RideRent Pro - Final Project Code

This is the complete source code for our CSE 299 final project.

---

## 👥 Team Members & Responsibilities (4 Members)

### 1. ORNIMA HAFIZUR (Admin Panel & Authentication) - **My Part**
- **Admin Login Authentication**
  - Login with username & password
  - Logout & Session Management (Session start/destroy)
  - Password Hashing & Verification
  - Forgot Password
  - Register
- **Full Database Creation**
  - MySQL Database Design (Admin,Booking,Customer,Driver,Hub_Inspection,Notifications,Users, Vehicle, Vehicle Owner, Payments methods,Reviews)
  - Database connection established via `db_connect.php`
- **Common Layout Parts (Header, Footer, Sidebar)**
  - Created common `header.php`, `footer.php`, `sidebar.php` for all pages
  - Sidebar menu for Admin Dashboard (Dashboard, Users, Vehicles, Reports)
- **Asset Management (CSS, JS, Images)**
  - Custom `style.css` (Layout & Responsive Design)
  - `validation.js` for frontend form validation
  - All required images (Logo, Icons) uploaded in the `images/` folder
- **Admin Dashboard View**
  - Display summary overview of total users, rentals, and income
- **User Management**
  - View the list of all regular users
  - Block / Unblock any user
- **Vehicle Approval System**
  - Approve or Reject vehicle advertisements posted by owners

---


---

##  Technology Stack
- **Backend:** PHP (Core)
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL (phpMyAdmin)
- **Version Control:** Git & GitHub

---

## ⚙️ Database Setup (How to run this project)
1. Create a database in phpMyAdmin (e.g., `riderentpro_db`).
2. Import the `database/riderentpro_db.sql` file (we will upload this file).
3. Update your database username & password in `includes/database.php`.
4. Run the project on localhost.

---

> 📌 **Note:** After everyone finishes their work, we will merge all branches into `main`.
