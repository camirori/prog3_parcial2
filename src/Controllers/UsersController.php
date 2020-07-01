<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Utils\AuthJWT;
use App\Models\Tipo;

class UsersController {
    public function login(Request $request, Response $response, $args){
        $params = $request->getParsedBody();
        if(isset($params['email']) && isset($params['clave'])){
            $user = User::where('email',$params['email'])->get();
            if($user->isNotEmpty()){
                $user= $user[0];
                if($user->clave == $params['clave']){
                    $data= AuthJWT::crearJWT(AuthJWT::generarPayload(array("id"=>$user->id, "tipo"=>$user->tipo_id)));
                    $status = 'success';                    
                }else{
                    $data ='Error de sesion: Contraseña incorrecta';    
                    $status = 'fail';
                }
            }else{
                $data ='Error de sesion: Usuario no encontrado';    
                $status = 'fail';
            }
        }else{
            $data ='Error de sesion: Debe indicar las credenciales para iniciar sesión';    
            $status = 'fail';
        }
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    //checkLoggedIn middleware



    public function add(Request $request, Response $response, $args){   //registro
        $params = $request->getParsedBody();
        if(isset($params['email']) && isset($params['nombre']) && isset($params['tipo']) && isset($params['clave']) && isset($params['legajo'])){
            if($params['legajo']<=2000 && $params['legajo']>=1000){
                if(User::where('legajo',$params['legajo'])->get()->isEmpty()){
                    if(User::where('email',$params['email'])->get()->isEmpty()){
                        $model = new User;
                        $model->email = $params['email'];
                        $model->nombre = $params['nombre'];
                        $model->tipo_id = Tipo::where('tipo',$params['tipo'])->value('id');
                        $model->clave = $params['clave'];
                        $model->legajo = $params['legajo'];

                        $data= $model->save();
                        $status = $data ? 'success':'fail';
                    }else{
                        $data ="El email de usuario ya existe";
                        $status = 'fail';
                    }                         
                }else{
                    $data ='El legajo ya existe';    
                    $status = 'fail';
                }
            }else{
                $data ='El legajo debe ser entre 2000 y 1000';    
                $status = 'fail';
            }
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
            $model = User::find($args['id']);   

            if(isset($params['email']))
                $model->email = $params['email'];
            if(isset($params['tipo']))
                $model->tipo = $params['tipo'];
            if(isset($params['password']))
                $model->password = $params['password'];

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
        $data = User::destroy($args['id']);
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
        $data = User::all();
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function get(Request $request, Response $response, $args){
        $data = User::find($args['id']);
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

    public function getByTipo(Request $request, Response $response, $args){
        $data = User::where('tipo',$args['tipo'])->get();
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function getByThisTipo(Request $request, Response $response, $args){
        $params = $request->getHeader('tipo');
        $args['tipo']=$params[0];
        return $this->getByTipo($request,$response,$args);
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