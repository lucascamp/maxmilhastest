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
     * Instantiate a new UserController instance.
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
     * short url in bitly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cpf(Request $request)
    {
        try {

            $input = $request->all();
            
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {

                $consulta_cpf = Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();

                if (is_null($consulta_cpf)) 
                {
                    return $this->sendError('cpf não encontrado.')->setStatusCode(404);
                }

                $consulta_cpf->count = ($consulta_cpf->count + 1);
                $consulta_cpf->save();

                if(!isset($_SESSION['consultas_counter']))
                {
                    $_SESSION['consultas_counter'] = 0;
                }

                if(count($_GET) > 0)
                {
                    $_SESSION['consultas_counter']++;
                }
                
                return $this->sendResponse($consulta_cpf->toArray(), 'consulta_cpf retrieved successfully.')->setStatusCode(200);
            }

            return $this->sendError('cpf invalido.')->setStatusCode(404);


        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage)->setStatusCode(400);; 
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            $validator = Validator::make($input, [
                'cpf' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors())->setStatusCode(400);       
            }

            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function block(Request $request)
    {
        try{

            $input = $request->all();

            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            $validator = Validator::make($input, [
                'cpf' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors())->setStatusCode(400);       
            }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unblock(Request $request)
    {
        try{

            $input = $request->all();

            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

            $validator = Validator::make($input, [
                'cpf' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors())->setStatusCode(400);       
            }

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $input = $request->all();

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
     * B
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status()
    {
        $listagem['cpfs'] = Cpf::all();

        $listagem['blacklist'] =  Cpf::where('blocked', '=', 1)->count();

        if(!isset($_SESSION['consultas_counter']))
        {
            $_SESSION['consultas_counter'] = 0;
        }
        
        $listagem['consultas_realizadas'] = $_SESSION['consultas_counter'];
        //Cpf::sum('count');

        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $_SESSION['start_date']);
        $from = \Carbon\Carbon::now();

        $listagem['uptime'] = $to->diffInMinutes($from).' minutos';

        return $this->sendResponse($listagem, 'Status serviço')->setStatusCode(200);
    }
}