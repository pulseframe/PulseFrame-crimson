<?php

namespace PulseFrame\Crimson;

/**
 * Class StackTraceHandler
 *
 * This class processes an exception's stack trace and manages file content retrieval for viewing.
 *
 * @category ExceptionHandling
 * @name StackTraceHandler
 * @package YourPackageName
 */
class StackTraceHandler
{
  private $exception;
  private $stackTraces = [];
  private $fileToView;
  private $highlightLines = [];

  // Define allowed directories for file access
  private const ALLOWED_DIRECTORIES = [
    ROOT_DIR
  ];

  /**
   * StackTraceHandler constructor.
   *
   * @param \Throwable $exception The exception to process.
   */
  public function __construct(\Throwable $exception)
  {
    $this->exception = $exception;
    $this->processTrace();
    $this->determineFileToView();
  }

  /**
   * Process the stack trace from the exception.
   */
  private function processTrace()
  {
    // Capture the stack trace
    $trace = $this->exception->getTrace();

    // Log the original exception file and line
    $this->stackTraces[] = [
      'file' => $this->exception->getFile(),
      'line' => $this->exception->getLine(),
      'function' => '[Exception Thrown]',
    ];

    foreach ($trace as $item) {
      $file = $item['file'] ?? '[internal]';
      $line = $item['line'] ?? 0;

      $function = $item['function'] ?? '[unknown function]';

      if ($file !== '[internal]') {
        $key = "{$file}:{$line}";
        if (!isset($this->stackTraces[$key])) {
          $this->stackTraces[$key] = [
            'file' => $file,
            'line' => $line,
            'function' => $function,
          ];
        }
      }
    }

    $this->stackTraces = array_values($this->stackTraces);
  }

  /**
   * Determine which file to view based on GET parameters or default to the first stack trace.
   */
  private function determineFileToView()
  {
    // Check cookies for file and line
    $requestedFile = \PulseFrame\Facades\Cookie::get('file');
    $this->highlightLines = explode(',', \PulseFrame\Facades\Cookie::get('line', ''));

    if ($requestedFile) {
      // Sanitize and validate the file path
      if ($this->isValidFile($requestedFile)) {
        $this->fileToView = realpath($requestedFile);
      } else {
        $this->fileToView = null; // Handle invalid file request
      }
    } elseif (!empty($this->stackTraces)) {
      $this->fileToView = $this->stackTraces[0]['file'];
      $this->highlightLines = [$this->stackTraces[0]['line']];
    }
  }

  /**
   * Validate the requested file against allowed directories.
   *
   * @param string $file The file path to validate.
   * @return bool True if the file is valid, false otherwise.
   */
  private function isValidFile($file)
  {
    // Normalize the path
    $realPath = realpath($file);

    // Check if the file path is not false and is within allowed directories
    if ($realPath !== false) {
      foreach (self::ALLOWED_DIRECTORIES as $allowedDir) {
        if (strpos($realPath, realpath($allowedDir)) === 0) {
          return true; // File is valid
        }
      }
    }

    return false; // File is not valid
  }

  /**
   * Get the content of the file to view.
   *
   * @return array|null The content of the file or null if not found or invalid.
   */
  public function getFileContent()
  {
    return $this->fileToView ? (file_exists($this->fileToView) ? file($this->fileToView) : null) : null;
  }

  /**
   * Get the stack traces for display.
   *
   * @return array
   */
  public function getStackTraces()
  {
    return $this->stackTraces;
  }

  /**
   * Get the file to view.
   *
   * @return string|null
   */
  public function getFileToView()
  {
    return $this->fileToView;
  }

  /**
   * Get the highlighted lines.
   *
   * @return array
   */
  public function getHighlightLines()
  {
    return $this->highlightLines;
  }

  /**
   * Get the exception name without the namespace if it belongs to PulseFrame\Crimson\Exceptions.
   *
   * @return string The processed exception name.
   */
  public function getExceptionName()
  {
    $exceptionClass = get_class($this->exception);

    // Check if the exception belongs to the PulseFrame\Crimson\Exceptions namespace
    if (strpos($exceptionClass, 'PulseFrame\\Crimson\\Exceptions\\') === 0) {
      return (new \ReflectionClass($this->exception))->getShortName(); // Remove the namespace
    }

    return $exceptionClass; // Return the full class name if not
  }
}
