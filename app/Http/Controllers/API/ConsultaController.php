<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Cpf;
use Validator;
use Session;

class ConsultaController extends BaseController
{
    protected $cpf;
    
    /**
     * Inicializa construtor
     */ 
    public function __construct(Cpf $cpf)
    {
        session_start();
        if(!isset($_SESSION['start_date']))
        {
            $_SESSION['start_date'] = date('Y-m-d H:i:s');
        }

        $this->cpf = $cpf;
    }

   /**
     * Pesquisa CPF e retornar ele com o status.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cpf(Request $request)
    {
        try {

            //contador de consultas realizadas
            if(!isset($_SESSION['consultas_counter']))
            {
                $_SESSION['consultas_counter'] = 0;
            }

            if(count($_GET) > 0)
            {
                $_SESSION['consultas_counter']++;
            }

            $input = $request->all();
            
            //remove caracteres especiais
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);
            
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                //valida CPF
                $consulta_cpf = Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();

                if (is_null($consulta_cpf)) 
                {
                    return $this->sendError('cpf não encontrado.')->setStatusCode(404);
                }

                $consulta_cpf->count = ($consulta_cpf->count + 1);
                $consulta_cpf->save();

                
                return $this->sendResponse($consulta_cpf->toArray(), 'consulta_cpf retrieved successfully.')->setStatusCode(200);
            }

            return $this->sendError('cpf invalido.')->setStatusCode(404);


        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage)->setStatusCode(400);; 
        }
    }

    /**
     * Salva novos cpfs no banco
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            //remove caracteres especiais
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            //validação requerida
            $validator = Validator::make($input, [
                'cpf' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors())->setStatusCode(400);       
            }

            //validação customizada de cpf
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                //cria ou atualiza cpf
                $cpf_new = Cpf::firstOrNew($input);
                $cpf_new->save();
            
                return $this->sendResponse($cpf_new->toArray(), 'Cpf criado com sucesso.')->setStatusCode(200);
            }

            return $this->sendError('cpf invalido.')->setStatusCode(404);

        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage)->setStatusCode(400);; 
        }
    }

    /**
     * Bloqueia CPF
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function block(Request $request)
    {
        try{

            $input = $request->all();

            //remove caracteres especiais
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            $validator = Validator::make($input, [
                'cpf' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors())->setStatusCode(400);       
            }

            //valida CPF
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $cpf_update =  Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();   
                $cpf_update->blocked = 1;
                $cpf_update->save();
            
                return $this->sendResponse($cpf_update->toArray(), 'Cpf bloqueado com sucesso.')->setStatusCode(200);
            }

            return $this->sendError('cpf invalido.')->setStatusCode(404);

        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage)->setStatusCode(400);; 
        }
    }

     /**
     * Debloqueia CPF
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unblock(Request $request)
    {
        try{

            $input = $request->all();

            //remove caracteres especiais
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            $validator = Validator::make($input, [
                'cpf' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors())->setStatusCode(400);       
            }

            //valida CPF
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $cpf_update =  Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();   
                $cpf_update->blocked = 0;
                $cpf_update->save();
            
                return $this->sendResponse($cpf_update->toArray(), 'Cpf desbloqueado com sucesso.')->setStatusCode(200);
            }

            return $this->sendError('cpf invalido.')->setStatusCode(404);

        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage)->setStatusCode(400);; 
        }
    }

    /**
     * Remove cpf do banco de acordo com input do formulario.
     * @param  array  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $input = $request->all();

            //remove caracteres especiais e valida
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $deletedRows = Cpf::where('cpf', '=',  $input['cpf'])->delete();

                return $this->sendResponse($deletedRows, 'Cpf deletado.')->setStatusCode(200);
            }

            return $this->sendError('cpf invalido.')->setStatusCode(404);

        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage)->setStatusCode(400);; 
        }
    }

   /**
     * retorna dados referentes ao app
     * @return \Illuminate\Http\Response
     */
    public function status()
    {
        //busca todos os cpfs cadastrados
        $listagem['cpfs'] = Cpf::all();

        //busca total de cpfs no blacklist
        $listagem['blacklist'] =  Cpf::where('blocked', '=', 1)->count();

        if(!isset($_SESSION['consultas_counter']))
        {
            $_SESSION['consultas_counter'] = 0;
        }
        
        //busca consultas ja realizadas
        $listagem['consultas_realizadas'] = $_SESSION['consultas_counter'];

        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $_SESSION['start_date']);
        $from = \Carbon\Carbon::now();

        //calcula uptime do servidor
        $listagem['uptime'] = $to->diffInMinutes($from).' minutos';

        return $this->sendResponse($listagem, 'Status serviço')->setStatusCode(200);
    }
}