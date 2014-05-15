<?php
require_once "HeadCouchException.php";
require_once "HeadCouchHttp.php";
class HeadCouchDdoc
{
	private $db;
	
	private $document;
	
	private $http;
	
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
        $this->http->setMethod('DELETE')->request($this->db . "/_design/" . $this->document . $qs);

        return $this->http->getResponse();
    }
	
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
        $this->http->setMethod("GET")->request($this->db . "/_design/" . $this->document . $qs);

        return $this->http->getResponse();
    }
	
    public function getRevision()
    {
    	$headers = $this->head();
    
    	return $headers->etag;
    }
    
	public function head()
	{
		$this->http->setMethod("HEAD")->request($this->db . "/_design/" . $this->document);
	
		return json_encode($this->http->getResponseHeaders());
	}
	
	public function put($data)
	{
		$this->http->setMethod("PUT")->setData($data)->request($this->db . "/_design/" . $this->document);
	
		return $this->http->getResponse();
	}
	
	static public function newInstance($db, $document=NULL, $host=NULL, $port=NULL)
	{
		return new self($db, $document, $host, $port);
	}
}
?>