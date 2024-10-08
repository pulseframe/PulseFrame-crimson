<?php

namespace PulseFrame\Crimson\Exceptions;

class RateLimitExceededException extends \RuntimeException
{
  /**
   * @var int HTTP status code for the exception
   */
  protected $statusCode;

  /**
   * RateLimitExceededException constructor.
   *
   * @param string $message Error message
   * @param int $code HTTP status code (default is 429 for rate limit exceeded)
   * @param \Throwable|null $previous Previous throwable used for exception chaining
   */
  public function __construct(string $message, int $code = 429, \Throwable $previous = null)
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
