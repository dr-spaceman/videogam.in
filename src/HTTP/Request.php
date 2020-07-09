<?php

namespace Vgsite\HTTP;

use Vgsite\API\Exceptions\APIException;
use Vgsite\API\Exceptions\APIInvalidArgumentException;

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
        $this->path = $path;

        $querystring = parse_url($uri, PHP_URL_QUERY);
        parse_str($querystring, $query);
        $this->query = $query;

        if ($body !== '' && $body !== null) {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $body);
            fseek($stream, 0);

            $this->stream = new \GuzzleHttp\Psr7\Stream($stream);
        }
    }

    /**
     * Parse sort query string value
     * Format: [?sort=]fieldname[:asc|desc]
     *
     * @param string $sort_query The query string
     * 
     * @return array [fieldname, sort_direction(asc|desc)]
     */
    public function parseSortQuery(string $sort_query, array $allowed_fields=[]): array
    {
        $test = '/^\??(sort=)?([a-z\-_]*):?(asc|desc)?$/i';
        if (! preg_match($test, $sort_query, $matches)) {
            throw new APIInvalidArgumentException('Sort parameter not in valid format. Try: `?sort=fieldname[:asc|desc]`', '?sort');
        }

        if (! empty($allowed_fields) && false === array_search($matches[2], $allowed_fields)) {
            throw new APIInvalidArgumentException(
                sprintf('Sort parameter fieldname given (`%s`) is not allowed. Try one of: %s.', $matches[2], implode(', ', $allowed_fields)), 
                '?sort'
            );
        }

        return [$matches[2], $matches[3]];
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
}