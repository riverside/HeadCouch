<?php

namespace HeadCouch\Transport;

use HeadCouch\Transport as Transport;

/**
 * HTTP Client
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch\Transport
 */
class File extends Transport
{
    /**
     * Perform an HTTP request
     *
     * @param string $url
     * @return File
     */
    public function request(string $url): self
    {
        $url = 'http://' . $this->host . ':' . $this->port . '/' . $url;

        $data = $this->getData();

        $options = array(
            'http' => array(
                'method' => $this->method,
                'user_agent' => $this->userAgent,
                'follow_location' => 1,
                'protocol_version' => 1.1,
                'timeout' => $this->timeout,
                'header' => '',
            )
        );

        $this->addHeader("X-HTTP-Method-Override: " . strtoupper($this->method));
        $this->addHeader("Content-Type: application/json");
        $this->addHeader("Accept: application/json");
        switch (strtoupper($this->method)) {
            case 'GET':
                break;
            case 'POST';
                if ($data) {
                    $this->addHeader("Content-Length: " . strlen($data));
                    $options['http']['content'] = $data;
                }
                break;
            case "COPY":
                break;
            case "DELETE":
                if ($data) {
                    $this->addHeader("Content-Length: " . strlen($data));
                    $options['http']['content'] = $data;
                }
                break;
            case "PUT":
                if ($data) {
                    $this->addHeader("Content-Length: " . strlen($data));
                    $options['http']['content'] = $data;
                }
                break;
            case "HEAD":
                break;
        }
        if (!empty($this->username) && !empty($this->password)) {
            $this->addHeader("Authorization: Basic " . base64_encode($this->username . ":" . $this->password));
        }
        foreach ($this->getHeaders() as $header) {
            $options['http']['header'] .= $header . "\r\n";
        }

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        foreach ($http_response_header as $line) {
            $this->getHeader(NULL, $line);
        }

        if ($response == false) {
            $this->error = array('code' => http_response_code(), 'text' => '');
        } else {
            $this->response = $response;
        }

        $this->url = $url;

        return $this;
    }

    /**
     * Returns new instance of the File
     *
     * @param string|NULL $host
     * @param int|NULL $port
     * @param string|NULL $user
     * @param string|NULL $pswd
     * @return File
     */
    static public function newInstance(string $host = NULL, int $port = NULL, string $user = NULL, string $pswd = NULL): self
    {
        return new self($host, $port, $user, $pswd);
    }
}