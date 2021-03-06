<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <!-- Organization Name -->
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href=" {{ url('/') }}">USPdev CoPaCo</a>
    
    @auth
    <form class="form-control form-control-dark" style="padding: 0;" method="GET" action="{{ action('EquipamentoController@search') }}">
        <input class="form-control form-control-dark w-100" type="text" name="pesquisar" placeholder="Pesquisar Mac Address" aria-label="Search">
    </form>
    @endauth

    <ul class="navbar-nav px-3">
        
        @auth
        <li class="nav-item text-nowrap">
            <form action="/logout" method="POST">
                {{ csrf_field() }}
                <span class="oi oi-person" title="{{ Auth::user()->name }}"></span>
                <button type="submit" class="btn btn-dark">Sair</button>
            </form>
        </li>
        @else
        <li class="nav-item text-nowrap">
            <a class="btn btn-dark" href="/login">Entrar</a>
        </li>
        @endauth

    </ul>
</nav>
