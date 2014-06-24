<?php
require_once "HeadCouchTransport.php";
/**
 * HTTP Client
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 0.1.1
 * @package HeadCouch
 */
class HeadCouchCurl extends HeadCouchTransport
{
/**
 * Perform HTTP request
 *
 * @param string $url
 * @return HeadCouchCurl
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
 * Returns new instance of the HeadCouchCurl
 *
 * @param string $host
 * @param number $port
 * @param string $user
 * @param string $pswd
 * @return HeadCouchCurl
 */
	static public function newInstance($host=NULL, $port=NULL, $user=NULL, $pswd=NULL)
	{
		return new self($host, $port, $user, $pswd);
	}
}
?>