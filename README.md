# Input package

[![Latest Stable Version](https://poser.pugx.org/tdt/input/version.png)](https://packagist.org/packages/tdt/input)
[![Build Status](https://travis-ci.org/tdt/input.png?branch=development)](https://travis-ci.org/tdt/input)

This is the Laravel package called "input" and serves as an extract-map-load configuration (EML) as part of the datatank core application (tdt/core). The current instances of the eml stack are focussed on semantifying data. This means that raw data can be transformed into semantic data by providing a mapping file.

# NoSQL (MongoDB)

In this fork MongoDB is supported as a loader implementation. This means our ETL sequence now loads data into a NoSQL database, currently only a MongoDB database.

# Get PHP ready to interface with Mongo

* Go to the official [MongoDB PHP driver repository](https://github.com/mongodb/mongo-php-driver) in order to make the MongoDB PHP driver
* Copy the driver (.so or .dll) to your extensions folder of your PHP installation and you're done!
* If it doesn't work, restart Apache, or make sure you copied the driver to the right location

# Configure a MongoDB connection in laravel

Go to your app/config/database.php file and add a mongodb connection:

```
'mongodb' => array(
    'driver'   => 'mongodb',
    'host'     => 'localhost',
    'port'     => 27017,
    'username' => '',
    'password' => '',
    'database' => 'packed'
),

```
