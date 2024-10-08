<?php

namespace PulseFrame\Crimson;

/**
 * Class StackTraceViewer
 *
 * This class processes an exception's stack trace and manages file content retrieval for viewing.
 *
 * @category ExceptionHandling
 * @name StackTraceViewer
 * @package YourPackageName
 */
class StackTraceHandler
{
  private $exception;
  private $stackTraces = [];
  private $fileToView;
  private $highlightLines = [];

  /**
   * StackTraceViewer constructor.
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
    $trace = $this->exception->getTrace();

    foreach ($trace as $item) {
      $file = $item['file'] ?? '[internal]';
      $line = $item['line'] ?? 0;
      if ($file !== '[internal]') {
        $key = "{$file}:{$line}";
        if (!isset($this->stackTraces[$key])) {
          $this->stackTraces[$key] = [
            'file' => $file,
            'line' => $line,
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
    if (isset($_GET['file']) && isset($_GET['line'])) {
      $this->fileToView = $_GET['file'];
      $this->highlightLines = explode(',', $_GET['line']);
    } elseif (!empty($this->stackTraces)) {
      $this->fileToView = $this->stackTraces[0]['file'];
      $this->highlightLines = [$this->stackTraces[0]['line']];
    }
  }

  /**
   * Get the content of the file to view.
   *
   * @return array|null The content of the file or null if not found.
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
}
