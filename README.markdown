# HeadCouch

#### CouchDB PHP client

Version 0.1.1

http://zinoui.com/

#### Server
```php
<?php
require_once 'HeadCouch.php';

$transport = HeadCouchSocket::newInstance('127.0.0.1', 5984)
	->setUsername('my_username')
	->setPassword('my_password')
;

$server = HeadCouchServer::newInstance($transport);

// Accessing the root of a CouchDB instance
$result = $server->ping();

// Requests a Universally Unique Identifier from the CouchDB instance
$result = $server->uuid();

// Returns a list of all the databases
$result = $server->allDbs();

// List of running tasks
$result = $server->activeTasks();

// Returns a list of all database events in the CouchDB instance
$result = $server->dbUpdates();

// Gets the CouchDB log
$result = $server->log();

// Restarts the CouchDB instance
$result = $server->restart();

// Returns the statistics for the running server
$result = $server->stats();
?>
```
#### Database
```php
<?php
require_once 'HeadCouch.php';

$transport = HeadCouchSocket::newInstance('127.0.0.1', 5984)
	->setUsername('my_username')
	->setPassword('my_password')
;

$database = HeadCouchDatabase::newInstance($transport, 'db_name');

// Create database
$result = $database->create();

// Delete database
$result = $database->delete();

// Gets information about the specified database
$result = $database->get();

// Returns the HTTP Headers about the specified database
$result = $database->head();

// Creates a new document in the specified database
$result = $database->post(array(
    'key1' => 'val1', 
    'key2' => 'val2'
));
?>
```
#### Document
```php
<?php
require_once 'HeadCouch.php';

$transport = HeadCouchSocket::newInstance('127.0.0.1', 5984)
	->setUsername('my_username')
	->setPassword('my_password')
;

$document = HeadCouchDocument::newInstance($transport, 'db_name', 'doc_name');

// Creates a new document
$result = $document->create(array(
    'key1' => 'val1', 
    'key2' => 'val2'
));

// Deletes the specified document from the database
$result = $document->delete();

// Returns document
$result = $document->get();

// Returns document's revision token
$result = $document->getRevision();

// Returns the HTTP Headers about the specified document
$result = $document->head();
?>
```
