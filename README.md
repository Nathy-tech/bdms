# Blood Donation Management System

## 📌 Project Overview
The **Blood Donation Management System** is a web-based platform designed to streamline the process of blood donation, management, and distribution. It allows donors to register, schedule donations, and receive notifications when their blood is used. Hospitals can request blood, and administrators manage the entire workflow, ensuring a smooth and efficient system.

## 🚀 Features
### 🔹 **Admin**
- Approve donation requests
- Register hospitals, nurses, and inventory managers
- Manage user accounts
- Post announcements
- Generate reports
- Notify donors when their blood is used

### 🔹 **Donor**
- Register and log in
- Request donation
- Receive notifications when blood is used
- View reports and donation history

### 🔹 **Nurse**
- Collect and record blood donations
- View reports

### 🔹 **Hospital**
- Request blood from the system
- Update patient status
- Provide feedback
- View reports

### 🔹 **Inventory Manager**
- Manage blood inventory (add, discard, distribute blood units)
- View blood stock levels

## 🏗️ Tech Stack
- **Backend**: PHP (Core PHP, no framework)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Server**: Apache (via XAMPP)
- **Email Notifications**: PHPMailer (SMTP-based email sending)

## 📂 Project Structure
```
/blood-donation-system/
│
├── /css/                  # Stylesheets
├── /js/                   # JavaScript files
├── /includes/             # Database connection & functions
├── /pages/
│   ├── admin/             # Admin dashboard & functionalities
│   ├── donor/             # Donor features
│   ├── nurse/             # Nurse functionalities
│   ├── hospital/          # Hospital features
│   ├── inventory_manager/ # Blood inventory management
│
├── /uploads/              # Uploaded reports & files
├── /verification/         # Email verification logic
├── /notifications/        # Donor notification system
└── index.php              # Main landing page
```

## ⚙️ Installation Guide
### 1️⃣ **Clone the Repository**
```bash
git clone https://github.com/Nathy-tech/bdms.git
cd blood-donation-system
```

### 2️⃣ **Set Up Database**
- Import the SQL file (`database.sql`) into your MySQL database.
- Update `/includes/db.php` with your database credentials:
```php
$mysqli = new mysqli('localhost', 'username', 'password', 'blood_donation_db');
```

### 3️⃣ **Configure PHPMailer**
- Install PHPMailer using Composer:
```bash
composer require phpmailer/phpmailer
```
- Update email settings in `/includes/functions.php`:
```php
$mail->isSMTP();
$mail->Host = 'smtp.example.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@example.com';
$mail->Password = 'your-email-password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

### 4️⃣ **Run the Application**
- Start Apache & MySQL in XAMPP.
- Open `http://localhost/blood-donation-system/` in your browser.

## 📩 How Donor Notifications Work
1. Donor donates blood → recorded in `blood_units` table.
2. Blood is distributed to a hospital → recorded in `distributed_bloods` table.
3. Admin clicks "Notify Donor" → system retrieves donor's email and sends a notification.

## 📜 License
This project is open-source.

## 📞 Contact
For questions or contributions, reach out to: **mannathy5@gmail.com**

