<?php

namespace PulseFrame\Crimson\Http\Providers;

use PulseFrame\Facades\Config;
use PulseFrame\Contracts\ServiceProviderInterface;
use PulseFrame\Http\Handlers\RouteHandler;
use PulseFrame\Crimson\Http\Handlers\ExceptionHandler;

class CrimsonServiceProvider implements ServiceProviderInterface
{
  public function register(): void
  {
    Config::set('view', "error_handler", [self::class, 'handleCrimsonErrors']);

    set_exception_handler([ExceptionHandler::class, 'handle']);

    RouteHandler::setErrorHandler([ExceptionHandler::class, 'handle']);
  }

  public function boot(): void {}
}
