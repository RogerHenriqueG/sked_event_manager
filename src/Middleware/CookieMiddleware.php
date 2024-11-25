<?php
//CookieMiddleware

namespace App\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Service\User\User;

class CookieMiddleware
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
        @session_start();
        $agora = new \Datetime('now', $GLOBALS['TZ']);
        
        if (!isset($_SESSION['Tentativa'])) {
            $_SESSION['Tentativa'] = 0;
        }else{
            $_SESSION['Tentativa'] += 1;
        }



        if (isset($_SESSION['ProximaPermitida'])) {
            
            if ($_SESSION['ProximaPermitida'] > $agora) {
                $_SESSION['Tentativa'] += 1;
                $segundos = $_SESSION['Tentativa'] * 5;
                $response = new Response();
                $_SESSION['ProximaPermitida'] = date_add($agora, new \DateInterval("PT{$segundos}S"));
                $response = new Response();
                return $response->withHeader('Location','/')->withStatus(302)->withHeader('X-LOGIN',"Você deverá aguardar {$segundos} para tentar login novamente.");
            }else{
                $_SESSION['Tentativa'] = 0;
                return $response;
            }
        }

        if ($_SESSION['Tentativa'] > 3) {
            $segundos = $_SESSION['Tentativa'] * 5;
            $_SESSION['ProximaPermitida'] = date_add($agora, new \DateInterval("PT{$segundos}S"));
            $response = new Response();
            return $response->withHeader('Location','/')->withStatus(302)->withHeader('X-LOGIN',"Você deverá aguardar {$segundos} segundos para tentar login novamente.");
        }


        
        return $response;
    }
}