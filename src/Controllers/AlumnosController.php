<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Alumno;

class AlumnosController {
    public function add(Request $request, Response $response, $args){
        $params = $request->getParsedBody();
        if(isset($params['nombre']) && isset($params['legajo']) && isset($params['localidad_id'])){
            $model = new Alumno;
            $model->nombre = $params['nombre'];
            $model->legajo = $params['legajo'];
            $model->localidad_id = $params['localidad_id'];

            $data= $model->save();
            $status = $data ? 'success':'fail';
        }else{
            $data ='Faltan datos';    
            $status = 'fail';
        }
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function update(Request $request, Response $response, $args){
        $params = $request->getParsedBody();  
        if($params){
            $model = Alumno::find($args['id']);   

            if(isset($params['nombre']))
            $model->nombre = $params['nombre'];
            if(isset($params['legajo']))
                $model->legajo = $params['legajo'];
            if(isset($params['localidad_id']))
                $model->localidad_id = $params['localidad_id'];

            $data= $model->save();
            $status = $data ? 'success':'error';            
        }else{
            $data ='No se indicaron datos a actualizar';    
            $status = 'fail';
        }
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function updateThis(Request $request, Response $response, $args){
        $params = $request->getHeader('id');
        $args['id']=$params[0];
        return $this->update($request,$response,$args);
    }

    public function delete(Request $request, Response $response, $args){
        $data = Alumno::destroy($args['id']);
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data.' registro(s) borrado(s)');
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function deleteThis(Request $request, Response $response, $args){
        $params = $request->getHeader('id');
        $args['id']=$params[0];
        return $this->delete($request,$response,$args);
    }

    public function getAll(Request $request, Response $response, $args){
        $data = Alumno::all();
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function get(Request $request, Response $response, $args){
        $data = Alumno::find($args['id']);
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function getThis(Request $request, Response $response, $args){
        $params = $request->getHeader('id');
        $args['id']=$params[0];
        return $this->get($request,$response,$args);
    }

    public function getByLocalidad(Request $request, Response $response, $args){
        $data = Alumno::where('localidad_id',$args['localidad'])->get();
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }


}


/*
Insert/Add
Update(id)/updateThis
Delete(id)/deleteThis
DeleteBy...

SelectAll/GetAll
Select(id)/Get/GetThis
SelectBy/GetBy...
*/