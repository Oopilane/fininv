<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception {

    private $errorCode;

    public function __construct(string $message, int $errorCode = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): int { return $this->errorCode; }

}