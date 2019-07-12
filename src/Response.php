<?php

namespace HeadCouch;
/**
 * CouchDB response class
 *
 * @author Dimitar Ivanov (http://twitter.com/DimitarIvanov)
 * @link http://github.com/riverside/HeadCouch
 * @license MIT
 * @version 1.0.0
 * @package HeadCouch
 */
class Response
{
    /**
     * @var string
     */
    protected $response = "";

    /**
     * Response constructor.
     *
     * @param string $response
     */
    public function __construct(string $response = "")
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return json_decode($this->response, true);
    }

    /**
     * @return \stdClass
     */
    public function toObject(): \stdClass
    {
        return json_decode($this->response);
    }
}