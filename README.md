# HeadCouch
[![Build Status](https://api.travis-ci.org/riverside/HeadCouch.svg)](https://travis-ci.org/riverside/HeadCouch) [![Latest Stable Version](https://poser.pugx.org/riverside/head-couch/v/stable)](https://packagist.org/packages/riverside/head-couch) [![License](https://poser.pugx.org/riverside/head-couch/license)](https://packagist.org/packages/riverside/head-couch)

#### CouchDB PHP client

https://zinoui.com/
#### Loading
```php
require __DIR__ . '/vendor/autoload.php';
```
#### Transport
- cURL
```php
$transport = \HeadCouch\Curl::newInstance('127.0.0.1', 5984)
	->setUsername('my_username')
	->setPassword('my_password');
```
- Socket
```php
$transport = \HeadCouch\Socket::newInstance('127.0.0.1', 5984)
	->setUsername('my_username')
	->setPassword('my_password');
```
- File
```php
$transport = \HeadCouch\File::newInstance('127.0.0.1', 5984)
	->setUsername('my_username')
	->setPassword('my_password');
```
#### Server
```php
$server = \HeadCouch\Server::newInstance($transport);

// Accessing the root of a CouchDB instance
$response = $server->ping();

// Requests a Universally Unique Identifier from the CouchDB instance
$response = $server->uuid();

// Returns a list of all the databases
$response = $server->allDbs();

// List of running tasks
$response = $server->activeTasks();

// Returns a list of all database events in the CouchDB instance
$response = $server->dbUpdates();

// Gets the CouchDB log
$response = $server->log();

// Restarts the CouchDB instance
$response = $server->restart();

// Returns the statistics for the running server
$response = $server->stats();
```
#### Database
```php
try {
    $database = \HeadCouch\Database::newInstance($transport, 'db_name');
} catch (\HeadCouch\Exception $e) {
    echo $e->getMessage();
}

// Create database
$response = $database->create();

// Delete database
$response = $database->delete();

// Gets information about the specified database
$response = $database->get();

// Returns the HTTP Headers about the specified database
$response = $database->head();

// Creates a new document in the specified database
$response = $database->post(array(
    'key1' => 'val1', 
    'key2' => 'val2'
));
```
#### Document
```php
try {
    $document = \HeadCouch\Document::newInstance($transport, 'db_name', 'doc_name');
} catch (\HeadCouch\Exception $e) {
    echo $e->getMessage();
}

// Creates a new document
$response = $document->create(array(
    'key1' => 'val1', 
    'key2' => 'val2'
));

// Deletes the specified document from the database
$response = $document->delete();

// Returns document
$response = $document->get();

// Returns document's revision token
$response = $document->getRevision();

// Returns the HTTP Headers about the specified document
$response = $document->head();
```
#### Response
```php
$result = $response->toArray();
// print_r($result);

$result = $response->toObject();
// get_object_vars($result);

$result = $response->toString();
// echo $result;
```