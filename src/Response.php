<?php
namespace HeadCouch;

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
    public function __construct(string $response="")
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