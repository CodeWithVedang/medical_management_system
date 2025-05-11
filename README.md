Below is the updated `README.md` file for your Medical Management System project, incorporating the addition of a `.sql` file that can be imported directly to set up the database. This README reflects the current project structure (after removing email and PDF functionalities) and provides clear setup instructions, project information, and usage details.

---

# Medical Management System

## Overview

The Medical Management System is a web-based application designed to manage medicine sales for a pharmacy or medical store. It allows users to add medicines to a cart, apply discounts, generate bills, and print them. The system also includes features for managing medicine inventory, viewing sales history, and a symptom checker.

This project focuses on simplicity and core functionality, providing an intuitive interface for customer sales while maintaining a lightweight structure.

---

## Features

- **Customer Selling**: Add medicines to a cart, apply a bill discount, and generate/print a bill with customer details.
- **Medicine Management**: Manage medicine inventory (add, update, delete medicines).
- **Sales History**: View past sales records.
- **Symptom Checker**: A tool to check symptoms (functionality depends on implementation).
- **Dashboard**: Overview of sales and inventory (functionality depends on implementation).

**Removed Features** (as per recent updates):
- Email sending functionality.
- Manual PDF generation and saving.

---

## Project Structure

```
medical_management_system/
├── css/
│   └── style.css              # Stylesheet for the application
├── js/
│   └── script.js             # General JavaScript (e.g., for navbar menu toggle)
├── database/
│   └── medical_system.sql 
├── php/
│   ├── config.php            
│   ├── get_bill.php         
│   └── process_sale.php      
│   └── add_medicine.php      
│   └── delete_medicine.php      
│   └── edit_medicine.php      
│   └── process_symptom.php  
│   └── view_medicines_dashboard.php      
│   └── view_medicine.php      
├── python/
│   └── symptom_checker.python
├── database/
│   └── medical_management_system.sql  # SQL file to import database schema and sample data
├── home.php                  # Home page
├── index.php                 # Medicine management page
├── dashboard.php             # Dashboard page
├── customer_selling.php      # Customer selling page (medicine sales and bill printing)
├── sales_history.php         # Sales history page
├── symptom_checker.php       # Symptom checker page
└── README.md                 # Project documentation (this file)
```

---

## Prerequisites

Before setting up the project, ensure you have the following installed:

1. **Web Server**: Apache (e.g., via XAMPP, WAMP, or MAMP).
2. **PHP**: Version 7.4 or higher.
3. **MySQL**: For the database.
4. **Browser**: A modern web browser (e.g., Chrome, Firefox).

---

## Setup Instructions

Follow these steps to set up and run the Medical Management System on your local machine.

### 1. Clone or Download the Project

- Clone the repository or download the project files to your local machine.
- Place the `medical_management_system` folder in your web server's root directory (e.g., `htdocs` for XAMPP).

### 2. Set Up the Database

1. **Start MySQL**: Ensure your MySQL server is running (e.g., via XAMPP control panel).
2. **Import the Database**:
   - Open phpMyAdmin (e.g., `http://localhost/phpmyadmin`).
   - Create a new database named `medical_management_system`.
   - Go to the "Import" tab in phpMyAdmin.
   - Click "Choose File" and select `database/medical_management_system.sql` from the project folder.
   - Click "Go" to import the database schema and sample data.
   - This will create the necessary tables (`medicines`, `sales`, `sale_items`) and insert sample medicines for testing.

### 3. Configure the Database Connection

1. Open `php/config.php`.
2. Update the database credentials as needed:

   ```php
   <?php
   $host = 'localhost';
   $db = 'medical_management_system';
   $user = 'root'; // Replace with your MySQL username
   $pass = '';     // Replace with your MySQL password

   $conn = new mysqli($host, $user, $pass, $db);

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```

### 4. Start the Web Server

- Start Apache and MySQL (e.g., via XAMPP control panel).
- Ensure the project is accessible at `http://localhost/medical_management_system/`.

### 5. Access the Application

- Open your browser and navigate to `http://localhost/medical_management_system/`.
- Start with `customer_selling.php` to test the core functionality:
  - Go to `http://localhost/medical_management_system/customer_selling.php`.
  - Add medicines to the cart, apply a discount if needed, and generate a bill.
  - Enter customer details and print the bill.

---

## Usage

### Customer Selling (`customer_selling.php`)

1. **Add Medicines to Cart**:
   - Select a medicine from the dropdown.
   - Enter the quantity.
   - Click "Add" to add the medicine to the cart.
2. **Apply Discount**:
   - Enter a bill discount percentage (0-100).
   - The total price will update automatically.
3. **Generate Bill**:
   - Click "Generate Bill".
   - Enter customer details (name, phone, optional doctor name) in the modal.
   - Submit to generate the bill.
4. **Print Bill**:
   - In the "Bill Preview" section, click "Print Bill" to open the bill in a new window and print it.

### Other Pages

- **Home (`home.php`)**: Landing page with navigation links.
- **Medicine Management (`index.php`)**: Manage medicine inventory.
- **Dashboard (`dashboard.php`)**: View sales and inventory statistics.
- **Sales History (`sales_history.php`)**: View past sales.
- **Symptom Checker (`symptom_checker.php`)**: Check symptoms (if implemented).

---

## Troubleshooting

1. **"Connection failed" Error**:
   - Check your database credentials in `php/config.php`.
   - Ensure MySQL is running.

2. **Bill Not Generating**:
   - Check the PHP error log for issues in `process_sale.php`.
   - Ensure `sale_id` is set in the session (check `process_sale.php`).

3. **Print Bill Not Working**:
   - Check the browser console (F12 → Console) for JavaScript errors.
   - Ensure `php/get_bill.php` is working and returning valid HTML.

4. **Styles Not Loading**:
   - Ensure `css/style.css` exists and is accessible.
   - Check the browser’s Network tab (F12 → Network) for 404 errors.

5. **Database Import Issues**:
   - Ensure the `medical_management_system.sql` file is not corrupted.
   - Verify that the database name in `config.php` matches the imported database (`medical_management_system`).

---

## Technologies Used

- **Frontend**:
  - HTML
  - CSS (`css/style.css`)
  - JavaScript (`js/script.js` for general scripts, inline scripts in `customer_selling.php`)
- **Backend**:
  - PHP
  - MySQL
- **Server**:
  - Apache (via XAMPP/WAMP/MAMP)
- **AIML**:
  - python  

---

## Notes

- The project no longer supports email sending or manual PDF generation, focusing on core functionality (medicine sales and bill printing).
- Ensure `process_sale.php` and `get_bill.php` are correctly implemented to handle sale processing and bill generation.
- If additional features are needed (e.g., PDF generation, email sending), they can be re-added with appropriate libraries (e.g., TCPDF, PHPMailer).

---

## License

This project is for educational purposes and does not include a specific license. Modify and use it as needed for personal or academic projects.

---

This README includes the addition of the `.sql` file for easy database setup and reflects the current state of the project. Let me know if you need further adjustments!