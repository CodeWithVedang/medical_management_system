# Medical Management System

A web-based system to manage medicines, provide statistical insights, handle sales and billing, and view sales history. Built for academic purposes using HTML, CSS, JavaScript, PHP, MySQL, Python, and XAMPP.

## Modules
1. **Medicine Management**: CRUD operations for medicines.
2. **Dashboard**: Statistical data, total sales, and available medicine inventory with charts.
3. **Customer Selling**: Medicine sales, billing, and invoice generation for multiple medicines with customer details.
4. **Sales History**: Filter sales by customer name to view detailed sales history, with bill printing.

## Setup Instructions
1. Install **XAMPP** and start Apache and MySQL.
2. Copy this folder to `C:\xampp\htdocs\medical_management_system`.
3. Create a database named `medical_system` in phpMyAdmin.
4. Run the following SQL to create the required tables:

## SQL QUERIES

   CREATE TABLE medicines (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(100) NOT NULL,
       type VARCHAR(50) NOT NULL,
       expiry_date DATE NOT NULL,
       price DECIMAL(10, 2) NOT NULL,
       stock INT NOT NULL
   );

   CREATE TABLE sales (
       id INT AUTO_INCREMENT PRIMARY KEY,
       customer_name VARCHAR(100) NOT NULL,
       customer_phone VARCHAR(20) NOT NULL,
       doctor_name VARCHAR(100),
       discount DECIMAL(5, 2) DEFAULT 0.00,
       total_price DECIMAL(10, 2) NOT NULL,
       sale_date DATETIME NOT NULL
   );

   CREATE TABLE sale_items (
       id INT AUTO_INCREMENT PRIMARY KEY,
       sale_id INT NOT NULL,
       medicine_id INT NOT NULL,
       quantity INT NOT NULL,
       subtotal DECIMAL(10, 2) NOT NULL,
       FOREIGN KEY (sale_id) REFERENCES sales(id),
       FOREIGN KEY (medicine_id) REFERENCES medicines(id)
   );
