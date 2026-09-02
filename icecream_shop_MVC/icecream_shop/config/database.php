<?php
// XAMPP-friendly database connection. Automatically creates the demo database/tables if needed.
$host = "localhost";
$username = "root";
$password = "";
$dbname = "icecream_shop";

$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("MySQL connection failed: " . $conn->connect_error . "<br>Please start MySQL in XAMPP.");
}
$conn->set_charset("utf8mb4");
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
if (!$conn->select_db($dbname)) {
    die("Could not select database: " . $conn->error);
}

$conn->query("CREATE TABLE IF NOT EXISTS admins (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");
$conn->query("CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 phone VARCHAR(30) DEFAULT '',
 address TEXT,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");
$conn->query("CREATE TABLE IF NOT EXISTS products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 category VARCHAR(80) NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 description TEXT,
 image VARCHAR(255) NOT NULL DEFAULT 'vanilla.svg',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");
$conn->query("CREATE TABLE IF NOT EXISTS orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 customer_name VARCHAR(100) NOT NULL,
 phone VARCHAR(30) NOT NULL,
 address TEXT NOT NULL,
 payment_method VARCHAR(50) NOT NULL DEFAULT 'Cash on Delivery',
 total_amount DECIMAL(10,2) NOT NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB");
$conn->query("CREATE TABLE IF NOT EXISTS order_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 product_id INT NOT NULL,
 quantity INT NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB");
$conn->query("CREATE TABLE IF NOT EXISTS delivery_staff (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 phone VARCHAR(30) DEFAULT '',
 address TEXT,
 password VARCHAR(255) NOT NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

$dcol = $conn->query("SHOW COLUMNS FROM delivery_staff LIKE 'address'");
if ($dcol && $dcol->num_rows === 0) { $conn->query("ALTER TABLE delivery_staff ADD COLUMN address TEXT AFTER phone"); }

// Add delivery assignment to existing installations without breaking older databases.
$col = $conn->query("SHOW COLUMNS FROM orders LIKE 'delivery_staff_id'");
if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN delivery_staff_id INT NULL AFTER user_id");
}
$conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL,
 subject VARCHAR(200) DEFAULT '',
 message TEXT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Create the demo admin if it does not exist. The actual password is verified/updated in admin/index.php.
$demoHash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = 'admin@icecream.com' LIMIT 1");
$stmt->execute();
$res = $stmt->get_result();
if (!$res->fetch_assoc()) {
    $ins = $conn->prepare("INSERT INTO admins (name,email,password) VALUES ('Administrator','admin@icecream.com',?)");
    $ins->bind_param('s', $demoHash);
    $ins->execute();
}

// Create the demo delivery staff account.
$deliveryEmail = 'delivery@icecream.com';
$deliveryPassword = 'delivery123';
$ds = $conn->prepare("SELECT id FROM delivery_staff WHERE email = ? LIMIT 1");
$ds->bind_param('s', $deliveryEmail);
$ds->execute();
if (!$ds->get_result()->fetch_assoc()) {
    $dh = password_hash($deliveryPassword, PASSWORD_DEFAULT);
    $phone = '+880 1700-000000';
    $status = 'Active';
    $di = $conn->prepare("INSERT INTO delivery_staff (name,email,phone,password,status) VALUES ('Delivery Staff',?,?,?,?)");
    $di->bind_param('ssss', $deliveryEmail, $phone, $dh, $status);
    $di->execute();
}

// Add sample products only when the table is empty.
$check = $conn->query("SELECT COUNT(*) AS c FROM products");
if ($check && (int)$check->fetch_assoc()['c'] === 0) {
    $products = [
        ['Classic Vanilla','Classic',180,'Smooth vanilla ice cream with a rich creamy finish.','vanilla.svg'],
        ['Strawberry Bliss','Fruit',220,'Sweet strawberry ice cream with a bright fruity taste.','strawberry.svg'],
        ['Mint Dream','Classic',210,'Cool mint ice cream for a refreshing sweet treat.','mint.svg'],
        ['Chocolate Fudge','Chocolate',250,'Deep chocolate flavor with a soft, creamy texture.','chocolate.svg'],
        ['Mango Magic','Fruit',230,'Tropical mango ice cream made for sunny days.','mango.svg'],
        ['Berry Cheesecake','Special',280,'Creamy cheesecake ice cream with berry swirls.','berry.svg']
    ];
    $p = $conn->prepare("INSERT INTO products (name,category,price,description,image) VALUES (?,?,?,?,?)");
    foreach ($products as $row) { $p->bind_param('ssdss',$row[0],$row[1],$row[2],$row[3],$row[4]); $p->execute(); }
}
?>
