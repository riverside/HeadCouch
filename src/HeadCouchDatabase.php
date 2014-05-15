<?php
require_once "HeadCouchException.php";
require_once "HeadCouchHttp.php";
/**
 * CouchDB database wrapper
 *
 * @author Dimitar Ivnaov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.0
 * @package HeadCouch
 */
class HeadCouchDatabase
{
/**
 * Database name
 *
 * @var string
 */
    private $db;
/**
 * Instance of the HTTP Client
 *
 * @var mixed
 */
    private $http;
/**
 * Constructor
 *
 * @param string $db
 * @param string $host
 * @param number $port
 * @throws HeadCouchException
 */
    public function __construct($db, $host=NULL, $port=NULL)
    {
        if (empty($db))
        {
            throw new HeadCouchException("\$db could not be empty.");
        }
        $this->db = $db;
        $this->http = HeadCouchHttp::newInstance($host, $port);
    }
/**
 * Creates a new database.
 *
 * @return mixed
 */
    public function create()
    {
        $this->http->setMethod("PUT")->request($this->db);

        return $this->http->getResponse();
    }
/**
 * Deletes the specified database, and all the documents
 * and attachments contained within it.
 *
 * @return mixed
 */
    public function delete()
    {
        $this->http->setMethod('DELETE')->request($this->db);

        return $this->http->getResponse();
    }
/**
 * Gets information about the specified database.
 *
 * @return mixed
 */
    public function get()
    {
        $this->http->setMethod('GET')->request($this->db);

        return $this->http->getResponse();
    }
/**
 * Returns the HTTP Headers containing a minimal amount
 * of information about the specified database. Since the
 * response body is empty this method is a lightweight way
 * to check if the database exists already or not.
 *
 * @return mixed
 */
    public function head()
    {
    	$this->http->setMethod('HEAD')->request($this->db);
    
    	return json_encode($this->http->getResponseHeaders());
    }
/**
 * Creates a new document in the specified database,
 * using the supplied JSON document structure.
 *
 * @param array $data
 * @param boolean $batch
 * @return mixed
 */
    public function post($data, $batch=FALSE)
    {
    	$qs = NULL;
    	if ($batch)
    	{
    		$qs = "?batch=ok";
    	}
    	$this->http->setMethod('POST')->setData($data)->request($this->db . $qs);
    
    	return $this->http->getResponse();
    }
/**
 * Returns new instance of the HeadCouchDatabase
 *
 * @param string $db
 * @param string $host
 * @param number $port
 * @return HeadCouchDatabase
 */
    static public function newInstance($db, $host=NULL, $port=NULL)
    {
        return new self($db, $host, $port);
    }
/**
 * A database purge permanently removes the references
 * to deleted documents from the database.
 *
 * @param array $data
 * @return mixed
 */
    public function purge($data)
    {
    	$this->http->setMethod("POST")->setData($data)->request($this->db . "/_purge");
    
    	return $this->http->getResponse();
    }
/**
 * With given a list of document revisions, returns the
 * document revisions that do not exist in the database.
 *
 * @param array $data
 * @return mixed
 */
    public function missingRevs($data)
    {
    	$this->http->setMethod("POST")->setData($data)->request($this->db . "/_missing_revs");
    
    	return $this->http->getResponse();
    }
/**
 * Given a set of document/revision IDs, returns the subset
 * of those that do not correspond to revisions stored in
 * the database.
 *
 * @param array $data
 * @return mixed
 */
    public function revsDiff($data)
    {
    	$this->http->setMethod("POST")->setData($data)->request($this->db . "/_revs_diff");
    
    	return $this->http->getResponse();
    }
/**
 * Gets the current revs_limit (revision limit) setting.
 * -- OR --
 * Sets the maximum number of document revisions that will
 * be tracked by CouchDB, even after compaction has occurred.
 * You can set the revision limit on a database with a scalar
 * integer of the limit that you want to set as the request body.
 *
 * @param array $limit
 * @return mixed
 */
    public function revsLimit($limit=NULL)
    {
    	if (is_null($limit))
    	{
    		$this->http->setMethod("GET")->request($this->db . "/_revs_limit");
    	} else {
    		$this->http->setMethod("PUT")->setData($limit)->request($this->db . "/_revs_limit");
    	}
    
    	return $this->http->getResponse();
    }
/**
 * Returns the current security object from the specified database.
 * -- OR --
 * Sets the security object for the given database.
 *
 * @param array $admins
 * @param array $members
 * @return mixed
 */
    public function security($admins=array(), $members=array())
    {
    	if (empty($admins) && empty($members))
    	{
    		$this->http->setMethod("GET")->request($this->db . "/_security");
    	} else {
    		$this->http->setMethod("PUT")->setData(compact('admins', 'members'))->request($this->db . "/_security");
    	}
    
    	return $this->http->getResponse();
    }
/**
 * Compacts the view indexes associated with the specified
 * design document. If may be that compacting a large view
 * can return more storage than compacting the atual db.
 * Thus, you can use this in place of the full database
 * compaction if you know a specific set of view indexes
 * have been affected by a recent database change.
 *
 * @param string $ddoc
 * @return mixed
 */
    public function compact($ddoc=NULL)
    {
    	$qs = NULL;
    	if (!is_null($ddoc))
    	{
    		$qs = "/" . $ddoc;
    	}
    	$this->http->setMethod("POST")->request($this->db . "/_compact" . $qs);
    
    	return $this->http->getResponse();
    }
/**
 * Commits any recent changes to the specified database to
 * disk. You should call this if you want to ensure that
 * recent changes have been flushed. This function is
 * likely not required, assuming you have the recommended
 * configuration setting of delayed_commits=false, which
 * requires CouchDB to ensure changes are written to disk
 * before a 200 or similar result is returned.
 *
 * @return mixed
 */
    public function ensureFullCommit()
    {
    	$this->http->setMethod("POST")->request($this->db . "/_ensure_full_commit");
    
    	return $this->http->getResponse();
    }
/**
 * Removes view index files that are no longer required by
 * CouchDB as a result of changed views within design
 * documents. As the view filename is based on a hash of
 * the view functions, over time old views will remain,
 * consuming storage. This call cleans up the cached
 * view output on disk for a given view.
 *
 * @return mixed
 */
    public function viewCleanup()
    {
    	$this->http->setMethod("POST")->request($this->db . "/_view_cleanup");
    
    	return $this->http->getResponse();
    }
/**
 *
 * @param array $keys
 * @param array $params
 * @return mixed
 */
    public function getAll($keys=array(), $params=array())
    {
    	if (empty($keys))
    	{
    		$tmp = array();
    		foreach ($params as $key => $value)
    		{
    			if (in_array($key, array(
    					'conflicts','descending','endkey','end_key','endkey_docid',
    					'end_key_doc_id','include_docs','inclusive_end','key','limit',
    					'skip','stale','startkey','start_key','startkey_docid',
    					'start_key_doc_id','update_seq')))
    			{
    				$tmp[$key] = $value;
    			}
    		}
   			$qs = empty($tmp) ? NULL : "?" . http_build_query($tmp);
    		
    		$this->http->setMethod("GET")->request($this->db . "/_all_docs" . $qs);
    	} else {
    		$this->http->setMethod("POST")->setData(compact('keys'))->request($this->db . "/_all_docs");
    	}
    
    	return $this->http->getResponse();
    }
}
?>