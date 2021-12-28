<?php

namespace QeeZer\HyperfApiResponder;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Psr\Http\Message\ResponseInterface;
use QeeZer\HyperfApiResponder\Contracts\BusinessException;
use QeeZer\HyperfApiResponder\Contracts\NondisclosureException;
use QeeZer\HyperfApiResponder\Entity\ResponseEntity;
use Throwable;

class ResponseError
{
    /**
     * response error.
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public static function responseError(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $data = null;

        if ((! $throwable instanceof BusinessException)
            && (env('APP_ENV') === 'local' || env('APP_DEBUG') === true)) {
            $data = (new ExceptionInfo($throwable))->getExceptionInfo();
        }

        $errorCode = $throwable->getCode() === ResponseEntity::SUCCESS_CODE
            ? ResponseEntity::DEFAULT_FAIL_CODE
            : $throwable->getCode();

        $message = $throwable instanceof NondisclosureException
            ? ResponseEntity::DEFAULT_ERROR_MESSAGE
            : $throwable->getMessage();

        $statusCode = $throwable instanceof BusinessException ? 200 : 500;

        return $response
            ->withStatus($statusCode)
            ->withBody(new SwooleStream(Json::encode(ResponseEntityFactory::responseEntity(
                $data,
                null,
                $message,
                $errorCode
            ))))->withHeader('Content-Type', 'application/json');
    }
}
