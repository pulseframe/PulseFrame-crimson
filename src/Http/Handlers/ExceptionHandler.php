<?php

namespace PulseFrame\Crimson\Http\Handlers;

use PulseFrame\Facades\View;
use PulseFrame\Facades\Env;
use PulseFrame\Facades\Log;
use PulseFrame\Facades\Config;
use PulseFrame\Facades\Response;
use PulseFrame\Facades\Translation;
use PulseFrame\Exceptions\NotFoundException;
use PulseFrame\Exceptions\MethodNotAllowedException;
use PulseFrame\Exceptions\AccessForbiddenException;
use PulseFrame\Exceptions\CrimsonFault;

/**
 * Class ExceptionHandler
 * 
 * @category handlers
 * @name ExceptionHandler
 * 
 * This class is responsible for handling application errors and exceptions. It captures exceptions using Sentry, 
 * and displays appropriate error views. It covers various types of exceptions and provides methods to 
 * initialize Sentry and render error views.
 */
class ExceptionHandler
{
  public static $ErrorCode = "";

  public static function initialize()
  {
    try {
      self::initializeSentry(Env::get('sentry_dsn'));

      self::$ErrorCode = substr(bin2hex(random_bytes(6)), 0, 8);

      // Set the exception and error handlers
      set_exception_handler([self::class, 'handle']);
      set_error_handler([self::class, 'handleErrors']);
    } catch (\Throwable $e) {
      throw new CrimsonFault("Failed to initialize ExceptionHandler: " . $e->getMessage(), 0, $e);
    }
  }

  /**
   * Initialize Sentry for error tracking.
   *
   * @category handlers
   * 
   * @param string $dsn The Sentry DSN (Data Source Name).
   * 
   * This static function initializes Sentry with the provided DSN and sets the sample rates for traces and profiles.
   */
  public static function initializeSentry($dsn)
  {
    try {
      \Sentry\init([
        'dsn' => $dsn,
        'traces_sample_rate' => 1.0,
        'profiles_sample_rate' => 1.0,
      ]);
    } catch (\Throwable $e) {
      throw new CrimsonFault("Failed to initialize Sentry: " . $e->getMessage(), 0, $e);
    }
  }

  /**
   * Handle all types of exceptions and errors.
   *
   * @category handlers
   * 
   * @param \Throwable $e The thrown exception or error.
   * 
   * This static function captures exceptions using Sentry and renders appropriate error views based on the type of exception.
   */
  public static function handle(\Throwable $e)
  {
    try {
      \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($e): void {
        $scope->setTag('error_code', self::$ErrorCode);
        \Sentry\captureException($e);
      });

      Log::Exception($e);

      if ($e instanceof NotFoundException) {
        return self::renderErrorView(404, 'The page you are looking for could not be found.');
      } elseif ($e instanceof MethodNotAllowedException) {
        return self::renderErrorView(405, 'The method you are using is not supported.');
      } elseif ($e instanceof AccessForbiddenException) {
        return self::renderErrorView(403, 'Access forbidden.');
      } else {
        return self::renderErrorView(500, "An internal server error occured.", $e);
      }
    } catch (\Throwable $innerException) {
      throw new CrimsonFault("Failed to handle exception: " . $innerException->getMessage(), 0, $innerException);
    }
  }

  /**
   * Handle PHP errors.
   *
   * @category handlers
   * 
   * @param int $severity The severity of the error.
   * @param string $message The error message.
   * @param string $file The filename where the error occurred.
   * @param int $line The line number where the error occurred.
   * @return bool Always returns false to indicate that standard PHP error handling should proceed.
   */
  public static function handleErrors(int $severity, string $message, string $file, int $line): void
  {
    try {
      $error = error_get_last();
      if ($error !== null) {
        Log::exception($error, $severity);
        self::renderErrorView(500, "An internal server error occurred.", $error);
      }
    } catch (\Throwable $e) {
      Log::error("Failed to handle error for file {$file}:{$line} - Message: {$message}");
      throw new CrimsonFault("Failed to handle PHP error: " . $e->getMessage(), 0, $e);
    }
  }

  /**
   * Render an error view.
   *
   * @category handlers
   * 
   * @param int $statusCode The HTTP status code.
   * @param string $message The error message.
   * @param mixed $exception Additional exception details (optional).
   */
  public static function renderErrorView($statusCode, $message, $exception = null)
  {
    try {
      while (ob_get_level()) {
        ob_end_clean();
      }

      ob_start();

      http_response_code($statusCode);

      if ($statusCode === 500) {
        \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($exception): void {
          $scope->setTag('error_code', self::$ErrorCode);
          \Sentry\captureException($exception);
        });
      }

      $ErrorCode = $statusCode === 500 ? self::$ErrorCode : null;

      new View();

      if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (Env::get('app.settings.debug')) {
          if ($ErrorCode) {
            $page = 'error';
          } else {
            $page = View::$errorPage;
          }
        } else {
          $page = View::$errorPage;
        }

        $appName = Env::get('app.name');

        if (Env::get('app.settings.debug') && $exception) {
          include(__DIR__ . "../../../Views/Page.php");
        } else {
          echo View::render($page, [
            'status' => $statusCode,
            'message' => $message,
            'code' => $ErrorCode
          ]);
        }
      } else {
        $message = Config::get('app', 'stage') === "development" ? $exception->getMessage() : Translation::key('errors.error-0');
        echo Response::JSON('error', $message, self::$ErrorCode);
      }
    } catch (\Exception $e) {
      throw new CrimsonFault("Failed to render error view: " . $e->getMessage(), 0, $e);
    } finally {
      exit;
    }
  }
}
