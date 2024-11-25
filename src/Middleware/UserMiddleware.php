<?php
namespace App\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Service\User\User;

class UserMiddleware
{
    /**
     *
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        print_r($_SESSION);exit();

        if(!$_SESSION){
            $response = new Response();
            return $response->withHeader('Location','/')->withStatus(302);
        }

        return $response;
    }
}


