<?php

namespace Modules\Core\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRequestParameterException extends HttpException
{
    public function __construct(string $message = 'Invalid request parameters.')
    {
        parent::__construct(400, $message);
    }
}
