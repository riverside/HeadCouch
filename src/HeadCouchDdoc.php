<?php
require_once "HeadCouchException.php";
/**
 * CouchDB design document wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.1
 * @package HeadCouch
 */
class HeadCouchDdoc
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
 * Deletes the specified document from the database. You
 * must supply the current (latest) revision, either by
 * using the $revision parameter to specify the revision.
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
        $this->transport->setMethod('DELETE')->request($this->db . "/_design/" . $this->document . $qs);

        return $this->transport->getResponse();
    }
/**
 * Returns design document with the specified design document`
 * from the specified database. Unless you request a specific
 * revision, the latest revision of the document will always
 * be returned.
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
        $this->transport->setMethod("GET")->request($this->db . "/_design/" . $this->document . $qs);

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
 * Returns the HTTP Headers containing a minimal amount
 * of information about the specified design document.
 *
 * @return string
 */
	public function head()
	{
		$this->transport->setMethod("HEAD")->request($this->db . "/_design/" . $this->document);
	
		return json_encode($this->transport->getResponseHeaders());
	}
/**
 * The PUT method creates a new named design document,
 * or creates a new revision of the existing design document.
 *
 * @param unknown $data
 */
	public function put($data)
	{
		$this->transport->setMethod("PUT")->setData($data)->request($this->db . "/_design/" . $this->document);
	
		return $this->transport->getResponse();
	}
/**
 * Returns new instance of the HeadCouchDdoc
 *
 * @param object $transport
 * @param string $db
 * @param string $document
 * @return HeadCouchDdoc
 */
	static public function newInstance($transport, $db, $document=NULL)
	{
		return new self($transport, $db, $document);
	}
}
?>