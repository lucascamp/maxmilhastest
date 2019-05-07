@extends('layout')

@section('content')
<style>
  .uper {
    margin-top: 40px;
  }
</style>

<div class="card uper">
  <div class="card-header">
    CPF
  </div>
  <div class="card-body">
    
    @if(session('status'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('status') }}</li>
            </ul>
        </div><br />
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
      </div><br />
    @endif

        <form method="post" action="{{ route('find') }}">
          <div class="form-group offset-lg-3 col-lg-6">
              @csrf
              <label for="name">Buscar CPF:</label>
              <input type="text" class="form-control" name="cpf"/><br>
              <button type="submit" class="btn btn-primary">Buscar CPF</button>
          </div>
        </form>
        <br><br>

        <form method="post" action="{{ route('store') }}">
            <div class="form-group offset-lg-3 col-lg-6">
                @csrf
                <label for="name">Adicionar CPF:</label>
                <input type="text" class="form-control" name="cpf"/><br>
                <button type="submit" class="btn btn-primary">Adicionar CPF</button>
            </div>
        </form>
        <br><br>

        <form method="post" action="{{ route('block') }}">
            <div class="form-group offset-lg-3 col-lg-6">
                @csrf
                <label for="name">Adicionar ao blacklist:</label>
                <input type="text" class="form-control" name="cpf"/><br>
                <button type="submit" class="btn btn-primary">Adicionar ao blacklist</button>
            </div>
        </form>
        <br><br>

        <form method="post" action="{{ route('unblock') }}">
          <div class="form-group offset-lg-3 col-lg-6">
              @csrf
              <label for="name">Remover do blacklist:</label>
              <input type="text" class="form-control" name="cpf"/><br>
              <button type="submit" class="btn btn-primary">Remover do blacklist</button>
          </div>
      </form>
      <br><br>

        <form method="post" action="{{ route('remove') }}">
            <div class="form-group offset-lg-3 col-lg-6">
                @csrf
                <label for="name">Deletar CPF:</label>
                <input type="text" class="form-control" name="cpf"/><br>
                <button type="submit" class="btn btn-primary">Deletar CPF</button>
            </div>
        </form>

  </div>
</div>

@endsection