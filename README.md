# Project K7X2B-M5T7Q-Y7N2B

This README guide outlines the steps to set up a local PHP development environment using XAMPP, a free, open-source cross-platform web server solution stack package.

## Step 1: Download XAMPP

1. Visit the [Apache Friends website](https://www.apachefriends.org).
2. Download the XAMPP version compatible with your operating system.

## Step 2: Install XAMPP

Run the XAMPP installer and follow the on-screen instructions.

## Step 3: Set Up Your Project

### Create Project Folder

1. Navigate to the `htdocs` directory in your XAMPP installation folder.
2. Create a new folder for your project.

### Create index.php

1. Inside your project folder, create a file named `index.php`.
2. This file will serve as the default homepage for your project.

## Step 4: Run the Server

### Start XAMPP

1. Open the XAMPP control panel.
2. Start the Apache server.

### Access Your Project

1. Open a web browser.
2. Type `localhost/[project_folder_name]` in the address bar, replacing `[project_folder_name]` with your project's folder name.
3. Your `index.php` file will open in the browser.

## Using PHP

- Write PHP code in your `index.php` or other PHP files.
- The code will be executed on the server when accessed through the browser.

## Additional Notes

- Ensure no other services are running on the same port as the Apache server (commonly port 80).
- To use database functionalities, start the MySQL service in XAMPP to utilize MariaDB.

