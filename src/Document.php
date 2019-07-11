<?php
namespace HeadCouch;
/**
 * CouchDB document wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch
 */
class Document
{
/**
 * Database name
 *
 * @var string
 */
    protected $db;
/**
 * Document ID
 *
 * @var string
 */
    protected $document;
/**
 * Instance of the transport
 *
 * @var mixed
 */
    protected $transport;
/**
 * Document constructor.
 *
 * @param Transport $transport
 * @param string $db
 * @param string|NULL $document
 * @throws Exception
 */
    public function __construct(Transport $transport, string $db, string $document=NULL)
    {
        if (empty($db))
        {
            throw new Exception("\$db could not be empty.");
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
 * @return Response
 */
    public function copy(string $dest, string $revision=NULL, bool $batch=FALSE): Response
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
 * @return Response
 */
    public function create($data, bool $batch=NULL): Response
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
 * @return string|Document
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
 * @return Response
 */
    public function delete(string $revision=NULL, bool $batch=FALSE): Response
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
 * @return string|Document
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
 * @return Response
 */
    public function get(string $rev=NULL, bool $revs=FALSE, bool $revs_info=FALSE): Response
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
        $headers = $this->head()->toArray();

        return $headers['etag'];
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
    
    	return $this->transport->getResponseHeaders();
    }
/**
 * Returns new instance of the Document
 *
 * @param Transport $transport
 * @param string $db
 * @param string $document
 * @return Document
 * @throws Exception
 */
    static public function newInstance(Transport $transport, string $db, string $document=NULL): self
    {
        return new self($transport, $db, $document);
    }
}