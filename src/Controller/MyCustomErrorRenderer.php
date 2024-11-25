<?php
namespace App\Controller;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class MyCustomErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
    	$view = file_get_contents(__DIR__ . '/../../views/404.html');
        return $view;
    }
}