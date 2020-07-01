<?php
namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Utils\AuthJWT;
use Exception;
use Slim\Psr7\Message;

class BeforeMiddlewareAuth
{
    /**
     * Validar JWT
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try {
            $headerJwt = $request->getHeader('Authorization');
            if($headerJwt){
                if ($jwtDecoded = AuthJWT::autentificar($headerJwt[0])) {
                $response = $handler->handle($request->withHeader('id',$jwtDecoded->data->id)/*->withHeader('tipo',$jwtDecoded->data->tipo)*/); //se ve a poder acceder al header en la ruta, tmb se puede agregar en response
                    $existingContent = (string) $response->getBody();   

                    $newResponse = new Response();
                    $newResponse->getBody()->write($existingContent);
                    return $newResponse;
                } else {
                    $response = new Response();
                    $rta = array('status'=> 'fail', 'data'=> 'Error de sesion: Usuario no encontrado');
                    $response->getBody()->write(json_encode($rta));
                    return $response->withStatus(401);
                }               
            }else {
                $response = new Response();
                $rta = array('status'=> 'fail', 'data'=> 'Error de sesion: Debe iniciar sesion para acceder a este recurso');
                $response->getBody()->write(json_encode($rta));
                return $response->withStatus(401);
            }   
        } catch (Exception $th) {
            $response = new Response();
            $rta = array('status'=> 'fail', 'data'=> 'Error de sesion: '.$th->getMessage());
            $response->getBody()->write(json_encode($rta));
                return $response->withStatus(401);
        }
    }
}