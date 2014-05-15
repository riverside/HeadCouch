<?php
/**
 * HTTP Client
 *
 * @author Dimitar Ivnaov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.0
 * @package HeadCouch
 */
class HeadCouchHttp
{
/**
 * The number of seconds to wait while trying to connect.
 * Use 0 to wait indefinitely.
 *
 * @var integer
 */
	private $connectTimeout = 30;
/**
 * Holds POST data
 *
 * @var mixed
 */
	private $data = NULL;
/**
 * Holds error code/text
 *
 * @var array
 */
	private $error = array();
/**
 * Holds request headers
 *
 * @var array
 */
	private $headers = array();
/**
 * Hostname
 *
 * @var string
 */
	private $host = '127.0.0.1';
/**
 * Last received HTTP code
 *
 * @var integer
 */
	private $httpCode;
/**
 * Holds information about the last transfer.
 *
 * @var array
 */
	private $httpInfo;
/**
 * HTTP method
 *
 * @var string
 */
	private $method = 'GET';
/**
 * Password for basic authentification
 *
 * @var string
 */
	private $password;
/**
 * Port
 *
 * @var integer
 */
	private $port = 5984;
/**
 * Response
 *
 * @var mixed
 */
	private $response = NULL;
/**
 * Response headers
 *
 * @var array
 */
	private $responseHeaders = array();
/**
 * FALSE to stop cURL from verifying the peer's certificate.
 *
 * @var boolean
 */
	private $sslVerifyPeer = FALSE;
/**
 * The maximum number of seconds to allow cURL functions to execute.
 *
 * @var integer
 */
	private $timeout = 30;
/**
 * URL of last API call
 *
 * @var string
 */
	private $url;
/**
 * The contents of the "User-Agent" header
 *
 * @var string
 */
	private $userAgent = "Zino CouchDB Client/1.0";
/**
 * Username for basic authentification
 *
 * @var string
 */
	private $username;
/**
 * Constructor
 *
 * @param string $host
 * @param number $port
 */
	public function __construct($host=NULL, $port=NULL)
	{
		if (!is_null($host))
		{
			$this->setHost($host);
		}
	
		if (!is_null($port))
		{
			$this->setPort($port);
		}
	}
/**
 * Returns new instance of the HeadCouchHttp
 *
 * @param string $host
 * @param number $port
 * @return HeadCouchHttp
 */
	static public function newInstance($host=NULL, $port=NULL)
	{
		return new self($host, $port);
	}
/**
 * Add http header
 *
 * @param string $header
 * @return HeadCouchHttp
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
 * Perform HTTP request
 *
 * @param string $url
 * @return HeadCouchHttp
 */
	public function request($url)
	{
		$url = 'http://' . $this->host . ':' . $this->port . '/' . $url;
		
		$this->httpInfo = array();
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		if (!empty($this->username) && !empty($this->password))
		{
			curl_setopt($ch, CURLOPT_USERPWD, sprintf("%s:%s", $this->username, $this->password));
		}
	
		$post_fields = $this->getData();
	
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
		$this->addHeader("X-HTTP-Method-Override: " . strtoupper($this->method));
		$this->addHeader("Content-Type: application/json");
		
		switch (strtoupper($this->method))
		{
			case "GET":
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
			case "PUT":
				if (!empty($post_fields))
				{
					$this->addHeader("Content-Length: " . strlen($post_fields));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
				}
				break;
			case "DELETE":
				if (!empty($post_fields))
				{
					$this->addHeader("Content-Length: " . strlen($post_fields));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
				}
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_POST, 1);
				
				if (!empty($post_fields))
				{
					$this->addHeader("Content-Length: " . strlen($post_fields));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
				}
				break;
			case "HEAD":
				curl_setopt($ch, CURLOPT_NOBODY, 1);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				break;
			case "COPY":
				
				break;
		}

		if (!empty($this->headers))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		} else {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		$this->response = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->httpInfo = array_merge($this->httpInfo, curl_getinfo($ch));
		$this->url = $url;
		if (curl_errno($ch) == 28)
		{
			$this->error = array('code' => 109, 'text' => 'Timeout');
		}
		curl_close($ch);

		return $this;
	}
/**
 * Set data
 *
 * @param mixed $data
 * @return HeadCouchHttp
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
 * @return HeadCouchHttp
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
 * @return HeadCouchHttp
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
 * @return HeadCouchHttp
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
 * @return HeadCouchHttp
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
 * @return HeadCouchHttp
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
 * @return HeadCouchHttp
 */
	public function setUsername($username)
	{
		$this->username = $username;
		
		return $this;
	}
}
?>