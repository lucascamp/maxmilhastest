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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                return Redirect::back()->withErrors(['Preencha o CPF']);
            }

            $valida_cpf = $this->cpf->validaCPF($input['cpf']);

            if($valida_cpf)
            {
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

                return back()->with('status', 'CPF exlcuído '.$deletedRows->cpf);
            }

            return Redirect::back()->withErrors(['CPF '.$input['cpf'].' Inválido']);

        } catch (Exception $e) {
            return Redirect::back()->withErrors(['Erro na request']);
        }
    }

    /**
     * Pesquisa CPF e retornar ele com o status.
     *
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
                $consulta_cpf = Cpf::where('cpf', '=', $input['cpf'])->firstOrFail();

                if (is_null($consulta_cpf)) 
                {
                    return Redirect::back()->withErrors(['CPF '.$input['cpf'].' não existe']);
                }

                $consulta_cpf->count = ($consulta_cpf->count + 1);
                $consulta_cpf->save();

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
                return Redirect::back()->withErrors(['CPF '.$input['cpf'].' não existe']);       
            }

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
                return Redirect::back()->withErrors(['CPF '.$input['cpf'].' não existe']);       
            }

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

        return view('status', ['listagem' => $listagem]);
    }

}
