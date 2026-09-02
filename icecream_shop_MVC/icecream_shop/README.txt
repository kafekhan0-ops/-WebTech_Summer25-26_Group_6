ICE CREAM DELIGHTS - PHP + MySQL PROJECT
==========================================

TECHNOLOGY
- PHP
- MySQL
- HTML
- Custom CSS (NO Bootstrap)
- Vanilla JavaScript
- XAMPP

1. INSTALL XAMPP
----------------
Install XAMPP and start Apache and MySQL.

2. COPY PROJECT
---------------
Copy the "icecream_shop" folder to:

C:\xampp\htdocs\

3. CREATE DATABASE
------------------
Open:

http://localhost/phpmyadmin

Click "Import", select:

icecream_shop/database/icecream_shop.sql

and click Go.

The SQL creates the database, tables and sample products.

4. CHECK DATABASE CONNECTION
----------------------------
The default XAMPP MySQL settings are:
Host: localhost
User: root
Password: empty
Database: icecream_shop

If your MySQL password is different, edit:
config/database.php

5. OPEN WEBSITE
---------------
http://localhost/icecream_shop/

6. CUSTOMER TEST
----------------
Register a new customer from:
http://localhost/icecream_shop/register.php

Then:
- Login
- Open Shop
- Add products
- Open Cart
- Checkout
- View My Orders

7. ADMIN TEST
-------------
Admin login:
URL: http://localhost/icecream_shop/admin/

Email: admin@icecream.com
Password: admin123

Admin can:
- See dashboard statistics
- Add products
- Edit products
- Delete products
- View customers
- View orders
- Change order status
- Read contact messages

8. IMAGE FILES
--------------
All sample product images are local SVG files in /images.
No Bootstrap or external CSS framework is used.

IMPORTANT
---------
This is a student/demo project. For a production deployment, add CSRF protection,
stronger admin controls, server-side authorization policies, HTTPS, and payment
gateway integration.

ACCOUNT MANAGEMENT FEATURES
- Customer: account.php provides view/edit profile, change password, and delete account.
- Customer: reset_password.php provides password reset using registered email + phone verification.
- Delivery Staff: delivery_profile.php provides view/edit profile and change password.
- Admin: admin/profile.php provides view/edit profile and change password.
