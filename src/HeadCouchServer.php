<?php
require_once "HeadCouchException.php";
require_once "HeadCouchHttp.php";
/**
 * CouchDB server wrapper
 *
 * @author Dimitar Ivnaov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.0
 * @package HeadCouch
 */
class HeadCouchServer
{
/**
 * Instance of the HTTP client
 *
 * @var mixed
 */
    private $http;
/**
 * Constructor
 *
 * @param string $host
 * @param number $port
 */
    public function __construct($host=NULL, $port=NULL)
    {
        $this->http = HeadCouchHttp::newInstance($host, $port);
    }
/**
 * List of running tasks, including the task type, name,
 * status and process ID. The result is a JSON array of the
 * currently running tasks, with each task being described
 * with a single object. Depending on operation type set
 * of response object fields might be different.
 *
 * @return mixed
 */
    public function activeTasks()
    {
        $this->http->setMethod("GET")->request("_active_tasks");

        return $this->http->getResponse();
    }
/**
 * Returns a list of all the databases in the CouchDB instance.
 *
 * @return mixed
 */
    public function allDbs()
    {
        $this->http->setMethod("GET")->request("_all_dbs");

        return $this->http->getResponse();
    }
/**
 * Returns a list of all database events in the CouchDB instance.
 *
 * @param string $feed
 * @param number $timeout
 * @param string $heartbeat
 * @return mixed
 */
    public function dbUpdates($feed='longpoll', $timeout=60, $heartbeat=TRUE)
    {
        $params = array();
        if (in_array($feed, array('longpoll', 'continuous', 'eventsource')))
        {
            $params['feed'] = $feed;
        }
        $params['timeout'] = (int) $timeout;
        $params['heartbeat'] = $heartbeat;
        $qs = http_build_query($params);
        $this->http->setMethod("GET")->request("_db_updates?".$qs);

        return $this->http->getResponse();
    }
/**
 * Gets the CouchDB log, equivalent to accessing the
 * local log file of the corresponding CouchDB instance.
 *
 * @param number $bytes
 * @param number $offset
 * @return mixed
 */
    public function log($bytes=1000, $offset=0)
    {
        $this->http->setMethod("GET")->request("_log?bytes=".intval($bytes).'&offset='.intval($offset));

        return $this->http->getResponse();
    }
/**
 * Returns new instance of the HeadCouchServer
 *
 * @param string $host
 * @param number $port
 * @return HeadCouchServer
 */
    static public function newInstance($host=NULL, $port=NULL)
    {
        return new self($host, $port);
    }
/**
 * Accessing the root of a CouchDB instance returns meta
 * information about the instance. The response is a JSON
 * structure containing information about the server,
 * including a welcome message and the version of the server.
 *
 * @return mixed
 */
    public function ping()
    {
        $this->http->setMethod("GET")->request(NULL);

        return $this->http->getResponse();
    }
/**
 * Restarts the CouchDB instance. You must be authenticated
 * as a user with administration privileges for this to work.
 *
 * @return mixed
 */
    public function restart()
    {
        $this->http->setMethod("POST")->request("_restart");

        return $this->http->getResponse();
    }
/**
 * The _stats resource returns a JSON object containing
 * the statistics for the running server. The object is
 * structured with top-level sections collating the statistics
 * for a range of entries, with each individual statistic
 * being easily identified, and the content of each statistic
 * is self-describing.
 *
 * @return mixed
 */
    public function stats()
    {
        $this->http->setMethod("GET")->request("_stats");

        return $this->http->getResponse();
    }
/**
 * Requests one or more Universally Unique Identifiers
 * (UUIDs) from the CouchDB instance. The response is
 * a JSON object providing a list of UUIDs.
 *
 * @param number $count
 * @return mixed
 */
    public function uuid($count=1)
    {
        $this->http->setMethod("GET")->request("_uuids?count=" . (int) $count);

        return $this->http->getResponse();
    }
}
?>