===
YAM
===

Yet Another Migrator (YAM) is a simple and flexible Application created for migrations for php in general, it includes a very trivial version management.

Install
=======
``composer install``

Copy and update following:
- app/bootstrap.php.dist to app/bootstrap.php (it actually load Doctrine ODM and ORM
- app/etc/db.php.dist to app/etc/db.php
- app/etc/application.php.dist to app/etc/application.php

Testing
=======
you can test rapidly test if core feature works using tests/test.sh
Those are not unit tests but will just run simple migrations cases to ensure core functionnalities works.
