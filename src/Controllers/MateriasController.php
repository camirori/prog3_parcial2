<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Materia;
use App\Models\User;
use App\Models\Inscripto;
use App\Utils\AuthJWT;
use App\Models\Tipo;

class MateriasController {
    public function add(Request $request, Response $response, $args){
        $id = ($request->getHeader('id'))[0];
       $tipoUser = User::where('id',$id)->value('tipo_id');

        if($tipoUser == '3'){
            $params = $request->getParsedBody();
            if(isset($params['materia']) && isset($params['cuatrimestre']) && isset($params['vacantes']) && isset($params['profesor'])){
                $model = new Materia;
                $model->materia = $params['materia'];
                $model->cuatrimestre = $params['cuatrimestre'];
                $model->vacantes = $params['vacantes'];
                $model->profesor_id = User::where('nombre',$params['profesor'])->orWhere('id',$params['profesor'])->value('id');

                $data= $model->save();
                $status = $data ? 'success':'fail';
            }else{
                $data ='Faltan datos';    
                $status = 'fail';
            }            
        }else{
            $data ='Recurso disponible solo para administradores';    
            $status = 'fail';
        }  

        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function updateProfesor(Request $request, Response $response, $args){
        $id = ($request->getHeader('id'))[0];
       $tipoUser = User::where('id',$id)->value('tipo_id');

        if($tipoUser == '3'){
            if(User::where('id',$args['profesor'])->value('tipo_id')==2){
                $model = Materia::find($args['id']);   
                if($model){
                    $model->profesor_id = $args['profesor'];

                    $data= $model->save();
                    $status = $data ? 'success':'fail';                    
                }
                else{
                    $data ='Materia no encontrada';    
                    $status = 'fail';
                }    
            }else{
                $data ='El id indicado no corresponde a un profesor';    
                $status = 'fail';
            }            
        }else{
            $data ='Recurso disponible solo para administradores';    
            $status = 'fail';
        }  

        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function updateInscripcion(Request $request, Response $response, $args){
        $id = ($request->getHeader('id'))[0];
       $tipoUser = User::where('id',$id)->value('tipo_id');

        if($tipoUser == '1'){
            $model = Materia::find($args['id']);   
            if($model){
                if($model->vacantes>0){
                    $inscripcion = new Inscripto;
                    $inscripcion->alumno_id = $id;
                    $inscripcion->materia_id = $args['id'];
                    //$inscripcion->date = time();
                    $result= $inscripcion->save();

                    if($result){
                        $model->vacantes = (--$model->vacantes);
                        $data= $model->save();
                        $status = $data ? 'success':'error';                                
                    }else{
                        $data ='Error en la inscripcion';    
                        $status = 'fail';
                    }  
                }else{
                    $data ='Materia sin vacantes';    
                    $status = 'fail';
                }  
            }else{
                $data ='Materia no encontrada';    
                $status = 'fail';
            }  
        }else{
            $data ='Recurso disponible solo para alumnos';    
            $status = 'fail';
        }  
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function update(Request $request, Response $response, $args){
        $params = $request->getParsedBody();  
        if($params){
            $model = Materia::find($args['id']);   

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
        $data = Materia::destroy($args['id']);
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
        
        $data = Materia::select('materia','cuatrimestre','vacantes')->get();//->addSelect(['nombre profesor'=>User::select('nombre')->whereColumn('id','materias.profesor_id')->value('nombre')])->
            //addSelect(['email profesor'=>User::whereColumn('id','materias.profesor_id')->value('email')])
            //->addSelect(['Cantidad inscriptos'=>Inscripto::whereColumn('materia_id','materias.id')->count()])->get();

        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function get(Request $request, Response $response, $args){
        $id = ($request->getHeader('id'))[0];
        $tipoUser = User::where('id',$id)->value('tipo_id');

        if($tipoUser == '3' || $tipoUser == '2'){   //admin o profesor
            $data = array();
            $data['datos']= Materia::find($args['id']);
            if($data['datos']){
                $idInscriptos = Inscripto::where('materia_id',$args['id'])->get();
                if($idInscriptos->isEmpty()){
                    $data['inscriptos']= 'No hay inscriptos';
                }else{
                    $data['inscriptos'] = array();
                    foreach($idInscriptos as $idInscripto){
                        $info=User::select('email','nombre','legajo')->where('id',$idInscripto->alumno_id)->get();
                        array_push($data['inscriptos'],$info);
                    }                
                }                
            }else{
                $data['datos']='No se encontro la materia';
            }
        }elseif($tipoUser == '1'){  //alumno
            $data = Materia::find($args['id'])??'No se encontro la materia';
        }
        
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
        $data = Materia::where('localidad_id',$args['localidad'])->get();
        $status = $data ? 'success':'fail';
        $rta = array('status'=> $status, 'data'=> $data);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }


}
