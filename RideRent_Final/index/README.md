# RideRent Pro - Vehicle Rental Management System

A complete PHP-based vehicle rental management system with professional folder structure and multiple user roles.

## 📁 Project Structure

```
RideRentPro Project/
├── config/                          # Configuration files
│   ├── config.php                 # Application configuration
│   ├── database.php               # Database connection
│   └── database/                  # Database files
│       └── riderent_prodb.sql
├── includes/                       # Shared includes
│   ├── header.php                 # Page header
│   ├── footer.php                 # Page footer
│   ├── sidebar.php                # Dynamic sidebar
│   └── functions.php              # Helper functions
├── assets/                         # Static assets
│   ├── css/                       # Stylesheets
│   │   ├── style.css              # Main stylesheet
│   │   ├── add_vehicle.css        # Vehicle form styles
│   │   └── vehicle.css            # Vehicle styles
│   ├── js/                        # JavaScript files
│   ├── images/                    # Image assets
│   └── uploads/                   # User uploads (vehicles, profiles)
├── admin/                          # Admin panel
│   ├── dashboard.php              # Admin dashboard
│   ├── users.php                  # User management
│   ├── drivers.php                # Driver management
│   ├── bookings.php               # Booking management
│   ├── reviews.php                # Review management
│   ├── ratings.php                # Ratings overview
│   ├── reports.php                # Reports page
│   └── vehicles/                  # Vehicle management
│       ├── vehicle_list.php       # Vehicle list
│       ├── add_vehicle.php        # Add vehicle
│       ├── edit_vehicle.php       # Edit vehicle
│       ├── delete_vehicle.php     # Delete vehicle
│       └── vehicle_details.php    # Vehicle details
├── owner/                          # Vehicle owner panel
│   ├── dashboard.php              # Owner dashboard
│   ├── vehicles.php               # Owner's vehicles
│   ├── bookings.php               # Owner's bookings
│   └── drivers.php                # Available drivers
├── driver/                         # Driver panel
│   ├── dashboard.php              # Driver dashboard
│   ├── bookings.php               # Driver's bookings
│   └── profile.php                # Driver profile
├── customer/                       # Customer panel
│   ├── dashboard.php              # Customer dashboard
│   ├── vehicles.php               # Browse vehicles
│   ├── book_vehicle.php           # Book a vehicle
│   ├── bookings.php               # Customer's bookings
│   └── profile.php                # Customer profile
├── auth/                           # Authentication
│   ├── login.html                 # Login page
│   ├── login.php                  # Login handler
│   ├── register.php               # Registration handler
│   └── logout.php                 # Logout handler
├── HomePage.html                   # Original landing page (deprecated)
├── index.php                       # New PHP homepage (recommended)
├── test_db.php                     # Database connection test
└── README.md                       # This file
```

## 🚀 Features

### User Roles
- **Admin**: Full system management, user management, vehicle oversight, booking management
- **Vehicle Owner**: Manage own vehicles, view bookings, track earnings
- **Driver**: View assigned bookings, manage profile, track earnings
- **Customer**: Browse vehicles, book rentals, view booking history

### Key Functionality
- Vehicle management (add, edit, delete vehicles)
- Driver management with ratings
- Booking system with payment tracking
- User authentication and registration
- Dashboard for each user type
- Search and filter vehicles
- Profile management
- Professional folder structure

## 📋 Database Setup

1. Import the SQL file into your MySQL database:
   ```bash
   mysql -u root -p riderent_prodb < config/database/riderent_prodb.sql
   ```

2. Or use phpMyAdmin to import the SQL file from `config/database/`

## ⚙️ Configuration

### Database Connection
Edit `config/database.php` to match your database credentials:

```php
$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL password
$dbname = "riderent_prodb";
```

### Application Configuration
Edit `config/config.php` for application settings:

```php
define('APP_NAME', 'RideRent Pro');
define('APP_URL', 'http://localhost/RideRentPro%20Project');
define('DB_NAME', 'riderent_prodb');
```

## 🔑 Default Login Credentials

### Admin
- Email: ornima5170@gmail.com
- Password: ornima123

### Vehicle Owner
- Email: masud@gmail.com
- Password: masud123

### Driver
- Email: rahim.driver@gmail.com
- Password: rahim123

### Customer
- Email: mahmud@gmail.com
- Password: mahmud123

## � Database Name Change

The database has been renamed from `riderent_pro299` to `riderent_prodb`. All configuration files have been updated accordingly.

## �🛠️ Installation Requirements

1. **Web Server**: Apache/Nginx
2. **PHP**: Version 7.4 or higher
3. **MySQL**: Version 5.7 or higher / MariaDB 10.4+
4. **PHP Extensions**: mysqli, gd (for image processing)

## 📦 Setup Instructions

### Option 1: XAMPP/WAMP (Recommended for Windows)

1. Install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services
3. Copy the project folder to `htdocs`
4. Import the SQL file using phpMyAdmin
5. Update database credentials in `config/database.php`
6. Access the application at `http://localhost/RideRentPro%20Project/`

### Option 2: Local PHP Server

1. Install PHP and MySQL
2. Start MySQL server
3. Import the SQL file
4. Run PHP built-in server:
   ```bash
   cd "RideRentPro Project"
   php -S localhost:8000
   ```
5. Access at `http://localhost:8000`

## 🎯 Usage

1. Open `index.php` in your browser (the new PHP homepage)
2. Login with appropriate credentials using `auth/login.html`
3. Navigate through the dashboard based on your role
4. Register new users through the registration page (`auth/register.php`)
5. Test database connection at `test_db.php`

## 🧪 Testing

Run the database connection test:
```bash
php config/test_connection.php
```

Or access it in your browser:
```
http://localhost/RideRentPro%20Project/config/test_connection.php
```

## 🔧 Troubleshooting

### Database Connection Issues
- Ensure MySQL server is running
- Check credentials in `config/database.php`
- Verify database name matches
- Ensure mysqli extension is enabled in PHP

### Image Upload Issues
- Ensure `assets/uploads/` folder has write permissions
- Check PHP upload_max_filesize and post_max_size settings
- Verify GD library is installed

### Session Issues
- Ensure PHP session storage directory is writable
- Check session.save_path in php.ini

### Path Issues
- Make sure `APP_URL` in `config/config.php` matches your server
- Check folder permissions

## 🔒 Security Notes

- This is a development/educational project
- In production, implement:
  - Password hashing (use password_hash() instead of plain text)
  - Prepared statements for all SQL queries
  - CSRF protection
  - Input validation and sanitization
  - SSL/HTTPS
  - Rate limiting
  - File upload validation

## 🚧 Future Enhancements

- Email notifications
- Payment gateway integration
- Real-time availability checking
- Advanced search and filtering
- Mobile app version
- Multi-language support
- Advanced reporting and analytics
- API endpoints for mobile apps

## 📝 License

This project is for educational purposes.

## 🤝 Support

For issues or questions, please refer to the project documentation or contact the development team.

## 🔄 Recent Updates

- Reorganized folder structure for better maintainability
- Added configuration files for easier setup
- Created shared includes for header, footer, sidebar
- Added helper functions for common operations
- Improved code organization and separation of concerns
- Updated all file paths to use relative references