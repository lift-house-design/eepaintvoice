MIGRATION INSTRUCTIONS
======================
There are hostnames and local paths stored in the database and files that must be updated when migrating the installation to a new environment.

Hostnames
---------
    * Database Tables
        * `exp_channels`
        * `exp_upload_prefs`
    * Files
        * `system\expressionengine\config\config.php`

Local Paths
-----------
    * Database Tables
        * `exp_files`
        * `exp_upload_prefs`
    * Files
        * `system\expressionengine\config\database.php`

Database Connection
-------------------
    * `system\expressionengine\config\database.php`