<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Cpf;
use Validator;
use Session;


class CpfController extends Controller
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
     * Exibe view com todas as ações.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Pesquisa CPF e retornar ele com o status.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
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

            //valida CPF
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $consulta_cpf = Cpf::where('cpf', '=', $input['cpf'])->first();
                
                if (!isset($consulta_cpf)) 
                {
                    return Redirect::back()->withErrors(['CPF '.$input['cpf'].' não cadastrado']);
                }

                $consulta_cpf->count = ($consulta_cpf->count + 1);
                $consulta_cpf->save();

                //retorna mensagem pelo status bloqueado
                if($consulta_cpf->blocked == 1)
                    return back()->with('status', 'CPF encontrado '.$consulta_cpf->cpf.', Status : BLOCKED');
                else
                    return back()->with('status', 'CPF encontrado '.$consulta_cpf->cpf.', Status : FREE');
            }

            return Redirect::back()->withErrors(['CPF '.$input['cpf'].' Inválido']);

        } catch (Exception $e) {
            return Redirect::back()->withErrors(['Erro na request']);
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
                return Redirect::back()->withErrors(['Preencha o CPF']);
            }

            //validação customizada de cpf
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                //cria ou atualiza cpf
                $cpf_new = Cpf::firstOrNew(['cpf' => $input['cpf']]);
                $cpf_new->save();
                
                return back()->with('status', 'CPF '.$cpf_new->cpf.' inserido com sucesso!'); 
            }

            return Redirect::back()->withErrors(['CPF Inválido']);

        } catch (Exception $e) {
            return Redirect::back()->withErrors(['Erro ao preencher o cpf']);
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
                return Redirect::back()->withErrors(['CPF '.$input['cpf'].' não existe']);       
            }

            //valida CPF
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $cpf_update =  Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();   
                $cpf_update->blocked = 1;
                $cpf_update->save();

                return back()->with('status', 'CPF '.$cpf_update->cpf.' bloqueado com sucesso');
            }

            return Redirect::back()->withErrors(['CPF '.$input['cpf'].' Inválido']);

        } catch (Exception $e) {
            return Redirect::back()->withErrors(['Erro na request']); 
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
                return Redirect::back()->withErrors(['CPF '.$input['cpf'].' não existe']);       
            }

            //valida CPF
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $cpf_update =  Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();   
                $cpf_update->blocked = 0;
                $cpf_update->save();

                return back()->with('status', 'CPF '.$cpf_update->cpf.' desbloqueado com sucesso');
            }

            return Redirect::back()->withErrors(['CPF '.$input['cpf'].' Inválido']);

        } catch (Exception $e) {
            return Redirect::back()->withErrors(['Erro na request']); 
        }
    }

    /**
     * Remove cpf do banco de acordo com input do formulario.
     * @param  array  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        try{
            $input = $request->all();

            //remove caracteres especiais e valida
            $input['cpf'] = preg_replace('/\D/','',$input['cpf']);
            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
                $deletedRows = Cpf::where('cpf', '=',  $input['cpf'])->delete();

                return back()->with('status', 'CPF excluído '.$input['cpf']);
            }

            return Redirect::back()->withErrors(['CPF '.$input['cpf'].' Inválido']);

        } catch (Exception $e) {
            return Redirect::back()->withErrors(['Erro na request']);
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

        return view('status', ['listagem' => $listagem]);
    }
}
