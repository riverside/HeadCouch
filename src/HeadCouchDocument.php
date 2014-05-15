<?php
require_once "HeadCouchException.php";
require_once "HeadCouchHttp.php";
/**
 * CouchDB document wrapper
 *
 * @author Dimitar Ivnaov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.0
 * @package HeadCouch
 */
class HeadCouchDocument
{
/**
 * Database name
 *
 * @var string
 */
    private $db;
/**
 * Document ID
 *
 * @var string
 */
    private $document;
/**
 * Instance of the HTTP client
 *
 * @var mixed
 */
    private $http;
/**
 * Constructor
 *
 * @param string $db
 * @param string $document
 * @param string $host
 * @param number $port
 * @throws HeadCouchException
 */
    public function __construct($db, $document=NULL, $host=NULL, $port=NULL)
    {
        if (empty($db))
        {
            throw new HeadCouchException("\$db could not be empty.");
        }
        if (empty($document))
        {
            $document = md5(uniqid(rand(), true));
        }
        $this->db = $db;
        $this->document = $document;
        $this->http = HeadCouchHttp::newInstance($host, $port);
    }
/**
 * The COPY (which is non-standard HTTP) copies an existing
 * document to a new or existing document.
 *
 * The source document is specified on the request line,
 * with the Destination header of the request specifying
 * the target document.
 *
 * @param string $dest
 * @param string $revision
 * @param boolean $batch
 */
    public function copy($dest, $revision=NULL, $batch=FALSE)
    {
    	if (empty($revision))
    	{
    		$revision = $this->getRevision();
    	}
    	$qs = "?rev=" . $revision;
    	if ($batch)
    	{
    		$qs .= "&batch=ok";
    	}
    	$this->http
    		->setMethod('COPY')
    		->addHeader("Destination: " . $dest)
    		->request($this->db . "/" . $this->document . $qs);
    	
    	return $this->http->getResponse();
    }
/**
 * The PUT method creates a new named document, or creates
 * a new revision of the existing document. Unlike the
 * POST /{db} method, you must specify the document ID
 * in the request URL.
 *
 * @param array $data
 * @param boolean $batch
 */
    public function create($data, $batch=FALSE)
    {
    	$qs = NULL;
    	if ($batch)
    	{
    		$qs = "?batch=ok";
    	}
    	$this->http->setMethod("PUT")->setData($data)->request($this->db . "/" . $this->document . $qs);
    
    	return $this->http->getResponse();
    }
/**
 * Deletes the specified document from the database. You
 * must supply the current (latest) revision, either by
 * using the rev parameter to specify the revision.
 *
 * @param string $revision
 * @param boolean $batch
 */
    public function delete($revision=NULL, $batch=FALSE)
    {
        if (empty($revision))
        {
            $revision = $this->getRevision();
        }
        $qs = "?rev=" . $revision;
        if ($batch)
        {
        	$qs .= "&batch=ok";
        }
        $this->http->setMethod('DELETE')->request($this->db . "/" . $this->document . $qs);

        return $this->http->getResponse();
    }
/**
 * Returns document by the specified docid from the specified
 * db. Unless you request a specific revision, the latest
 * revision of the document will always be returned.
 *
 * @param string $rev
 * @param boolean $revs
 * @param boolean $revs_info
 */
    public function get($rev=NULL, $revs=FALSE, $revs_info=FALSE)
    {
    	$qs = NULL;
    	if (!empty($rev))
    	{
    		$qs = "?rev=" . $rev;
    	} elseif ($revs) {
    		$qs = "?revs=true";
    	} elseif ($revs_info) {
    		$qs = "?revs_info=true";
    	}
        $this->http->setMethod("GET")->request($this->db . "/" . $this->document . $qs);

        return $this->http->getResponse();
    }
/**
 * Returns double quoted document’s revision token
 *
 * @return string
 */
    public function getRevision()
    {
		$headers = $this->head();
		
        return $headers->etag;
    }
/**
 * Returns the HTTP Headers containing a minimal amount of
 * information about the specified document. The method
 * supports the same query arguments as the GET /{db}/{docid}
 * method, but only the header information (including document
 * size, and the revision as an ETag), is returned.
 *
 * @return mixed
 */
    public function head()
    {
    	$this->http->setMethod("HEAD")->request($this->db . "/" . $this->document);
    
    	return json_encode($this->http->getResponseHeaders());
    }
/**
 * Returns new instance of the HeadCouchDocument
 *
 * @param string $db
 * @param string $document
 * @param string $host
 * @param number $port
 * @return HeadCouchDocument
 */
    static public function newInstance($db, $document=NULL, $host=NULL, $port=NULL)
    {
        return new self($db, $document, $host, $port);
    }
}
?>