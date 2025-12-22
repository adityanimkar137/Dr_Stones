# Dr_Stones

Dr_Stones is a web-based application for managing rare stones, allowing users to submit proposals for new stones, place orders, and for admins to manage items, orders, and proposals.

---

## Table of Contents

- [Features](#features)  
- [Technology Stack](#technology-stack)  
- [Database Structure](#database-structure)  
- [Installation](#installation)  
- [Usage](#usage)  
- [Admin Login](#admin-login)  
- [Project Structure](#project-structure)  

---

## Features

- User registration and authentication  
- Submit proposals for new stones with image upload  
- Admin approval/rejection of stone proposals  
- Browse and place orders for existing stones  
- Manage orders, items, and proposals from the admin panel  

---

## Technology Stack

- **Backend:** PHP 8+  
- **Database:** MySQL / MariaDB  
- **Frontend:** HTML, CSS, JavaScript  
- **Server:** Apache / Nginx  

---

## Database Structure

### Database: `Stones`

**Tables:**

1. `users`  
   - `user_id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`  

2. `items`  
   - `id`, `name`, `subtitle`, `description`, `price`, `weight`, `origin`, `era`, `image`, `created_at`  

3. `proposals`  
   - `id`, `stone_name`, `stone_subtitle`, `stone_description`, `price`, `weight`, `origin`, `era`, `image`, `vendor_name`, `vendor_email`, `status`, `created_at`  

4. `orders`  
   - `id`, `user_id`, `item_id`, `date`, `status`  

5. `stones`  
   - `id`, `name`, `subtitle`, `description`, `price`, `origin`, `era`, `weight`, `image`, `created_at`, `updated_at`  

---


