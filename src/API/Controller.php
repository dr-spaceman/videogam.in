<?php

namespace Vgsite\API;

use Vgsite\Registry;
use Vgsite\API\Exceptions\APIException;
use Vgsite\API\Exceptions\APIInvalidArgumentException;
use Vgsite\API\Exceptions\APINotFoundException;
use Vgsite\HTTP\Request;
use Vgsite\HTTP\Response;
use Respect\Validation\Validator as v;

/**
 * Request input, Response output
 */
abstract class Controller
{
    /** @var int Number of items per page */
    const PER_PAGE = 100;

    /** @var array For `?sort` parameter; List of keys to whitelist */
    const SORTABLE_FIELDS = Array();

    /** @var Request */
    protected $request;

    /** @var CollectionJson Collection+JSON object */
    private $collection;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->collection = new CollectionJson();
    }

    public function processRequest(): void
    {
        try {
            switch ($this->request->getMethod()) {
                case 'GET':
                    if (! $this->request->getPath()[1]) {
                        $this->getAll();
                    } else {
                        $this->getOne($this->request->getPath()[1]);
                    };
                break;
                case 'POST':
                    $this->createFromRequest();
                break;
                case 'PUT':
                    $this->updateFromRequest();
                break;
                case 'DELETE':
                    $this->delete();
                break;
                default:
                    $message = sprintf(
                        'Request Method not valid (%s received). Try one of: GET, POST, PUT, DELETE.', 
                        $this->request_method
                    );
                    throw new APIException($message, null, 'INVALID_REQUEST_METHOD', 400);
            }
        } catch (APIException $e) {
            Registry::get('logger')->warning($e);

            $this->collection->setError($e->getErrorMessage());

            $this->render($e->getCode());
        } catch (\Exception | \Error $e) {
            Registry::get('logger')->warning($e);

            $error = ['title' => 'Server error', 'message' => $e->getMessage()];
            $this->collection->setError($error);

            $this->render(500);
        }
    }

    public function processSearchRequest()
    {
        if ($this->request_method != 'GET') {
            throw new APIInvalidArgumentException(
                sprintf(
                    'Request Method not valid (%s received). Try: GET.',
                    $this->request_method
                ),
                405
            );
        }

        $response = $this->getSearchResults($this->queries[0]);

        header($response['status_code_header']);
        if ($response['payload']) {
            echo json_encode($response['payload']);
        }
    }

    abstract protected function getOne($id): void;
    abstract protected function getAll(): void;

    // Model response method
    private function getUser($id)
    {
        $result = $this->personGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateFromRequest()
    {
        $result = $this->personGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function delete()
    {
        $result = $this->personGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->personGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    public function setPayload(array $items): self
    {
        $this->collection->setItems($items);
        // var_dump('Response::setPayload', $items, $this->collection);
        return $this;
    }

    public function render(int $code, array $headers=[], $body=null): void
    {
        // echo 'hi';
        // var_dump('Controller::render');
        $response = new Response($code, $headers);
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
        $response->withHeader('Access-Control-Allow-Methods', 'OPTIONS,GET,POST,PUT,DELETE');
        $response->withHeader('Access-Control-Max-Age', '3600');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        header(
            sprintf(
                'HTTP/%s %d %s', 
                $response->getProtocolVersion(), 
                $code,
                $response->getReasonPhrase()
            )
        );
        foreach ($response->getHeaders() as $header_name => $headers) {
            header(sprintf("%s: %s", $header_name, $response->getHeaderLine($header_name)));
        };

        if (isset($body)) {
            echo (string) $body;
        } else {
            echo $this->collection;
        }

        // $response = new Response($code, $headers);
        // var_dump($response);
        // $headers_default = [
        //     'Access-Control-Allow-Origin' => '*',
        //     'Content-Type' => 'application/json; charset=UTF-8',
        //     'Access-Control-Allow-Methods' => 'OPTIONS,GET,POST,PUT,DELETE',
        //     'Access-Control-Max-Age' => '3600',
        //     'Access-Control-Allow-Headers' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With',
        // ];

        // header(
        //     sprintf(
        //         'HTTP/%s %d %s',
        //         $response->getProtocolVersion(),
        //         $code,
        //         $response->getReasonPhrase()
        //     )
        // );
        // foreach ($headers_default as $key => $value) {
        //     header(sprintf("%s: %s", $key, $value));
        // };

        // echo 'fuu';
    }

    /**
     * Parse a variable in the query string. Returned value is appropriate to
     * use to fetch data or some other operation.
     *
     * @param string $key Key in querystring
     * @param int|string $default Default value to return if the 
     * @param callable $test Additional test to perform; Throw exception on failure
     *
     * @return string
     */
    public function parseQuery(string $key, $default, callable $test=null): string
    {
        $query = $this->request->getQuery();
        $value = $query[$key];

        if (empty($value)) {
            return $default;
        }

        // Run default tests
        switch ($key) {
            case 'page':
                if (! v::IntVal()->validate($value)) {
                    throw new APIInvalidArgumentException('Property `page` must be an integer.', '?page');
                }
                if ($value < 1) {
                    throw new APIInvalidArgumentException('Property `page` must be an integer greater than zero.', '?page');
                }
            break;

            case 'per_page':
                // static invocation allows extending classes to modify the constant PER_PAGE
                if ($value > static::PER_PAGE) {
                    throw new APIInvalidArgumentException(
                        sprintf('Requested number of items per page `%s` exceeds the maximum of %d', $value, static::PER_PAGE), '?per_page'
                    );
                }
            break;

            case 'sort':
                if(! in_array($value, static::SORTABLE_FIELDS)) {
                    throw new APIInvalidArgumentException(
                        sprintf(
                            'Requested sort key `%s` is out of the range of options available. Try one of: %s.',
                            $value,
                            implode(', ', static::SORTABLE_FIELDS)
                        ), '?sort'
                    );
                }
            break;

            case 'sort_dir':
                if (! in_array(strtolower($value), ['asc', 'desc'])) {
                    throw new APIInvalidArgumentException('Parameter `sort_dir` must be one of: asc, desc.', '?sort_dir');
                }
            break;
        }

        if (isset($test)) {
            if (false === call_user_func($test, $value)) {
                throw new APIInvalidArgumentException(
                    sprintf('Invalid parameter given for key `%s`. Suggested value: %s.', $key, $default),
                    sprintf('?%s', $key)
                );
            }
        }

        return $value;
    }

    public function convertPageToLimit(int $page, int $per_page): array
    {
        $min = ($page - 1) * $per_page;
        return [$min, $per_page];
    }
}
