<?php

namespace PulseFrame\Crimson\Exceptions;

class ValidationException extends \Exception
{
  /**
   * @var array Associative array of validation errors
   */
  protected $errors;

  /**
   * ValidationException constructor.
   *
   * @param array $errors Associative array where keys are field names and values are error messages
   * @param string $message Optional message for the exception (default is "Validation failed")
   * @param int $code Optional error code (default is 0)
   * @param \Exception|null $previous Optional previous exception for chaining
   */
  public function __construct(array $errors, $message = "Validation failed", $code = 0, \Exception $previous = null)
  {
    $this->errors = $errors;
    $message = $this->formatErrors();
    parent::__construct($message, $code, $previous);
  }

  /**
   * Format the validation errors into a string message.
   *
   * @return string Formatted error message
   */
  protected function formatErrors()
  {
    $errorMessages = [];
    foreach ($this->errors as $field => $error) {
      $errorMessages[] = "Validation failed for field '{$field}', error: {$error}";
    }
    return implode('; ', $errorMessages);
  }

  /**
   * Get the validation errors.
   *
   * @return array Associative array of validation errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
}
