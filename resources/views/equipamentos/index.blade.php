@extends('adminlte::page')

@section('content_header')
    <h1>Cadastrar Equipamento</h1>
@stop

@section('content')
    @include('messages.flash')
    @include('messages.errors')
<p>
    <a href="{{ route('equipamentos.create') }}" class="btn btn-success">
        Adicionar Equipamento
    </a>
</p>

<form method="post" action="/equipamentos/search">
    {{ csrf_field() }}
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Busca por MacAddress..." name="macaddress">
        <span class="input-group-btn">
            <button type="submit" class="btn btn-success"> Buscar </button>
        </span>
    </div><!-- /input-group -->
</form>


<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>MAC Address</th>
                <th>Identificação/patrimônio</th>
                <th>IP</th>
                <th>Rede</th>
                <th>Data de Vencimento</th>
                <th colspan="2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipamentos as $equipamento)
            <tr>
                <td><a href="/equipamentos/{{ $equipamento->id }}"> {{ $equipamento->macaddress }}</a></td>
                <td>{{ $equipamento->descricaosempatrimonio or $equipamento->patrimonio }}</td>
                <td>{{ $equipamento->ip or '' }}</td>
                <td><i>{{ $equipamento->rede->nome or '' }}</i>
                    @isset ($equipamento->rede->iprede)
                        <a href="/redes/{{$equipamento->rede->id}}">{{ $equipamento->rede->iprede or '' }}/{{ $equipamento->rede->cidr or '' }}</a>
                    @endisset
                </td>
                <td>{{ \Carbon\Carbon::CreateFromFormat('Y-m-d', $equipamento->vencimento)->format('d/m/Y') }}</td>
                <td>
                    <a href="{{action('EquipamentoController@edit', $equipamento->id)}}" class="btn btn-warning">Editar</a>
                </td>
                <td>
                    <form action="{{action('EquipamentoController@destroy', $equipamento->id)}}" method="post">
                      {{csrf_field()}} {{ method_field('delete') }}
                      <button class="delete-item btn btn-danger" type="submit">Deletar</button>
                  </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop
