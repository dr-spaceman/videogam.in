<?php

namespace Vgsite\HTTP;

use InvalidArgumentException;
use Vgsite\API\AccessToken;
use Vgsite\API\Exceptions\APIAuthorizationException;

/**
 * Handles HTTP request
 * 
 * Adopted from Guzzle PSR-7 Request 
 * @link https://github.com/guzzle/psr7
 */

class Request
{
    use MessageTrait;

    private $method;

    private $uri;
    
    /** @var \GuzzleHttp\Psr7\Stream */
    private $body;

    /** @var array URI path split into an array */
    private $path = Array();
    
    /** @var array URI query string parsed into an array */
    private $query = Array();

    /** @var array Parameters set in the query string */
    private $parameters = [
        'q' => null,
        'sort' => null, // format: ?sort=fieldname[:asc|des]
        'sort_by' => 'asc',
        'page' => 1,
        'per_page' => 100,
        'fields' => null,
    ];

    /**
     * @param string        $method  HTTP method
     * @param string        $uri     URI
     * @param array         $headers Request headers
     * @param string|null   $body    Request body
     * @param string        $version Protocol version
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        $path = parse_url($uri, PHP_URL_PATH);
        $path = explode('/', $path);
        array_shift($path); //_blank_
        array_shift($path); //api
        if ($path[0] == 'api.php') {
            array_shift($path);
        }
        $this->path = array_filter($path);

        $querystring = parse_url($uri, PHP_URL_QUERY);
        parse_str($querystring, $query);
        $this->query = $query;

        if ($body !== '' && $body !== null) {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $body);
            fseek($stream, 0);

            $this->body = new \GuzzleHttp\Psr7\Stream($stream);
        }
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getBody(): string
    {
        return $this->body->getContents();
    }

    public function sameDomain($assert=false): bool
    {
        if ($this->getHeader('Referer')) {
            $origin = $this->getHeader('Referer');
            $test = parse_url($origin)['host'] == parse_url(getenv('HOST_DOMAIN'))['host'];
        } else {
            $origin = 'Unknown Origin';
            $test = false;
        }

        if ($assert && !$test) {
            throw new APIAuthorizationException("`$origin` is not authorized to access this resource.");
        }

        return $test;
    }
}
