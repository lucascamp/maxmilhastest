@extends('layout')

@section('content')
<style>
  .uper {
    margin-top: 40px;
  }
</style>

<div class="card uper">
  <div class="card-header">
    Status da aplicação
  </div>
  <div class="card-body">
       
          <div class="offset-lg-1 col-lg-10">
            <table class="table">
              <tr>
                <td>CPFs na blacklist</td>
                <td>Uptime da aplicação</td>
                <td>Consultas realizadas desde o último restart</td>
              </tr>
              <tr>
                  <td><h3>{{ $listagem['blacklist'] }}</h3></td>
                  <td><h3>{{ $listagem['uptime'] }}</h3></td>
                  <td><h3>{{ $listagem['consultas_realizadas'] }}</h3></td>
                </tr>
            </table>  
          </div>

          <div class="form-group offset-lg-1 col-lg-10">
              <table class="table">
                  
                  <tr>
                    <td>CPF</td>
                    <td>Total consultas</td>
                    <td>Bloqueado</td>
                  </tr>

                  @foreach($listagem['cpfs'] as $k => $v)
                    <tr>
                        <td>{{ $v['cpf'] }}</td>
                        <td>{{ $v['count'] }}</td>
                        <td>{{ $v['blocked'] }}</td>
                    </tr>
                  @endforeach

                </table>
          </div>
   
        <br><br>
  </div>
</div>

@endsection