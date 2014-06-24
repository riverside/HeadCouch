<?php
require_once "HeadCouchException.php";
/**
 * CouchDB document wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.1
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
 * @param string $document
 * @throws HeadCouchException
 */
    public function __construct($transport, $db, $document=NULL)
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
        $this->transport = $transport;
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
    	$this->transport
    		->setMethod('COPY')
    		->addHeader("Destination: " . $dest)
    		->request($this->db . "/" . $this->document . $qs);
    	
    	return $this->transport->getResponse();
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
    	$this->transport->setMethod("PUT")->setData($data)->request($this->db . "/" . $this->document . $qs);
    
    	return $this->transport->getResponse();
    }
/**
 * Get/set database name
 *
 * @param string $dbName
 * @return string|HeadCouchDocument
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
        $this->transport->setMethod('DELETE')->request($this->db . "/" . $this->document . $qs);

        return $this->transport->getResponse();
    }
/**
 * Get/set document name
 *
 * @param string $docName
 * @return string|HeadCouchDocument
 */
    public function doc($docName=NULL)
    {
    	if (is_null($docName))
    	{
    		return $this->document;
    	}
    	 
    	$this->document = $docName;
    	 
    	return $this;
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
        $this->transport->setMethod("GET")->request($this->db . "/" . $this->document . $qs);

        return $this->transport->getResponse();
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
    	$this->transport->setMethod("HEAD")->request($this->db . "/" . $this->document);
    
    	return json_encode($this->transport->getResponseHeaders());
    }
/**
 * Returns new instance of the HeadCouchDocument
 *
 * @param object $transport
 * @param string $db
 * @param string $document
 * @return HeadCouchDocument
 */
    static public function newInstance($transport, $db, $document=NULL)
    {
        return new self($transport, $db, $document);
    }
}
?>