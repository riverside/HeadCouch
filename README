HeadCouch
=========

Version 0.1.0

    <?php
    require_once 'HeadCouch.php';

    // Accessing the root of a CouchDB instance
    $result = HeadCouchServer::newInstance()->ping();

    // Requests a Universally Unique Identifier from the CouchDB instance
    $result = HeadCouchServer::newInstance()->uuid();

    // Returns a list of all the databases
    $result = HeadCouchServer::newInstance()->allDbs();

    // List of running tasks
    $result = HeadCouchServer::newInstance()->activeTasks();

    // Returns a list of all database events in the CouchDB instance
    $result = HeadCouchServer::newInstance()->dbUpdates();

    // Gets the CouchDB log
    $result = HeadCouchServer::newInstance()->log();

    // Restarts the CouchDB instance
    $result = HeadCouchServer::newInstance()->restart();

    // Returns the statistics for the running server
    $result = HeadCouchServer::newInstance()->stats();
    ?>
