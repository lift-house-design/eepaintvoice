MIGRATION INSTRUCTIONS
======================
Everything has been templated in `system/expressionengine/config/config.php`. All you need to do is update the `$application_url` and `$application_path` variables to the correct value. Also update the `system/expressionengine/config/database.php` file to use the correct values. Then run `development/scripts/set-permissions.sh` to complete.


OLD
===
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

Application Updates
-------------------

You'll need to update paths/URLs in the following locations:
    * `Admin -> General Configuration`
    * `Admin -> Security and Privacy -> CAPTCHA Preferences`
    * `Admin -> System Administration -> Emoticon Preferences`
    * `Members -> Preferences`

And maybe these:
    * `Content -> Files -> File Upload Preferences`
    * `Design -> Templates -> Global Preferences`
    * `Admin -> Channel Administration > Channels`

