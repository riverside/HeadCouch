<?php

namespace HeadCouch;
/**
 * Transport
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch
 */
abstract class Transport
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
     * Password for basic authentication
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
     * Username for basic authentication
     *
     * @var string
     */
    protected $username;

    /**
     * Constructor
     *
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pswd
     */
    public function __construct(string $host = NULL, int $port = NULL, string $user = NULL, string $pswd = NULL)
    {
        if (!is_null($host)) {
            $this->setHost($host);
        }

        if (!is_null($port)) {
            $this->setPort($port);
        }

        if (!is_null($user)) {
            $this->setUsername($user);
        }

        if (!is_null($pswd)) {
            $this->setPassword($pswd);
        }
    }

    /**
     * Get data
     *
     * @return array|string
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
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * Custom header function
     *
     * @param resource $ch
     * @param string $header
     * @return int
     */
    public function getHeader($ch, string $header): int
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
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
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get response from last http request
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return new Response($this->response);
    }

    /**
     * Get response headers from last http request
     *
     * @return Response
     */
    public function getResponseHeaders(): Response
    {
        return new Response(json_encode($this->responseHeaders));
    }

    /**
     * Add http header
     *
     * @param string $header
     * @return Transport
     */
    public function addHeader(string $header): self
    {
        if (0 === stripos(substr($header, -8), 'HTTP/1.') && 3 == count($parts = explode(' ', $header))) {
            //list($method, $resource, $protocolVersion) = $parts;
            $method = $parts[0];

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
    protected function flattenHeaders(array $headers)
    {
        $flattened = array();
        foreach ($headers as $key => $header) {
            if (is_int($key)) {
                $flattened[] = $header;
            } else {
                $flattened[] = $key . ': ' . $header;
            }
        }

        return $flattened;
    }

    /**
     * Set data
     *
     * @param mixed $data
     * @return Transport
     */
    public function setData($data): self
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $this->data = $data;

        return $this;
    }

    /**
     * Set multiple headers at once
     *
     * @param array $headers
     * @return Transport
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = array();

        foreach ($this->flattenHeaders($headers) as $header) {
            $this->addHeader($header);
        }

        return $this;
    }

    /**
     * Set hostname
     *
     * @param string $host
     * @return Transport
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Set http method
     *
     * @param string $method Possible values: GET, POST, DELETE, PUT, HEAD, COPY
     * @return Transport
     */
    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Transport
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set port number
     *
     * @param int $port
     * @return Transport
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Transport
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Shortland to the "request" method
     *
     * @param string $url
     * @return Transport
     */
    public function get(string $url): self
    {
        $this->setMethod("GET")->request($url);

        return $this;
    }

    /**
     * Shortland to the "request" method
     *
     * @param string $url
     * @return Transport
     */
    public function post(string $url): self
    {
        $this->setMethod("POST")->request($url);

        return $this;
    }

    /**
     * Shortland to the "request" method
     *
     * @param string $url
     * @return Transport
     */
    public function put(string $url): self
    {
        $this->setMethod("PUT")->request($url);

        return $this;
    }

    /**
     * Shortland to the "request" method
     *
     * @param string $url
     * @return Transport
     */
    public function delete(string $url): self
    {
        $this->setMethod("DELETE")->request($url);

        return $this;
    }

    /**
     * Shortland to the "request" method
     *
     * @param string $url
     * @return Transport
     */
    public function head(string $url): self
    {
        $this->setMethod("HEAD")->request($url);

        return $this;
    }

    /**
     * Shortland to the "request" method
     *
     * @param string $url
     * @return Transport
     */
    public function copy(string $url): self
    {
        $this->setMethod("COPY")->request($url);

        return $this;
    }

    /**
     * Decode a chunked-encoded string
     *
     * @param string $str
     * @return string
     */
    public static function decode(string $str): string
    {
        for ($res = ''; !empty($str); $str = trim($str)) {
            $pos = strpos($str, "\r\n");
            $len = hexdec(substr($str, 0, $pos));
            $res .= substr($str, $pos + 2, $len);
            $str = substr($str, $pos + 2 + $len);
        }

        return $res;
    }

    /**
     * Create new instance
     *
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pswd
     */
    abstract static public function newInstance(string $host = NULL, int $port = NULL, string $user = NULL, string $pswd = NULL);

    /**
     * Make a request
     *
     * @param string $url
     */
    abstract public function request(string $url);
}