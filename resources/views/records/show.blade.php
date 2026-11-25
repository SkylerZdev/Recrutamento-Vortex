@extends('layout')
@php($vacancies = $section === 'vagas')
@section('title',$record->title ?? $record->name)
@section('content')
<a href="{{ route($section.'.index') }}">← {{ ucfirst($section) }}</a>
<div class="heading"><h1>{{ $record->title ?? $record->name }}</h1><a class="button" href="{{ route($section.'.edit',$record) }}">Editar</a></div>
<section class="panel info">
@if($vacancies)@include('records.status') <strong>{{ $record->contract }}</strong><p class="description">{{ $record->description }}</p>
@else<p>{{ $record->email }}</p>@endif
<small>Registro #{{ $record->id }} · Criado em {{ $record->created_at->format('d/m/Y') }} · Atualizado em {{ $record->updated_at->format('d/m/Y') }}</small></section>
@if($vacancies && $record->acceptsApplications())
<section class="panel info"><h2>Inscrever candidato</h2><form method="post" class="actions" action="{{ route('inscricoes.store',$record) }}">@csrf
<label>E-mail do candidato cadastrado<input type="email" required name="email" value="{{ old('email') }}"></label><button class="primary">Inscrever</button></form></section>@endif
<section class="panel info"><h2>{{ $vacancies ? 'Candidatos inscritos' : 'Vagas de inscrição' }} ({{ $related->total() }})</h2>
@forelse($related as $item)<p><a href="{{ route(($vacancies ? 'candidatos' : 'vagas').'.show',$item) }}">{{ $item->name ?? $item->title }}</a></p>@empty<p>Nenhuma inscrição.</p>@endforelse
@if($related->hasPages())@include('records.pages',['page'=>$related])@endif</section>
@endsection
