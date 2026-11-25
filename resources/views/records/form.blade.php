@extends('layout')
@php
    $vacancies = $section === 'vagas';
    $heading = $record->exists ? ($vacancies ? 'Editar vaga' : 'Editar candidato') : ($vacancies ? 'Nova vaga' : 'Novo candidato');
@endphp
@section('title',$heading)
@section('content')
<a href="{{ route($section.'.index') }}">← {{ ucfirst($section) }}</a><h1>{{ $heading }}</h1>
<form class="panel stack form" method="post" action="{{ $record->exists ? route($section.'.update',$record) : route($section.'.store') }}">@csrf
@if($record->exists) @method('PUT') @endif
@if($vacancies)
<label>Título<input name="title" required maxlength="255" value="{{ old('title',$record->title) }}"></label>
<label>Descrição<textarea name="description" required maxlength="10000" rows="6">{{ old('description',$record->description) }}</textarea></label>
<label>Contrato<select name="contract" required><option value="">Selecione</option>@foreach(\App\Enums\Contract::cases() as $contract)<option @selected(old('contract',$record->contract)===$contract->value)>{{ $contract->value }}</option>@endforeach</select></label>
<label>Situação<select name="paused"><option value="0">Aberta</option><option value="1" @selected(old('paused',$record->paused))>Pausada</option></select></label>
@else
<label>Nome<input name="name" required maxlength="255" value="{{ old('name',$record->name) }}"></label>
<label>E-mail<input name="email" type="email" required maxlength="255" value="{{ old('email',$record->email) }}"></label>
@endif<div class="actions"><button class="primary">Salvar</button><a href="{{ route($section.'.index') }}">Cancelar</a></div></form>
@endsection
