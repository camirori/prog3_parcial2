<?php
namespace App\Middlewares;

//use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AfterMiddlewareUppercase{
    /**
     * Agrega header Content-type
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        $newBody= $response->getBody();
        $newBody = json_decode($newBody);
        if(!is_scalar($newBody->data)){                           //no tiene turnos
            foreach($newBody->data as $mascota => $arrTurnos){
                if(is_array($arrTurnos)){
                    foreach($arrTurnos as $key => $turno){
                        if(date_timestamp_get(date_create_from_format("Y-m-d",$turno->fecha))>time()){
                            $fecha = $turno->fecha;
                            $hora = $turno->hora;
                            unset($newBody->data->$mascota[$key]->fecha);
                            unset($newBody->data->$mascota[$key]->hora);
                            $newBody->data->$mascota[$key]->FECHA=$fecha;
                            $newBody->data->$mascota[$key]->HORA=$hora;
                        }
                    }                
                }
            }            
        }

        $response = new Response();
        $response->getBody()->write(json_encode($newBody));
        return $response;
    }
}