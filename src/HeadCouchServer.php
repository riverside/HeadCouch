<?php
require_once "HeadCouchException.php";
/**
 * CouchDB server wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.1
 * @package HeadCouch
 */
class HeadCouchServer
{
/**
 * Instance of the transport
 *
 * @var mixed
 */
    private $transport;
/**
 * Constructor
 *
 * @param object $transport
 */
    public function __construct($transport)
    {
        $this->transport = $transport;
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
        $this->transport->setMethod("GET")->request("_active_tasks");

        return $this->transport->getResponse();
    }
/**
 * Returns a list of all the databases in the CouchDB instance.
 *
 * @return mixed
 */
    public function allDbs()
    {
        $this->transport->setMethod("GET")->request("_all_dbs");

        return $this->transport->getResponse();
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
        $this->transport->setMethod("GET")->request("_db_updates?".$qs);

        return $this->transport->getResponse();
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
        $this->transport->setMethod("GET")->request("_log?bytes=".intval($bytes).'&offset='.intval($offset));

        return $this->transport->getResponse();
    }
/**
 * Returns new instance of the HeadCouchServer
 *
 * @param object $transport
 * @return HeadCouchServer
 */
    static public function newInstance($transport)
    {
        return new self($transport);
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
        $this->transport->setMethod("GET")->request(NULL);

        return $this->transport->getResponse();
    }
/**
 * Restarts the CouchDB instance. You must be authenticated
 * as a user with administration privileges for this to work.
 *
 * @return mixed
 */
    public function restart()
    {
        $this->transport->setMethod("POST")->request("_restart");

        return $this->transport->getResponse();
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
        $this->transport->setMethod("GET")->request("_stats");

        return $this->transport->getResponse();
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
        $this->transport->setMethod("GET")->request("_uuids?count=" . (int) $count);

        return $this->transport->getResponse();
    }
}
?>