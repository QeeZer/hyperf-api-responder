<?php

namespace QeeZer\HyperfApiResponder;

use QeeZer\HyperfApiResponder\Entity\DataEntity;
use QeeZer\HyperfApiResponder\Entity\ResponseEntity;

class ResponseEntityFactory
{
    /**
     * DataEntity.
     * @param mixed $data
     * @param mixed $meta
     */
    public static function dataEntity($data = null, $meta = null): DataEntity
    {
        return new DataEntity(empty($meta) ? null : $meta, empty($data) ? null : $data);
    }

    /**
     * ResponseEntity.
     * @param mixed $data
     * @param mixed $meta
     */
    public static function responseEntity(
        $data = null,
        $meta = null,
        string $message = ResponseEntity::DEFAULT_SUCCESS_MESSAGE,
        int $code = ResponseEntity::SUCCESS_CODE
    ): ResponseEntity {
        return new ResponseEntity($code, $message, self::dataEntity($data, $meta));
    }
}
