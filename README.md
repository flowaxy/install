# Flowaxy CMS Installer

A separate installation module for Flowaxy CMS that provides a simple and convenient system installation process through a web interface.

## 📋 Description

Flowaxy CMS Installer is a separate installer that interacts with the system core through `ENGINE_DIR` and does not require loading the entire engine. After successful installation, the `install` directory can be completely deleted.

## 🚀 How to Use

### Step 1: Downloading the Installer

1. Download the installer from GitHub: [https://github.com/flowaxy/install](https://github.com/flowaxy/install)
2. Extract the archive to your project root
3. Make sure the `install` directory is at the same level as the `engine` directory

```
project/
├── engine/
├── install/          ← Installer here
├── storage/
└── index.php
```

### Step 2: Starting the Installation

1. Open in your browser: `https://your-domain.com/install`
2. The installation wizard will open with step-by-step instructions

### Step 3: Installation Process

The installation wizard includes the following steps:

1. **Welcome** — introduction to the system and its features
2. **System Check** — checking server requirements (PHP version, extensions, access rights)
3. **Database Setup** — entering data to connect to the MySQL database
4. **Table Creation** — automatic creation of all required tables
5. **Administrator Creation** — setting up the administrator account
6. **Completion** — final check and transition to the admin panel

### Step 4: Removing the Installer

After successful installation, the system will automatically delete the `install` directory and create a marker file `storage/config/installed.flag`.

## 📁 Directory Structure

```
install/
├── assets/                         # Static resources
│   ├── images/                     # Images and logos
│   ├── scripts/                    # JavaScript files
│   └── styles/                     # CSS styles
├── core/                           # Installer core
│   ├── InstallerController.php     # Installation controller
│   └── InstallerManager.php        # Installation manager
├── pages/                          # Installation wizard pages
│   ├── welcome.php                 # Welcome page
│   ├── system-check.php            # System check
│   ├── database.php                # Database setup
│   ├── tables.php                  # Table creation
│   ├── user.php                    # Administrator creation
│   └── success.php                 # Success page
├── templates/                      # Templates
│   └── installer.php               # Main template
├── index.php                       # Entry point
└── README.md                       # This file
```

## ⚙️ Requirements

### System Requirements

- **PHP**: 8.4.0 or higher
- **MySQL**: 5.7 or 8.0+ (8.0+ recommended)
- **PHP Extensions**:
  - `pdo`
  - `pdo_mysql`
  - `mbstring`
  - `json`
  - `openssl`
  - `session`

### Access Rights

The installer requires write permissions for the following directories:

- `storage/config/` — for saving database configuration
- `storage/cache/` — for cache
- `storage/logs/` — for logs
- `storage/temp/` — for temporary files

## 🔒 Security

- The installer automatically blocks access after successful installation
- Checking for the presence of the `storage/config/installed.flag` file ensures that re-installation is not possible without deleting the marker
- All confidential data (e.g., database passwords) is stored in encrypted form

## 📝 Notes

- The installer does not require loading the entire system engine, making the process faster
- After installation, the `install` directory can be deleted without risk to system operation
- The marker file `storage/config/installed.flag` is used by the system to check the installation status

## 🆘 Troubleshooting

### Installer Not Opening

1. Make sure the `install` directory is in the project root
2. Check directory access rights
3. Make sure the web server is configured correctly

### Database Connection Errors

1. Check the correctness of entered data (host, port, database name, user, password)
2. Make sure the MySQL server is running
3. Check if the database user has permissions to create tables

### Table Creation Errors

1. Check the database user's permissions to create and modify tables
2. Make sure the database exists and is accessible
3. Check log files in `storage/logs/` for error details

## 📄 License

This installer is part of Flowaxy CMS and is distributed under the same license terms.

---

*Developed with ❤️ for Flowaxy CMS*
