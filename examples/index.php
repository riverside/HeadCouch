<?php
if (isset($_GET['do']))
{
	include '../src/HeadCouch.php';
	
	$data = array('key1'=>'val1', 'key2' => 'val2', 'key3' => 'val3');
	
	$transport = HeadCouchSocket::newInstance($_GET['host'], $_GET['port'])
		->setUsername($_GET['user'])
		->setPassword($_GET['pswd'])
	;
	
	$server = HeadCouchServer::newInstance($transport);
	
	if (in_array($_GET['do'], array('createDb', 'deleteDb', 'getDb', 'headDb', 'postDb')))
	{
		$database = HeadCouchDatabase::newInstance($transport, @$_GET['dbName']);
	}
	
	if (in_array($_GET['do'], array('createDb', 'deleteDb', 'getDb', 'headDb', 'postDb', 'createDoc', 'deleteDoc', 'getDoc', 'getDocRev', 'headDoc')))
	{
		$document = HeadCouchDocument::newInstance($transport, @$_GET['dbName'], @$_GET['docName']);
	}
	
	switch ($_GET['do'])
	{
		# Server
		case 'ping':
			$r = $server->ping();
			break;
		case 'uuid':
			$r = $server->uuid();
			break;
		case 'allDbs':
			$r = $server->allDbs();
			break;
		case 'activeTasks':
			$r = $server->activeTasks();
			break;
		case 'dbUpdates':
			$r = $server->dbUpdates();
			break;
		case 'log':
			$r = $server->log();
			break;
		case 'restart':
			$r = $server->restart();
			break;
		case 'stats':
			$r = $server->stats();
			break;
		# Database
		case 'createDb':
			$r = $database->create();
			break;
		case 'deleteDb':
			$r = $database->delete();
			break;
		case 'getDb':
			$r = $database->get();
			break;
		case 'headDb':
			$r = $database->head();
			break;
		case 'postDb':
			$r = $database->post($data);
			break;
		# Document
		case 'createDoc':
			$r = $document->create($data);
			break;
		case 'deleteDoc':
			$r = $document->delete($_GET['docRev']);
			break;
		case 'getDoc':
			$r = $document->get();
			break;
		case 'getDocRev':
			$r = $document->getRevision();
			break;
		case 'headDoc':
			$r = $document->head();
			break;
	}
	
	?><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Return back</a><?php
	
	echo '<pre>';
	print_r(json_decode($r));
	echo '</pre>';
	exit;
}
?>
<!doctype html>
<html lang="en">
	<head>
		<title>HeadCouch | CouchDB PHP Client</title>
		<meta charset="utf-8">
		<style type="text/css">
		body{
			font: normal 13px Arial, sans-serif;
		}
		.info{
			color: #777;
			display: block;
			font-size: 12px;
		}
		</style>
	</head>
	<body>
	
		<form action="" method="get">
			<fieldset>
				<legend>General</legend>
				<label>Host: <input type="text" name="host" value="127.0.0.1" /></label>
				<label>Port: <input type="text" name="port" value="5984" /></label>
				<label>Username: <input type="text" name="user" value="root" /></label>
				<label>Password: <input type="text" name="pswd" value="1" /></label>
				<label>Database: <input type="text" name="dbName" /></label>
			</fieldset>
			<fieldset style="float: left; width: 30%">
				<legend>Server</legend>
				<p>
					<label><input type="radio" name="do" value="ping" /> Ping <span class="info">Accessing the root of a CouchDB instance returns meta information about the instance.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="uuid" /> Uuid <span class="info">Requests one or more Universally Unique Identifiers (UUIDs) from the CouchDB instance. </span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="stats" /> Stats <span class="info">The _stats resource returns a JSON object containing the statistics for the running server.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="restart" /> Restart <span class="info">Restarts the CouchDB instance.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="log" /> Log <span class="info">Gets the CouchDB log, equivalent to accessing the local log file of the corresponding CouchDB instance.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="dbUpdates" /> dbUpdates <span class="info">Returns a list of all database events in the CouchDB instance.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="activeTasks" /> activeTasks <span class="info">List of running tasks, including the task type, name, status and process ID.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="allDbs" /> allDbs <span class="info">Returns a list of all the databases in the CouchDB instance.</span></label>
				</p>
				<p>
					<input type="submit" value="Submit" />
				</p>
			</fieldset>
			<fieldset style="float: left; width: 30%">
				<legend>Database</legend>
				<p>
					<label><input type="radio" name="do" value="createDb" /> Create database <span class="info">Creates a new database. </span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="deleteDb" /> Delete database <span class="info">Deletes the specified database, and all the documents and attachments contained within it.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="getDb" /> Get database <span class="info">Gets information about the specified database.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="headDb" /> Head database <span class="info">Returns the HTTP Headers containing a minimal amount of information about the specified database.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="postDb" /> Post database <span class="info">Creates a new document in the specified database, using the supplied JSON document structure.</span></label>
				</p>
				<p>
					<input type="submit" value="Submit" />
				</p>
			</fieldset>
			<fieldset style="float: left; width: 30%">
				<legend>Document</legend>
				<p><label>Document ID: <input type="text" name="docName" /></label></p>
				<p><label>Revision: <input type="text" name="docRev" /></label></p>
				<p>
					<label><input type="radio" name="do" value="createDoc" /> Create document <span class="info">The PUT method creates a new named document, or creates a new revision of the existing document. </span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="deleteDoc" /> Delete document <span class="info">Deletes the specified document from the database. </span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="getDoc" /> Get document <span class="info">Returns document by the specified docid from the specified db.</span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="getDocRev" /> Get document revision <span class="info"></span></label>
				</p>
				<p>
					<label><input type="radio" name="do" value="headDoc" /> Head document <span class="info">Returns the HTTP Headers containing a minimal amount of information about the specified document.</span></label>
				</p>
				<p>
					<input type="submit" value="Submit" />
				</p>
			</fieldset>
		</form>
	
	</body>
</html>