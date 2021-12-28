<?php

namespace QeeZer\HyperfApiResponder;

use Hyperf\HttpServer\Response;
use Hyperf\Utils\Arr;
use Psr\Http\Message\ResponseInterface;
use QeeZer\HyperfApiResponder\Entity\ResponseEntity;
use Throwable;

trait Responder
{
    /** @var Response */
    protected $response;

    /**
     * response item.
     * @param $item
     * @param null $resource
     * @param array $meta
     * @return ResponseInterface
     */
    public function responseItem($item, $resource = null, array $meta = []): ResponseInterface
    {
        if ($resource) {
            $resourceInstance = new $resource($item);
            $item = $resourceInstance->toArray();
        }

        return $this->response->json(ResponseEntityFactory::responseEntity($item, $meta));
    }

    /**
     * response collection.
     * @param $collection
     * @param null $resource
     * @param array $meta
     * @return ResponseInterface
     */
    public function responseCollection($collection, $resource = null, array $meta = []): ResponseInterface
    {
        if ($resource) {
            $result = $collection->map(function ($item) use ($resource) {
                $resourceInstance = new $resource($item);
                return $resourceInstance->toArray();
            });
        } else {
            $result = &$collection;
        }

        return $this->response->json(ResponseEntityFactory::responseEntity($result, $meta));
    }

    /**
     * response paginate.
     * @param $paginator
     * @param null $resource
     * @param array $meta
     * @return ResponseInterface
     */
    public function responsePaginate($paginator, $resource = null, array $meta = []): ResponseInterface
    {
        $paginated = $paginator->toArray();
        $links = Arr::only($paginated, ['first_page_url', 'last_page_url', 'prev_page_url', 'next_page_url']);
        $pagination = Arr::except(
            $paginated,
            ['data', 'first_page_url', 'last_page_url', 'prev_page_url', 'next_page_url']
        );

        if ($resource) {
            $result = $paginator->getCollection()->map(function ($item) use ($resource) {
                $resourceInstance = new $resource($item);
                return $resourceInstance->toArray();
            });
        } else {
            $result = $paginator->items();
        }

        return $this->response->json(ResponseEntityFactory::responseEntity($result, array_merge($meta, [
            'pagination' => $pagination,
            'links' => $links,
        ])));
    }

    /**
     * response data.
     * @param $data
     * @param array $meta
     * @param string $message
     * @return ResponseInterface
     */
    public function responseData(
        $data,
        array $meta = [],
        string $message = ResponseEntity::DEFAULT_SUCCESS_MESSAGE
    ): ResponseInterface {
        return $this->response->json(ResponseEntityFactory::responseEntity($data, $meta, $message));
    }

    /**
     * response success.
     * @param string $message
     * @return ResponseInterface
     */
    public function responseSuccess(string $message = ResponseEntity::DEFAULT_SUCCESS_MESSAGE): ResponseInterface
    {
        return $this->response->json(ResponseEntityFactory::responseEntity(null, null, $message));
    }

    /**
     * response fail.
     * @param string $message
     * @param array $data
     * @param int $errorCode
     * @param int $httpStatusCode
     * @return ResponseInterface
     */
    public function responseFail(
        string $message = ResponseEntity::DEFAULT_FAIL_MESSAGE,
        array $data = [],
        int $errorCode = ResponseEntity::DEFAULT_FAIL_CODE,
        int $httpStatusCode = 200
    ): ResponseInterface {
        return $this->response->json(ResponseEntityFactory::responseEntity($data, null, $message, $errorCode))
            ->withStatus($httpStatusCode);
    }

    /**
     * response error.
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    public function responseError(Throwable $throwable): ResponseInterface
    {
        return ResponseError::responseError($throwable, $this->response);
    }

    /**
     * response unauthorized.
     * @param string $message
     * @param int $httpStatusCode
     * @return ResponseInterface
     */
    public function responseUnauthorized(
        string $message = ResponseEntity::DEFAULT_UNAUTHORIZED,
        int $httpStatusCode = 401
    ): ResponseInterface {
        return $this->response->json(ResponseEntityFactory::responseEntity(null, null, $message, 401))
            ->withStatus($httpStatusCode);
    }

    /**
     * response created.
     * @param string $message
     * @return ResponseInterface
     */
    public function responseCreated(string $message = ResponseEntity::DEFAULT_CREATED): ResponseInterface
    {
        return $this->response->json(ResponseEntityFactory::responseEntity(null, null, $message))
            ->withStatus(201);
    }

    /**
     * basic http response.
     * @param string $message
     * @param int $httpStatusCode
     * @return ResponseInterface
     */
    public function responseHttp(string $message = '', int $httpStatusCode = 200): ResponseInterface
    {
        return $this->response
            ->json(ResponseEntityFactory::responseEntity(null, null, $message, $httpStatusCode))
            ->withStatus($httpStatusCode);
    }
}
