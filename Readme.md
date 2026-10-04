# PaySphere

PaySphere is a digital wallet simulation built using PHP, MySQL, Bootstrap, HTML, CSS, and JavaScript.

The application allows users to register, log in securely, add money to their wallet, transfer money to other registered users, manage their profile, change passwords, and view detailed transaction history.

The project focuses on secure backend handling, database transactions, clean UI design, and real-world wallet-style functionality.

## Features

- User registration and login
- Password hashing and verification
- Session-based authentication
- Secure logout
- Add Money wallet top-up
- Send Money to registered users
- Prevent self-transfers
- Balance validation
- Maximum transaction limit
- Database transactions using COMMIT and ROLLBACK
- Row locking using `FOR UPDATE`
- Unique transaction reference IDs
- Transaction status and type
- Recent transaction history
- Full transaction history
- Search and filters
- Pagination
- Transaction details modal
- Edit Profile
- Change Password
- Duplicate phone number validation
- CSRF protection
- POST-Redirect-GET duplicate submission protection
- Responsive Bootstrap-based UI

## Tech Stack

### Frontend

- HTML5
- CSS3
- Bootstrap 5
- Bootstrap Icons
- JavaScript

### Backend

- PHP
- MySQL
- MySQLi Prepared Statements

### Development Environment

- XAMPP
- Apache
- phpMyAdmin

## Project Structure

```text
PaySphere/
│
├── admin/
├── assets/
├── css/
│   └── app.css
├── database/
├── images/
├── includes/
│   ├── auth.php
│   ├── config.php
│   ├── db.php
│   ├── footer.php
│   ├── header.php
│   ├── navbar.php
│   └── user-navbar.php
├── js/
├── pages/
├── uploads/
├── user/
│   ├── dashboard.php
│   ├── send-money.php
│   ├── add-money.php
│   ├── transactions.php
│   └── profile.php
├── index.php
├── login.php
├── register.php
├── logout.php
└── README.md
```
## Database Schema

PaySphere uses a MySQL database named `paysphere`.

### Users Table

The `users` table stores user account information and wallet balance.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Transactions Table

The `transactions` table stores wallet transfers and wallet top-up records.

Main columns:

```text
id
reference_id
sender_id
receiver_id
amount
type
status
created_at
```
Transaction types:
TRANSFER
ADD_MONEY

## Security Features

PaySphere includes several security practices to protect user accounts and wallet operations.

- Passwords are stored using `password_hash()` instead of plain text.
- User login is verified using `password_verify()`.
- Authenticated pages are protected using PHP sessions.
- Session IDs are regenerated after successful login.
- SQL queries use MySQLi prepared statements to reduce SQL injection risk.
- CSRF tokens are used for sensitive form submissions.
- Wallet transfers use database transactions.
- `COMMIT` is used when all transfer operations succeed.
- `ROLLBACK` is used when any transfer operation fails.
- `FOR UPDATE` is used to lock the sender's balance row during a transfer.
- Users cannot transfer money to their own account.
- The application checks for sufficient balance before completing a transfer.
- A maximum transaction limit is enforced.
- Phone numbers are validated and must be unique.
- POST-Redirect-GET is used after successful money operations to prevent accidental resubmission on page refresh.

## How Send Money Works

When a user sends money to another registered PaySphere user, the application performs the following steps:

1. The user enters the receiver's email and transfer amount.

2. The application validates:
   - the receiver exists
   - the sender is not sending money to themselves
   - the amount is valid
   - the amount does not exceed the transaction limit

3. A database transaction is started using:

```sql
START TRANSACTION

```TRANSFER
ADD_MONEY
```
## How Add Money Works

PaySphere includes an Add Money feature that simulates adding funds to the user's wallet.

The process works as follows:

1. The user enters the amount they want to add.

2. The application validates:
   - the amount is numeric
   - the amount is greater than zero
   - the amount does not exceed the maximum allowed limit

3. A database transaction is started.

4. The user's wallet balance is updated.

5. A new transaction record is created with:
   - sender ID
   - receiver ID
   - amount
   - transaction type as `ADD_MONEY`
   - transaction status as `SUCCESS`
   - unique reference ID

6. For Add Money, the same user is stored as both sender and receiver because it represents a wallet top-up simulation.

7. If everything succeeds, the transaction is committed.

8. If any operation fails, the transaction is rolled back.

9. After success, the application redirects using the POST-Redirect-GET pattern to avoid accidental duplicate submission on page refresh.

## Setup Instructions

Follow these steps to run PaySphere locally.

1. Install XAMPP.

2. Open XAMPP Control Panel and start:
   - Apache
   - MySQL

3. Copy the project folder into:

```text
C:\xampp\htdocs\PaySphere
```
4. Open phpMyAdmin in your browser.

5. Create a new database named:

```text
paysphere
```

6. Create the required `users` and `transactions` tables.

7. Make sure the database connection in `includes/db.php` is configured correctly:

```php
<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "paysphere"
);

if (!$conn) {
    die("Database Connection Failed");
}

?>
```

8. Open the project in your browser:

```text
http://localhost/PaySphere/
```

9. Register a new user account.

10. Log in and use the dashboard to:

- Add money
- Send money
- View transactions
- Edit profile information
- Change password


## Main Application Pages

### Dashboard

The dashboard displays:

- Current wallet balance
- Send Money shortcut
- Add Money shortcut
- Transaction history shortcut
- Recent transactions
- User profile options

### Send Money

The Send Money page allows a logged-in user to transfer wallet balance to another registered PaySphere user using their email address.

### Add Money

The Add Money page simulates adding funds to the user's PaySphere wallet.

### Transactions

The Transactions page provides:

- Full transaction history
- Search functionality
- Transaction type filters
- Transaction status filters
- Pagination
- Transaction details


## Transaction Types

PaySphere currently uses two transaction types:

```text
TRANSFER
ADD_MONEY
```

`TRANSFER` represents money sent between two registered PaySphere users.

`ADD_MONEY` represents a simulated wallet top-up.


## Transaction Status

The application supports the following transaction statuses:

```text
SUCCESS
PENDING
FAILED
```

At the current stage of the project, successful wallet operations are mainly recorded with the `SUCCESS` status.


## Transaction Reference ID

Every transaction receives a unique reference ID.

A reference ID is generated using:

- The `PS` prefix
- Current date and time
- Random bytes

Example:

```text
PS20260908184530A1B2C3
```

This makes every wallet transaction easier to identify and track.


## Important Backend Concepts Used

This project demonstrates several backend development concepts:

- PHP sessions
- Authentication
- Password hashing
- Prepared statements
- SQL joins
- Database transactions
- Row locking
- CSRF protection
- Form validation
- POST-Redirect-GET pattern
- Pagination
- Search and filtering
- Transaction history management


## Future Improvements

Possible future improvements for PaySphere include:

- Real payment gateway integration
- Email verification
- Forgot password functionality
- OTP verification
- Admin dashboard
- Transaction analytics
- User notifications
- Two-factor authentication
- Improved audit logging
- Stronger idempotency protection for payment requests
- Deployment to a production server


## Project Scope

PaySphere is an educational digital wallet simulation.

It is designed to demonstrate full-stack web development, authentication, secure database operations, transaction handling, and wallet-style functionality.

It does not process real bank payments or real financial transactions.


## Author

Developed as a full-stack PHP and MySQL project for learning, portfolio demonstration, and interview preparation.
## Conclusion

PaySphere demonstrates a complete digital wallet workflow using PHP and MySQL.

The project combines authentication, session handling, secure database operations, wallet transfers, transaction tracking, profile management, and responsive frontend design.

It is intended as a portfolio and interview project to demonstrate practical full-stack development concepts.