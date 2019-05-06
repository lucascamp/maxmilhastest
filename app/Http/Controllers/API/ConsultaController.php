<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Cpf;
use Validator;
use Session;

class ConsultaController extends BaseController
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        session_start();
        if(!isset($_SESSION['start_date']))
        {
            $_SESSION['start_date'] = date('Y-m-d H:i:s');
        }
    }

    /**
     * short url in bitly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cpf(Request $request)
    {
        $input = $request->all();
        
        $cpf = preg_replace('/\D/','',$input['cpf']);

        $consulta_cpf = Cpf::where('cpf', '=', $cpf)->firstOrFail();

        try {

            if (is_null($consulta_cpf)) 
            {
                return $this->sendError('consulta_cpf not found.');
            }

            return $this->sendResponse($consulta_cpf->toArray(), 'consulta_cpf retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendResponse($input['cpf'], $e->getMessage); 
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
        $input = $request->all();

        $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

        $validator = Validator::make($input, [
            'cpf' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $cpf = Cpf::firstOrNew($input);
        //$cpf->count = ($cpf->count + 1);
        $cpf->save();
        
        if(!isset($_SESSION['postCounter']))
        {
            $_SESSION['postCounter'] = 0;
        }

        if(count($_POST) > 0)
        {
            $_SESSION['postCounter']++;
        }

        return $this->sendResponse($cpf->toArray(), 'Cpf criado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $input = $request->all();

        $input['cpf'] = preg_replace('/\D/','',$input['cpf']);

        $deletedRows = Cpf::where('cpf', '=',  $input['cpf'])->delete();

        return $this->sendResponse($deletedRows, 'Cpf deletado.');
    }

    /**
     * B
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status()
    {
        $listagem['cpf'] = Cpf::all();

        $listagem['blacklist'] =  Cpf::where('blocked', '=', 1)->count();

        if(!isset($_SESSION['postCounter']))
        {
            $_SESSION['postCounter'] = 0;
        }
        
        $listagem['consultas_realizadas'] = $_SESSION['postCounter'];
        //Cpf::sum('count');

        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $_SESSION['start_date']);
        $from = \Carbon\Carbon::now();

        $listagem['uptime'] = $to->diffInMinutes($from).' minutos';

        return $this->sendResponse($listagem, 'Status serviço');
    }
}