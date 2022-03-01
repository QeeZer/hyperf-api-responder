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

        if (is_string($throwable->getCode())) {
            $errorCode = ResponseEntity::DEFAULT_STRING_CODE;
        } else {
            // 因 php 异常 code 默认为 0 与接口默认成功 code 为 0 冲突所以转换一下.
            $errorCode = $throwable->getCode() === ResponseEntity::SUCCESS_CODE
                ? ResponseEntity::DEFAULT_FAIL_CODE
                : $throwable->getCode();
        }

        if ($throwable instanceof NondisclosureException) {
            $message = ResponseEntity::DEFAULT_ERROR_MESSAGE;
        } else {
            // 若 code 为 string 时，则将 code 放 message 前面.
            $message = is_string($throwable->getCode())
                ? '[' . $throwable->getCode() . ']' . $throwable->getMessage()
                : $throwable->getMessage();
        }

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
