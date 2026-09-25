# 🚨 Emergency BD

### A Real-Time Emergency & Crime Reporting Platform for Bangladesh

**Emergency BD** is a full-stack web application designed to provide citizens with a centralized platform for reporting emergencies and crime incidents in Bangladesh.

The system allows users to submit incident reports with **GPS coordinates, incident classification, threat intensity, descriptions, evidence, and anonymous reporting options**. Administrators can review submitted reports, approve or reject incidents, manage citizen accounts, and monitor verified incidents through a map-based interface.

---

## 🎯 Project Overview

Emergency situations often require fast access to reliable information and coordinated responses. Emergency BD aims to provide a structured digital platform where citizens can report incidents while administrators can verify and manage those reports.

The application follows a **citizen → moderation → verified incident** workflow:

```text
Citizen
   │
   ├── Register / Login
   │
   ├── Detect or Search Location
   │
   ├── Submit Emergency Report
   │
   ├── Attach Evidence
   │
   └── Report Anonymously
   │
   ▼
Admin Dashboard
   │
   ├── Review Pending Reports
   ├── Approve / Reject
   ├── Manage Users
   └── Assign Contribution Points
   │
   ▼
Verified Emergency Data
   │
   └── Displayed on the Emergency Map
```

---

## ✨ Features

### 👤 Citizen Features

* 🔐 User registration and login
* 🔑 Token-based authentication using Laravel Sanctum
* 🚨 Emergency / crime reporting
* 📍 Automatic GPS location detection
* 🔎 Manual address search
* 🗺️ Location-based incident visualization
* 📊 Threat intensity level from **1–5**
* 📝 Detailed incident descriptions
* 👤 Optional victim information
* 🕵️ Anonymous reporting
* 📷 Photo evidence upload
* 🎥 Video evidence upload
* 🔗 External news/article evidence links
* 🎫 Contribution/token point system
* 💬 Request additional points from administrators
* 🚑 Quick access to Bangladesh emergency contacts

### 🛡️ Admin Features

* 🔐 Dedicated administrator dashboard
* 📋 View pending incident reports
* ✅ Approve verified reports
* ❌ Reject reports
* 👥 View registered citizens
* 🚫 Suspend citizen accounts
* 🔓 Reactivate suspended accounts
* 🎫 Assign contribution points
* 🗑️ Delete reports
* 🗑️ Delete user accounts
* 🗺️ View approved incidents on the map

### 🌍 Emergency Information

The application provides quick access to:

* **National Emergency:** `999`
* **DMP Police Headquarters:** `+880 223381967`
* **Bangladesh Police Web Portal**

---

## 🏗️ System Architecture

```text
┌───────────────────────────────┐
│         Vue.js Frontend       │
│                               │
│  Authentication               │
│  Citizen Dashboard            │
│  Report Form                  │
│  Interactive Map              │
│  Admin Dashboard              │
└───────────────┬───────────────┘
                │
                │ REST API / Axios
                ▼
┌───────────────────────────────┐
│       Laravel Backend         │
│                               │
│  Authentication               │
│  Report Management            │
│  Admin Management             │
│  Validation                   │
│  File Upload                  │
│  API Endpoints                │
└───────────────┬───────────────┘
                │
                ▼
┌───────────────────────────────┐
│          SQLite Database      │
│                               │
│  Users                        │
│  Crime Reports                │
│  Reports                      │
│  Sessions / Cache / Jobs      │
└───────────────────────────────┘
```

---

## 🛠️ Technology Stack

### Frontend

| Technology       | Purpose                         |
| ---------------- | ------------------------------- |
| **Vue 3**        | User interface                  |
| **Vite**         | Frontend development/build tool |
| **Vue Router**   | Client-side routing             |
| **Axios**        | HTTP/API communication          |
| **Tailwind CSS** | UI styling                      |
| **Leaflet**      | Interactive maps                |
| **JavaScript**   | Application logic               |

### Backend

| Technology          | Purpose                        |
| ------------------- | ------------------------------ |
| **PHP 8.2+**        | Backend language               |
| **Laravel 12**      | Backend framework              |
| **Laravel Sanctum** | API authentication             |
| **SQLite**          | Database                       |
| **Eloquent ORM**    | Database interaction           |
| **REST API**        | Frontend-backend communication |

---

## 📁 Project Structure

```text
Emergency-BD-CSE470/
│
├── backend/
│   │
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Api/
│   │   │   │   │   ├── AdminController.php
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   └── CrimeReportController.php
│   │   │   │   └── ReportController.php
│   │   │   └── Middleware/
│   │   │
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── CrimeReport.php
│   │   │   └── Report.php
│   │   │
│   │   └── Providers/
│   │
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   │       └── AdminSeeder.php
│   │
│   ├── routes/
│   │   ├── api.php
│   │   ├── web.php
│   │   └── console.php
│   │
│   ├── resources/
│   ├── storage/
│   ├── composer.json
│   └── package.json
│
├── frontend/
│   │
│   ├── src/
│   │   ├── components/
│   │   │   ├── AdminDashboard.vue
│   │   │   ├── AuthComponent.vue
│   │   │   ├── Dashboard.vue
│   │   │   ├── EmergencyContacts.vue
│   │   │   ├── MapComponent.vue
│   │   │   └── ReportForm.vue
│   │   │
│   │   ├── router/
│   │   │   └── index.js
│   │   │
│   │   ├── App.vue
│   │   ├── main.js
│   │   └── style.css
│   │
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
│
└── README.md
```

---

## 🔐 Authentication

Emergency BD uses **Laravel Sanctum** for API authentication.

### Registration

```http
POST /api/register
```

Required information:

```json
{
    "name": "User Name",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

New citizens receive:

* `user` account
* `citizen` role
* `active` status
* Initial contribution points
* Sanctum authentication token

### Login

```http
POST /api/login
```

Example:

```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

The returned token is used for authenticated API requests:

```http
Authorization: Bearer <TOKEN>
```

---

## 🚨 Emergency Report Workflow

A citizen can submit an incident containing:

```text
Incident Type
      ↓
Threat Intensity (1–5)
      ↓
Description
      ↓
GPS Coordinates
      ↓
Area / City
      ↓
Photo / Video Evidence
      ↓
External Evidence Link
      ↓
Anonymous Reporting Option
      ↓
Submit
```

New reports are initially assigned:

```text
status = pending
```

The administrator can then:

```text
pending
   ├── approved
   └── rejected
```

Only approved reports are intended to be used for verified map information.

---

## 📍 Location & Mapping

The reporting system supports geographic information including:

* Latitude
* Longitude
* Area
* City

Users can either:

1. Automatically detect their current GPS position, or
2. Search for an address manually.

Approved incident locations can then be displayed through the application's map interface.

---

## 📷 Evidence System

Users can attach supporting evidence to their reports.

Supported evidence includes:

* `.jpg`
* `.jpeg`
* `.png`
* `.mp4`

The backend validates uploaded media and stores it through Laravel's public storage system.

External news/article links can also be submitted as supporting evidence.

---

## 🕵️ Anonymous Reporting

Users can enable:

```text
Protect Identity
```

When anonymous reporting is selected, the report is marked with:

```text
is_anonymous = true
```

This allows the incident to be submitted without publicly exposing the reporting user's identity.

---

## 🎫 Contribution Point System

Emergency BD includes a contribution/token mechanism.

New users receive initial points.

Submitting a report consumes one point for eligible citizen accounts.

If a citizen runs out of points, they can request additional points from an administrator.

Administrators can manually assign additional points through the admin dashboard.

---

## 👨‍💼 Admin Dashboard

The administrator dashboard provides a centralized moderation interface.

### Pending Reports

Administrators can inspect:

* Report ID
* Reporter
* Incident classification
* Intensity
* Current status

Available actions:

```text
VERIFY
REJECT
```

### User Management

Administrators can:

```text
View Users
     │
     ├── Suspend Account
     ├── Reactivate Account
     ├── Assign Points
     └── Delete Account
```

---

## 🔌 API Endpoints

### Authentication

| Method | Endpoint        | Description               |
| ------ | --------------- | ------------------------- |
| `POST` | `/api/register` | Register a new user       |
| `POST` | `/api/login`    | Authenticate user         |
| `POST` | `/api/logout`   | Logout authenticated user |

### Citizen

| Method | Endpoint              | Description               |
| ------ | --------------------- | ------------------------- |
| `POST` | `/api/reports`        | Submit emergency report   |
| `POST` | `/api/points/request` | Request additional points |

### Reports

| Method   | Endpoint                   | Description                |
| -------- | -------------------------- | -------------------------- |
| `GET`    | `/api/reports/pending`     | Retrieve pending reports   |
| `GET`    | `/api/reports/map`         | Retrieve approved map data |
| `PATCH`  | `/api/reports/{id}/status` | Approve/reject report      |
| `DELETE` | `/api/reports/{id}`        | Delete report              |

### User Management

| Method   | Endpoint                        | Description             |
| -------- | ------------------------------- | ----------------------- |
| `GET`    | `/api/users`                    | Retrieve citizen users  |
| `POST`   | `/api/users/{id}/toggle-status` | Suspend/reactivate user |
| `POST`   | `/api/users/{id}/assign-points` | Assign points           |
| `DELETE` | `/api/users/{id}`               | Delete user             |

---

# 🚀 Installation & Setup

## Prerequisites

Make sure the following are installed:

* PHP `8.2+`
* Composer
* Node.js
* npm
* Git

---

## 1. Clone the Repository

```bash
git clone https://github.com/RawnakAhasan911/Emergency-BD-CSE470.git
cd Emergency-BD-CSE470
```

---

# ⚙️ Backend Setup

Navigate to the backend:

```bash
cd backend
```

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

---

## 🗄️ Database Setup

The project is configured to work with SQLite.

Make sure the database exists:

```bash
touch database/database.sqlite
```

Run migrations:

```bash
php artisan migrate
```

Seed the administrator account:

```bash
php artisan db:seed --class=AdminSeeder
```

---

## 📦 Frontend Dependencies

From the `backend` directory, install the required Node dependencies if needed:

```bash
npm install
```

Build frontend assets:

```bash
npm run build
```

---

# 🌐 Frontend Setup

Open another terminal and navigate to:

```bash
cd frontend
```

Install dependencies:

```bash
npm install
```

Start the development server:

```bash
npm run dev
```

The Vite development server will provide the frontend URL in the terminal.

---

# 🖥️ Running the Backend

From the `backend` directory:

```bash
php artisan serve
```

The Laravel API will normally be available at:

```text
http://127.0.0.1:8000
```

The frontend communicates with the Laravel API through Axios.

---

# 🔑 Default Administrator

The project includes an administrator seeder.

Default administrator credentials configured by the current project:

```text
Email:    admin@emergency.bd
Password: admin1234
```

> ⚠️ Change the default administrator credentials before deploying the application to a production environment.

---

# 🧪 Testing

Laravel's testing framework can be executed using:

```bash
php artisan test
```

Or:

```bash
composer test
```

---

# 🔒 Security Considerations

The project incorporates several security mechanisms:

* Laravel Sanctum API tokens
* Password hashing through Laravel
* Request validation
* Authentication middleware
* Role-based application logic
* File type and size validation
* Protected administrative endpoints
* Hidden password/token attributes in user responses

For production deployment, additional hardening should be applied, including:

* Strong production secrets
* HTTPS
* Secure environment variables
* Proper role/permission middleware enforcement
* Rate limiting
* Production database configuration
* Secure file storage
* Input/output security review

---

# 🎓 Academic Context

This project was developed as part of:

**CSE470 — Software Engineering**

The project demonstrates concepts including:

* Full-stack web application development
* RESTful API design
* Authentication and authorization
* Database design
* CRUD operations
* Role-based functionality
* Geographic information systems
* File upload handling
* User management
* Software architecture
* Frontend-backend integration

---

# 👥 Project

**Emergency BD**

Repository:

[Emergency-BD-CSE470 on GitHub](https://github.com/RawnakAhasan911/Emergency-BD-CSE470?utm_source=chatgpt.com)

Developed using:

```text
Vue.js
Laravel
PHP
SQLite
Tailwind CSS
JavaScript
Axios
Leaflet
Laravel Sanctum
```

---

## 📄 License

This project was developed for academic purposes as part of the CSE470 Software Engineering course.
