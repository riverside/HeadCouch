<?php

namespace HeadCouch;
/**
 * CouchDB server wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch
 */
class Server
{
    /**
     * Instance of the transport
     *
     * @var mixed
     */
    protected $transport;

    /**
     * Constructor
     *
     * @param Transport $transport
     */
    public function __construct(Transport $transport)
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
     * @return Response
     */
    public function activeTasks(): Response
    {
        $this->transport->setMethod("GET")->request("_active_tasks");

        return $this->transport->getResponse();
    }

    /**
     * Returns a list of all the databases in the CouchDB instance.
     *
     * @return Response
     */
    public function allDbs(): Response
    {
        $this->transport->setMethod("GET")->request("_all_dbs");

        return $this->transport->getResponse();
    }

    /**
     * Returns the status of the node or cluster, per the cluster setup wizard.
     *
     * @return Response
     */
    public function clusterSetup(): Response
    {
        $this->transport->setMethod("GET")->request("_cluster_setup");

        return $this->transport->getResponse();
    }

    /**
     * Returns a list of all database events in the CouchDB instance.
     *
     * @param string $feed
     * @param int $timeout
     * @param int $heartbeat
     * @return Response
     */
    public function dbUpdates(string $feed = 'normal', int $timeout = 60, $heartbeat = 60000): Response
    {
        $params = array();
        if (in_array($feed, array('normal', 'longpoll', 'continuous', 'eventsource'))) {
            $params['feed'] = $feed;
        }
        $params['timeout'] = (int)$timeout;
        $params['heartbeat'] = $heartbeat;
        $qs = http_build_query($params, '', '&');
        $this->transport->setMethod("GET")->request("_db_updates?" . $qs);

        return $this->transport->getResponse();
    }

    /**
     * Returns information of a list of the specified databases in the CouchDB instance.
     *
     * @param array $keys Array of database names to be requested
     * @return Response
     */
    public function dbsInfo($keys = array()): Response
    {
        $this->transport->setMethod("POST")->setData(array(
            'keys' => $keys
        ))->request("_dbs_info");

        return $this->transport->getResponse();
    }

    /**
     * Gets the CouchDB log, equivalent to accessing the
     * local log file of the corresponding CouchDB instance.
     *
     * @param int|NULL $bytes
     * @param int|NULL $offset
     * @return Response
     */
    public function log(int $bytes = NULL, int $offset = NULL): Response
    {
        $bytes = $bytes ? $bytes : 1000;
        $offset = $offset ? $offset : 0;
        $this->transport->setMethod("GET")->request("_log?bytes=" . intval($bytes) . '&offset=' . intval($offset));

        return $this->transport->getResponse();
    }

    /**
     * Displays the nodes that are part of the cluster as cluster_nodes.
     *
     * @return Response
     */
    public function membership(): Response
    {
        $this->transport->setMethod("GET")->request("_membership");

        return $this->transport->getResponse();
    }

    /**
     * Returns new instance of the Server
     *
     * @param Transport $transport
     * @return Server
     */
    static public function newInstance(Transport $transport): self
    {
        return new self($transport);
    }

    /**
     * Accessing the root of a CouchDB instance returns meta
     * information about the instance. The response is a JSON
     * structure containing information about the server,
     * including a welcome message and the version of the server.
     *
     * @return Response
     */
    public function ping(): Response
    {
        $this->transport->setMethod("GET")->request("");

        return $this->transport->getResponse();
    }

    /**
     * Restarts the CouchDB instance. You must be authenticated
     * as a user with administration privileges for this to work.
     *
     * @return Response
     */
    public function restart(): Response
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
     * @return Response
     */
    public function stats(): Response
    {
        $this->transport->setMethod("GET")->request("_stats");

        return $this->transport->getResponse();
    }

    /**
     * Confirms that the server is up, running, and ready to respond to requests.
     *
     * @return Response
     */
    public function up(): Response
    {
        $this->transport->setMethod("GET")->request("_up");

        return $this->transport->getResponse();
    }

    /**
     * Requests one or more Universally Unique Identifiers
     * (UUIDs) from the CouchDB instance. The response is
     * a JSON object providing a list of UUIDs.
     *
     * @param int $count
     * @return Response
     */
    public function uuid($count = 1): Response
    {
        $this->transport->setMethod("GET")->request("_uuids?count=" . (int)$count);

        return $this->transport->getResponse();
    }
}