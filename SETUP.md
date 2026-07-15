 FitLife local setup (XAMPP)

## 1. Place the project in `htdocs`

Copy the complete project folder into the `htdocs` directory of the XAMPP installation. The folder can have any name; the examples below use `fitlife`.

The final layout must contain these files:

```text
htdocs/
`-- fitlife/
    |-- index.php
    |-- project_db_FULL_IMPORT.sql
    |-- config/
    |   `-- database.example.php
    |-- public/
    `-- views/
```

## 2. Start Apache and MySQL

Open the XAMPP Control Panel and start both **Apache** and **MySQL**. Note the port shown for each service:

- Apache normally uses port `80`; some XAMPP installations use `8080`.
- MySQL normally uses port `3306`.

## 3. Create the database

1. Open phpMyAdmin from the XAMPP Control Panel.
2. Select **Databases**.
3. Create a database named `project_db` with the `utf8mb4_general_ci` collation.

## 4. Import the complete SQL file

1. In phpMyAdmin, select the new `project_db` database.
2. Select **Import**.
3. Choose `project_db_FULL_IMPORT.sql` from the project folder.
4. Leave the format set to **SQL**, then select **Import**.

The import is complete when phpMyAdmin shows these twelve tables:

```text
contact_messages
exercises
gym_staff
gyms
members
membership_plans
muscles
programs
program_days
program_exercises
users
user_results
```

The SQL file contains the complete current table definitions, keys, relationships, and data needed by the application.

For an existing Phase 1 Step 2 installation, import only
`FitlifeDB/original_phase_1_step_3_membership_plans.sql` instead of importing the
full-install file. This adds membership plans without replacing existing data.

## 5. Create the local database configuration

Copy `config/database.example.php` to `config/database.local.php`. Edit only the copied local file so its values match this computer:

```php
<?php

return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'project_db',
    'username' => 'root',
    'password' => '',
];
```

Use the MySQL port, username, and password configured in XAMPP. A default local XAMPP installation usually uses username `root` with an empty password. `config/database.local.php` is excluded by `.gitignore`; do not commit it.

## 6. Open FitLife

Use the URL matching Apache's port and the actual project folder name:

- Apache on port `80`: `http://localhost/fitlife/`
- Apache on port `8080`: `http://localhost:8080/fitlife/`

For example, a folder named `MyFitLifeCopy` opens at `http://localhost/MyFitLifeCopy/` on port `80`, or `http://localhost:8080/MyFitLifeCopy/` on port `8080`.

If the project files were placed directly in `htdocs` instead of a subfolder, open `http://localhost/` or `http://localhost:8080/`.
