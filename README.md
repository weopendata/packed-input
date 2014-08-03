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

# Hook the service provider into the main application

In order to notify the main application of the existence of this package, add the service provider to the configuration of the main application. This is done by simply adding Tdt\Input\InputServiceProvider to the providers array in app/config/app.php.

Publish assets `php artisan asset:publish --bench=packed-input`

# Configure jobs

An ETL process can be added using the REST interface

```
 {
    extract: {
        type: "Csv",
        uri: "path/to/the/data.csv",
        delimiter: ",",
        has\_header\_row: "1"
    },
    map: {
        type: "Packedartist | Packedobject | Packedinstitution",
        data_provider: "Groeninge",
        creator_column: "Name of the column where the creator is given, by default creator is assumed"
    },
    load: {
        type: "Packedartist | Packedobject | Packedinstitution",
        data_provider: "Groeninge"
    }
}
```

The json above is a template of how a job might look like, in order to add a job, simply replace the bits that are specific for your ETL process and use it as the body of a PUT request:

    $ curl -v -d 'your_json_here' -XPUT -H "accept: application/json" http://user:pw@host/api/input/your/job

# Run jobs

Running jobs is done through the command line, go to the root of the application and simply execute the following command:

    $ php artisan input:execute your/job

The output will be written to the console, so in case you want to check these logs later on, make sure to pipe them to a file.
