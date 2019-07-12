<?php

namespace HeadCouch\Transport;

use HeadCouch\Transport as Transport;

/**
 * Socket transport
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch\Transport
 */
class Socket extends Transport
{
    /**
     * Perform an HTTP request
     *
     * @param string $url
     * @return Socket
     */
    public function request(string $url): self
    {
        $url = 'http://' . $this->host . ':' . $this->port . '/' . $url;
        $parts = parse_url($url);
        $out = "";
        if (function_exists("stream_socket_client")) {
            $fp = stream_socket_client("tcp://$this->host:$this->port", $errno, $errstr, $this->connectTimeout);
        } else {
            $fp = fsockopen($this->host, $this->port, $errno, $errstr, $this->connectTimeout);
        }
        if (!$fp) {
            $this->error = array('code' => $errno, 'text' => $errstr);
        } else {
            $data = $this->getData();
            $this->addHeader("X-HTTP-Method-Override: " . strtoupper($this->method));
            $this->addHeader("Content-Type: application/json");
            switch (strtoupper($this->method)) {
                case 'GET':
                    $out = "GET " . $parts['path'] . (isset($parts['query']) ? "?" . $parts['query'] : NULL) . " HTTP/1.1\r\n";
                    break;
                case 'POST';
                    $out = "POST " . $parts['path'] . (isset($parts['query']) ? "?" . $parts['query'] : NULL) . " HTTP/1.1\r\n";

                    if (!empty($data)) {
                        $this->addHeader("Content-Length: " . strlen($data));
                    }
                    break;
                case "COPY":
                    break;
                case "DELETE":
                    $out = "DELETE " . $parts['path'] . (isset($parts['query']) ? "?" . $parts['query'] : NULL) . " HTTP/1.1\r\n";
                    if (!empty($data)) {
                        $this->addHeader("Content-Length: " . strlen($data));
                    }
                    break;
                case "PUT":
                    $out = "PUT " . $parts['path'] . (isset($parts['query']) ? "?" . $parts['query'] : NULL) . " HTTP/1.1\r\n";
                    if (!empty($data)) {
                        $this->addHeader("Content-Length: " . strlen($data));
                    }
                    break;
                case "HEAD":
                    $out = "HEAD " . $parts['path'] . (isset($parts['query']) ? "?" . $parts['query'] : NULL) . " HTTP/1.1\r\n";
                    break;
            }
            $out .= "Host: " . $this->host . "\r\n";
            if (!empty($this->username) && !empty($this->password)) {
                $this->addHeader("Authorization: Basic " . base64_encode($this->username . ":" . $this->password));
            }
            foreach ($this->getHeaders() as $header) {
                $out .= $header . "\r\n";
            }
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);
            if (!empty($data)) {
                fwrite($fp, $data);
            }
            $response = '';
            $header = "not yet";
            while (!feof($fp)) {
                $line = fgets($fp, 128);
                $this->getHeader(NULL, $line);

                if ($line == "\r\n" && $header == "not yet") {
                    $header = "passed";
                }
                if ($header == "passed") {
                    //$response .= preg_replace('/\n|\r\n/', '', $line);
                    $response .= $line;
                }
            }
            fclose($fp);
            if (isset($this->responseHeaders['transfer-encoding']) && $this->responseHeaders['transfer-encoding'] == 'chunked') {
                $response = self::decode($response);
            }
            $this->response = $response;
        }

        $this->url = $url;

        return $this;
    }

    /**
     * Returns new instance of the Socket
     *
     * @param string|NULL $host
     * @param int|NULL $port
     * @param string|NULL $user
     * @param string|NULL $pswd
     * @return Socket
     */
    static public function newInstance(string $host = NULL, int $port = NULL, string $user = NULL, string $pswd = NULL): self
    {
        return new self($host, $port, $user, $pswd);
    }
}