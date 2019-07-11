<?php
namespace HeadCouch;
/**
 * CouchDB design document wrapper
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch
 */
class Design
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
 * Constructor
 *
 * @param Transport $transport
 * @param string $db
 * @param string $document
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
 * Deletes the specified document from the database. You
 * must supply the current (latest) revision, either by
 * using the $revision parameter to specify the revision.
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
        $this->transport->setMethod("GET")->request($this->db . "/_design/" . $this->document . $qs);

        return $this->transport->getResponse();
    }
/**
 * Returns double quoted document’s revision token
 *
 * @return string
 */
    public function getRevision(): string
    {
    	$headers = $this->head()->toArray();
    
    	return $headers['etag'];
    }
/**
 * Returns the HTTP Headers containing a minimal amount
 * of information about the specified design document.
 *
 * @return Response
 */
	public function head(): Response
	{
		$this->transport->setMethod("HEAD")->request($this->db . "/_design/" . $this->document);
	
		return $this->transport->getResponseHeaders();
	}
/**
 * The PUT method creates a new named design document,
 * or creates a new revision of the existing design document.
 *
 * @param array|string $data
 * @return Response
 */
	public function put($data): Response
	{
		$this->transport->setMethod("PUT")->setData($data)->request($this->db . "/_design/" . $this->document);
	
		return $this->transport->getResponse();
	}
/**
 * Returns new instance of the Design
 *
 * @param Transport $transport
 * @param string $db
 * @param string $document
 * @return Design
 * @throws Exception
 */
	static public function newInstance(Transport $transport, string $db, string $document=NULL): self
	{
		return new self($transport, $db, $document);
	}
}