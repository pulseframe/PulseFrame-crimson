<?php

namespace PulseFrame\Crimson\Exceptions;

class AccessForbiddenException extends \RuntimeException
{
  /**
   * @var int HTTP status code for the exception
   */
  protected $statusCode;

  /**
   * AccessForbiddenException constructor.
   *
   * @param string $message Error message
   * @param int $code HTTP status code (default is 403 for forbidden)
   * @param \Throwable|null $previous Previous throwable used for exception chaining
   */
  public function __construct(string $message, int $code = 403, \Throwable $previous = null)
  {
    $this->statusCode = $code;
    parent::__construct($message, $code, $previous);
  }

  /**
   * Get the HTTP status code for this exception.
   *
   * @return int HTTP status code
   */
  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
