<?php

namespace PulseFrame\Crimson\Exceptions;

class NotFoundException extends \RuntimeException
{
  /**
   * @var int HTTP status code for the exception
   */
  protected $statusCode;

  /**
   * NotFoundException constructor.
   *
   * @param string $message Error message
   * @param int $code HTTP status code (default is 404 for not found)
   * @param \Throwable|null $previous Previous throwable used for exception chaining
   */
  public function __construct(string $message, int $code = 404, \Throwable $previous = null)
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
