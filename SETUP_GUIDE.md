# InnovateX Event Management System - Setup Guide

## Files Created

### Database
- `database/schema.sql` - Database schema with tables for admins, events, and registrations

### Admin Panel
- `admin_login.php` - Admin authentication page
- `admin_dashboard.php` - Main admin dashboard with event management
- `admin_add_event.php` - Add new event form
- `admin_edit_event.php` - Edit existing event form
- `admin_view_registrations.php` - View registrations for each event
- `logout.php` - Logout functionality

### Public Pages
- `events.php` - Public events page with card-based layout
- `register.php` - Event registration form

### Configuration
- `config.php` - Database connection and utility functions

## Setup Instructions

### 1. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click on "Import" tab
3. Select `database/schema.sql` file
4. Click "Go" to import the database

### 2. Create Uploads Directory
Create a folder named `uploads` in your project root:
```
c:\xampp\htdocs\InnovateX-Website\uploads
```
Make sure it has write permissions.

### 3. Default Admin Credentials
- **Username:** admin
- **Password:** admin123

### 4. Access the System

**Admin Panel:**
- http://localhost/InnovateX-Website/admin_login.php

**Events Page:**
- http://localhost/InnovateX-Website/events.php

**Home Page:**
- http://localhost/InnovateX-Website/index.php

## Features

### Admin Panel Features
✅ Secure login system
✅ Dashboard with statistics (total events, upcoming events, registrations)
✅ Add new events with:
  - Cover image upload
  - Event details (title, description, prize money, date, event head)
  - Rule book PDF upload
  - Registration link
✅ Edit existing events
✅ Delete events (with file cleanup)
✅ View all registrations for each event

### Events Page Features
✅ Hero section with background image
✅ Responsive card-based layout
✅ Event cards display:
  - Cover image
  - Prize money badge
  - Event title & description
  - Event date
  - Event head name
  - Register button
  - Download rules button
✅ Fully responsive design
✅ Matches home page theme

### Registration System
✅ Team-based registration
✅ Collects: team name, leader info, team size, college name
✅ Email validation
✅ Success/error messages

## File Upload Limits
- Maximum file size: 5MB
- Allowed image formats: JPG, JPEG, PNG, WEBP
- Allowed document format: PDF

## Security Features
- Password hashing using PHP password_hash()
- SQL injection protection using prepared statements
- XSS prevention using htmlspecialchars()
- Session-based authentication
- File type validation for uploads

## Design Theme
- **Title Font:** ZENTRY (font-zentry)
- **Body Font:** ROBERT (font-robert-regular, font-robert-medium)
- **Color Scheme:** Matches InnovateX home page
  - Primary: #010101 (blue-200)
  - Accent: #4fb7dd (blue-300)
  - Background: #dfdff0 (blue-50)
  - Highlight: #edff66 (yellow-300)

## Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check database credentials in `config.php`
- Ensure database is imported

### Upload Errors
- Check `uploads` folder exists
- Verify folder has write permissions
- Check PHP upload_max_filesize in php.ini

### Image Not Displaying
- Ensure files are in the `uploads` folder
- Check file paths in database
- Verify image file extensions

## Next Steps
1. Change default admin password
2. Add more admin users if needed
3. Customize email notifications (future enhancement)
4. Add export functionality for registrations (future enhancement)

## Support
For issues or questions, refer to the code comments in each file.
