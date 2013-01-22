===
YAM
===

Yet Another Migrator (YAM) is a simple and flexible Application created for migrations for php in general, it includes a very trivial version management.

Install
=======
``composer -o install``

Copy and update following:
- app/bootstrap.php.dist to app/bootstrap.php (it actually load Doctrine ODM and ORM, feel free to change it)
- app/etc/db.php.dist to app/etc/db.php (configured by default for the bootstrap.php.dist)

Testing
=======
you can test rapidly test if core feature works using tests/test.sh
Those are not unit tests but will just run simple migrations cases to ensure core functionnalities works.
