# HeadCouch

#### CouchDB PHP client

Version 0.1.0

http://zinoui.com/

#### Server
```php
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
```
#### Database
```php
<?php
require_once 'HeadCouch.php';

// Create database
$result = HeadCouchDatabase::newInstance('db_name')->create();

// Delete database
$result = HeadCouchDatabase::newInstance('db_name')->delete();

// Gets information about the specified database
$result = HeadCouchDatabase::newInstance('db_name')->get();

// Returns the HTTP Headers about the specified database
$result = HeadCouchDatabase::newInstance('db_name')->head();

// Creates a new document in the specified database
$result = HeadCouchDatabase::newInstance('db_name')->post(array(
    'key1' => 'val1', 
    'key2' => 'val2'
));
?>
```
#### Document
```php
<?php
require_once 'HeadCouch.php';

// Creates a new document
$result = HeadCouchDocument::newInstance('db_name', 'doc_name')->create(array(
    'key1' => 'val1', 
    'key2' => 'val2'
));

// Deletes the specified document from the database
$result = HeadCouchDocument::newInstance('db_name', 'doc_name')->delete();

// Returns document
$result = HeadCouchDocument::newInstance('db_name', 'doc_name')->get();

// Returns document's revision token
$result = HeadCouchDocument::newInstance('db_name', 'doc_name')->getRevision();

// Returns the HTTP Headers about the specified document
$result = HeadCouchDocument::newInstance('db_name', 'doc_name')->head();
?>
```
