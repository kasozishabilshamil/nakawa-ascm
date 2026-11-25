# Nakawa ASCM (Agricultural Supply Chain Management)

A centralized, web-based digital platform designed to digitize and streamline the agricultural supply chain at Nakawa Market, Kampala, Uganda.

## Project Overview
Developed as a final year project for Makerere University Business School (MUBS), this platform connects Farmers, Vendors, Transporters, and Administrators into a single ecosystem to solve issues of information asymmetry, poor logistics, and post-harvest losses.

## Technologies Used
* **Frontend:** HTML5, CSS3, JavaScript, Font Awesome
* **Backend:** PHP (Native)
* **Database:** MySQL

## Features
* **Role-based Dashboards:** Different views for Farmers, Vendors, Transporters, and Admins.
* **Real-time Market Prices:** Admin can update prices which are visible to everyone.
* **Marketplace:** Vendors can browse produce listed by farmers and place orders.
* **Logistics Tracking:** Transporters can accept orders and update delivery statuses.

## How to Install and Run Locally
1. Clone this repository into your XAMPP `htdocs` folder.
2. Open phpMyAdmin and create a database named `nakawa_ascm_db`.
3. Import the provided SQL file (or run the table creation scripts).
4. Update `db_connect.php` with your database credentials.
5. Open your browser and go to `http://localhost/nakawa-ascm/index.php`.

## Test Accounts
* **Admin:** admin@test.com / password123
* **Farmer:** david@farmer.com / password123
* **Vendor:** vendor@test.com / password123
* **Transporter:** transport@test.com / password123

---
*Developed by BBC DISCUSSANTS GROUP - Makerere University Business School, 2026.*
