@extends('layout')
@section('title', 'Entrar')
@section('content')
<section class="panel login"><p class="eyebrow">Área de recrutamento</p><h1>Entrar</h1>
<form method="post" class="stack" action="{{ route('login') }}">@csrf
<label>E-mail<input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus></label>
<label>Senha<input type="password" name="password" required autocomplete="current-password"></label>
<button class="primary">Entrar</button></form></section>
@endsection
