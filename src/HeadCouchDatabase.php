<?php
require_once "HeadCouchException.php";
/**
 * CouchDB database wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.1
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
 * Instance of the transport
 *
 * @var mixed
 */
    private $transport;
/**
 * Constructor
 *
 * @param object $transport
 * @param string $db
 * @throws HeadCouchException
 */
    public function __construct($transport, $db)
    {
        if (empty($db))
        {
            throw new HeadCouchException("\$db could not be empty.");
        }
        $this->db = $db;
        $this->transport = $transport;
    }
/**
 * Creates a new database.
 *
 * @return mixed
 */
    public function create()
    {
        $this->transport->setMethod("PUT")->request($this->db);

        return $this->transport->getResponse();
    }
/**
 * Get/set database name
 *
 * @param string $dbName
 * @return string|HeadCouchDatabase
 */
    public function db($dbName=NULL)
    {
    	if (is_null($dbName))
    	{
    		return $this->db;
    	}
    	
    	$this->db = $dbName;
    	
    	return $this;
    }
/**
 * Deletes the specified database, and all the documents
 * and attachments contained within it.
 *
 * @return mixed
 */
    public function delete()
    {
        $this->transport->setMethod('DELETE')->request($this->db);

        return $this->transport->getResponse();
    }
/**
 * Gets information about the specified database.
 *
 * @return mixed
 */
    public function get()
    {
        $this->transport->setMethod('GET')->request($this->db);

        return $this->transport->getResponse();
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
    	$this->transport->setMethod('HEAD')->request($this->db);
    
    	return json_encode($this->transport->getResponseHeaders());
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
    	$this->transport->setMethod('POST')->setData($data)->request($this->db . $qs);
    
    	return $this->transport->getResponse();
    }
/**
 * Returns new instance of the HeadCouchDatabase
 *
 * @param object $transport
 * @param string $db
 * @return HeadCouchDatabase
 */
    static public function newInstance($transport, $db)
    {
        return new self($transport, $db);
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
    	$this->transport->setMethod("POST")->setData($data)->request($this->db . "/_purge");
    
    	return $this->transport->getResponse();
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
    	$this->transport->setMethod("POST")->setData($data)->request($this->db . "/_missing_revs");
    
    	return $this->transport->getResponse();
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
    	$this->transport->setMethod("POST")->setData($data)->request($this->db . "/_revs_diff");
    
    	return $this->transport->getResponse();
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
    		$this->transport->setMethod("GET")->request($this->db . "/_revs_limit");
    	} else {
    		$this->transport->setMethod("PUT")->setData($limit)->request($this->db . "/_revs_limit");
    	}
    
    	return $this->transport->getResponse();
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
    		$this->transport->setMethod("GET")->request($this->db . "/_security");
    	} else {
    		$this->transport->setMethod("PUT")->setData(compact('admins', 'members'))->request($this->db . "/_security");
    	}
    
    	return $this->transport->getResponse();
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
    	$this->transport->setMethod("POST")->request($this->db . "/_compact" . $qs);
    
    	return $this->transport->getResponse();
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
    	$this->transport->setMethod("POST")->request($this->db . "/_ensure_full_commit");
    
    	return $this->transport->getResponse();
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
    	$this->transport->setMethod("POST")->request($this->db . "/_view_cleanup");
    
    	return $this->transport->getResponse();
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
    		
    		$this->transport->setMethod("GET")->request($this->db . "/_all_docs" . $qs);
    	} else {
    		$this->transport->setMethod("POST")->setData(compact('keys'))->request($this->db . "/_all_docs");
    	}
    
    	return $this->transport->getResponse();
    }
}
?>