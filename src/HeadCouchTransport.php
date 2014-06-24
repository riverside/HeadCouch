<?php
/**
 * Transport
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.1
 * @package HeadCouch
 */
abstract class HeadCouchTransport
{
/**
 * The number of seconds to wait while trying to connect.
 * Use 0 to wait indefinitely.
 *
 * @var integer
 */
	protected $connectTimeout = 30;
/**
 * Holds POST data
 *
 * @var mixed
 */
	protected $data = NULL;
/**
 * Holds error code/text
 *
 * @var array
 */
	protected $error = array();
/**
 * Holds request headers
 *
 * @var array
 */
	protected $headers = array();
/**
 * Hostname
 *
 * @var string
 */
	protected $host = '127.0.0.1';
/**
 * Last received HTTP code
 *
 * @var integer
 */
	protected $httpCode;
/**
 * Holds information about the last transfer.
 *
 * @var array
 */
	protected $httpInfo;
/**
 * HTTP method
 *
 * @var string
 */
	protected $method = 'GET';
/**
 * Password for basic authentification
 *
 * @var string
 */
	protected $password;
/**
 * Port
 *
 * @var integer
 */
	protected $port = 5984;
/**
 * Response
 *
 * @var mixed
 */
	protected $response = NULL;
/**
 * Response headers
 *
 * @var array
 */
	protected $responseHeaders = array();
/**
 * FALSE to stop cURL from verifying the peer's certificate.
 *
 * @var boolean
 */
	protected $sslVerifyPeer = FALSE;
/**
 * The maximum number of seconds to allow cURL functions to execute.
 *
 * @var integer
 */
	protected $timeout = 30;
/**
 * URL of last API call
 *
 * @var string
 */
	protected $url;
/**
 * The contents of the "User-Agent" header
 *
 * @var string
 */
	protected $userAgent = "HeadCouch - CouchDB PHP Client/1.0";
/**
 * Username for basic authentification
 *
 * @var string
 */
	protected $username;
/**
 * Constructor
 *
 * @param string $host
 * @param string $port
 * @param string $user
 * @param string $pswd
 */
	public function __construct($host=NULL, $port=NULL, $user=NULL, $pswd=NULL)
	{
		if (!is_null($host))
		{
			$this->setHost($host);
		}
	
		if (!is_null($port))
		{
			$this->setPort($port);
		}
	
		if (!is_null($user))
		{
			$this->setUsername($user);
		}
	
		if (!is_null($pswd))
		{
			$this->setPassword($pswd);
		}
	}
/**
 * Get data
 *
 * @return Ambigous <mixed, string>
 */
	public function getData()
	{
		return $this->data;
	}
/**
 * Get errors from last http request
 *
 * @return array
 */
	public function getError()
	{
		return $this->error;
	}
/**
 * Custom header function
 *
 * @param resource $ch
 * @param string $header
 * @return number
 */
	public function getHeader($ch, $header)
	{
		$i = strpos($header, ':');
		if (!empty($i))
		{
			$key = strtolower(substr($header, 0, $i));
			$value = trim(substr($header, $i + 2));
			$this->responseHeaders[$key] = $value;
		}
	
		return strlen($header);
	}
/**
 * Get request headers
 *
 * @return array
 */
	public function getHeaders()
	{
		return $this->headers;
	}
/**
 * Get method
 *
 * @return string
 */
	public function getMethod()
	{
		return $this->method;
	}
/**
 * Get response from last http request
 *
 * @return mixed
 */
	public function getResponse()
	{
		return $this->response;
	}
/**
 * Get response headers from last http request
 *
 * @return array
 */
	public function getResponseHeaders()
	{
		return $this->responseHeaders;
	}
/**
 * Add http header
 *
 * @param string $header
 * @return HeadCouchCurl
 */
	public function addHeader($header)
	{
		if (0 === stripos(substr($header, -8), 'HTTP/1.') && 3 == count($parts = explode(' ', $header)))
		{
			list($method, $resource, $protocolVersion) = $parts;
	
			$this->setMethod($method);
			//$this->setResource($resource);
			//$this->setProtocolVersion((float) substr($protocolVersion, 5));
		} else {
			$this->headers[] = $header;
		}
	
		return $this;
	}
/**
 *
 * @param array $headers
 * @return array
 */
	protected function flattenHeaders($headers)
	{
		$flattened = array();
		foreach ($headers as $key => $header)
		{
			if (is_int($key))
			{
				$flattened[] = $header;
			} else {
				$flattened[] = $key.': '.$header;
			}
		}
	
		return $flattened;
	}
/**
 * Set data
 *
 * @param mixed $data
 * @return HeadCouchCurl
 */
	public function setData($data)
	{
		if (is_array($data))
		{
			$data = json_encode($data);
		}
		$this->data = $data;
	
		return $this;
	}
/**
 * Set multiple headers at once
 *
 * @param array $headers
 * @return HeadCouchCurl
 */
	public function setHeaders($headers)
	{
		$this->headers = array();
	
		foreach ($this->flattenHeaders($headers) as $header)
		{
			$this->addHeader($header);
		}
	
		return $this;
	}
/**
 * Set hostname
 *
 * @param string $host
 * @return HeadCouchTransport
 */
	public function setHost($host)
	{
		$this->host = $host;
	
		return $this;
	}
/**
 * Set http method
 *
 * @param string $method Possible values: GET, POST, DELETE, PUT, HEAD, COPY
 * @return HeadCouchTransport
 */
	public function setMethod($method)
	{
		$this->method = strtoupper($method);
	
		return $this;
	}
/**
 * Set passwrod
 *
 * @param string $password
 * @return HeadCouchTransport
 */
	public function setPassword($password)
	{
		$this->password = $password;
	
		return $this;
	}
/**
 * Set port number
 *
 * @param number $port
 * @return HeadCouchTransport
 */
	public function setPort($port)
	{
		$this->port = $port;
	
		return $this;
	}
/**
 * Set username
 *
 * @param string $username
 * @return HeadCouchTransport
 */
	public function setUsername($username)
	{
		$this->username = $username;
	
		return $this;
	}
/**
 * Shortland to the "request" method
 *
 * @param string $url
 * @return HeadCouchTransport
 */
	public function get($url)
	{
		$this->setMethod("GET")->request($url);
	
		return $this;
	}
/**
 * Shortland to the "request" method
 *
 * @param string $url
 * @return HeadCouchTransport
 */
	public function post($url)
	{
		$this->setMethod("POST")->request($url);
	
		return $this;
	}
/**
 * Shortland to the "request" method
 *
 * @param string $url
 * @return HeadCouchTransport
 */
	public function put($url)
	{
		$this->setMethod("PUT")->request($url);
	
		return $this;
	}
/**
 * Shortland to the "request" method
 *
 * @param string $url
 * @return HeadCouchTransport
 */
	public function delete($url)
	{
		$this->setMethod("DELETE")->request($url);
	
		return $this;
	}
/**
 * Shortland to the "request" method
 *
 * @param string $url
 * @return HeadCouchTransport
 */
	public function head($url)
	{
		$this->setMethod("HEAD")->request($url);
	
		return $this;
	}
/**
 * Shortland to the "request" method
 *
 * @param string $url
 * @return HeadCouchTransport
 */
	public function copy($url)
	{
		$this->setMethod("COPY")->request($url);
	
		return $this;
	}
/**
 * Create new instance
 *
 * @param string $host
 * @param string $port
 * @param string $user
 * @param string $pswd
 */
	abstract static public function newInstance($host=NULL, $port=NULL, $user=NULL, $pswd=NULL);
}
?>