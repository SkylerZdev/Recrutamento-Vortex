<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Vortex</title>
    <link rel="stylesheet" href="{{ asset('recrutamento.css') }}"><script defer src="{{ asset('recrutamento.js') }}"></script>
</head>
<body>
<header class="bar"><div class="shell nav"><a class="brand" href="{{ url('/') }}"><span aria-hidden="true">V</span> Vortex</a>
@auth
<nav aria-label="Principal">@foreach(['vagas' => 'Vagas', 'candidatos' => 'Candidatos'] as $key => $label)
<a href="{{ route($key.'.index') }}" @if(request()->routeIs($key.'.*')) aria-current="page" @endif>{{ $label }}</a>
@endforeach</nav>
<form class="end" method="post" action="{{ route('logout') }}">@csrf<button class="quiet">Sair</button></form>
@endauth</div></header>
<main class="shell">@if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="error" role="alert"><strong>Confira os dados informados.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</main>
</body></html>
