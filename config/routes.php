<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\MateriasController;
use App\Controllers\UsersController;
use App\Middlewares\BeforeMiddlewareAuth;



return function ($app) {
    $app->any('[/]', function ($request, $response, $args) {
        $response->getBody()->write("Bienvenido");		// obtiene el body para escribirlo
        return $response;
    }); 

    $app->post('/usuario', UsersController::class . ':add');
    $app->post('/login', UsersController::class . ':login');

/*     $app->group('/user', function (RouteCollectorProxy $group) {
        $group->post('[/]', UsuariosController::class . ':add');
        $group->put('/this', UsuariosController::class . ':updateThis')->add(BeforeMiddlewareAuth::class);
        $group->put('/{id}', UsuariosController::class . ':update')->add(BeforeMiddlewareAuth::class);
        $group->delete('/{id}', UsuariosController::class . ':delete')->add(BeforeMiddlewareAuth::class);
        $group->get('[/]', UsuariosController::class . ':getAll')->add(BeforeMiddlewareAuth::class);
        $group->get('/tipo={tipo}', UsuariosController::class . ':getByTipo')->add(BeforeMiddlewareAuth::class);
        $group->get('/this', UsuariosController::class . ':getThis')->add(BeforeMiddlewareAuth::class);
        $group->get('/{id}', UsuariosController::class . ':get')->add(BeforeMiddlewareAuth::class);
    }); */

    $app->group('/materias', function (RouteCollectorProxy $group) {
        $group->post('[/]', MateriasController::class . ':add');             //(Solo para admin). Recibe materia, cuatrimestre, vacantes y profesor y los guarda en la tabla materias.
        $group->get('/{id}', MateriasController::class . ':get');            //Para alumno muestra los datos de la materia, para profesor y admin muestra los datos de la materia y la lista de inscriptos (email, nombre y legajo).
        $group->put('/{id}/{profesor}', MateriasController::class . ':updateProfesor');  //admin) Asigna un profesor a una materia.
        $group->put('/{id}', MateriasController::class . ':updateInscripcion');         //Solo alumno. Se inscribe a una materia si hay vacantes. Restar una vacante si la inscripción es exitosa.
        $group->get('[/]', MateriasController::class . ':getAll');
    })->add(BeforeMiddlewareAuth::class);



};



/*
success	All went well, and (usually) some data was returned.	status, data	
fail	There was a problem with the data submitted, or some pre-condition of the API call wasn't satisfied	status, data	
error	An error occurred in processing the request, i.e. an exception was thrown	status, message
*/