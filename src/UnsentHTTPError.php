<?php

namespace Souravsspace\Unsent;

use Exception;

/**
 * HTTP error raised when raise_on_error is true and a request fails.
 */
class UnsentHTTPError extends Exception
{
    /**
     * @var int HTTP status code
     */
    public $statusCode;

    /**
     * @var array Error details from API
     */
    public $error;

    /**
     * @var string HTTP method
     */
    public $method;

    /**
     * @var string Request path
     */
    public $path;

    /**
     * Create a new UnsentHTTPError instance.
     *
     * @param int $statusCode HTTP status code
     * @param array $error Error details
     * @param string $method HTTP method
     * @param string $path Request path
     */
    public function __construct(int $statusCode, array $error, string $method, string $path)
    {
        $this->statusCode = $statusCode;
        $this->error = $error;
        $this->method = $method;
        $this->path = $path;

        $code = $error['code'] ?? 'UNKNOWN_ERROR';
        $message = $error['message'] ?? '';
        
        parent::__construct("{$method} {$path} -> {$statusCode} {$code}: {$message}", $statusCode);
    }
}
