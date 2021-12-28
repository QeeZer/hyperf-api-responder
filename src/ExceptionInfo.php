<?php

namespace QeeZer\HyperfApiResponder;

use Throwable;

class ExceptionInfo
{
    /** @var Throwable */
    private $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function getExceptionInfo(): array
    {
        return [
            'message' => $this->exception->getMessage(),
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
            'trace' => $this->exception->getTraceAsString(),
        ];
    }
}
