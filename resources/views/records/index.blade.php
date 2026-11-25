@extends('layout')
@php
    $vacancies = $section === 'vagas';
    $heading = $vacancies ? 'Vagas' : 'Candidatos';
    $labels = ['id' => 'ID','title' => 'Título','description' => 'Descrição','contract' => 'Contrato','paused' => 'Situação','name' => 'Nome','email' => 'E-mail','created_at' => 'Criação','updated_at' => 'Atualização'];
@endphp
@section('title', $heading)
@section('content')
<div class="heading"><div><p class="eyebrow">Recrutamento</p><h1>{{ $heading }}</h1></div>
<a class="button primary" href="{{ route($section.'.create') }}">{{ $vacancies ? '+ Nova vaga' : '+ Novo candidato' }}</a></div>
<section class="panel"><details @if(request()->except('page','per_page')) open @endif><summary>Filtrar {{ strtolower($heading) }}</summary>
<form method="get" class="filters">
@foreach(['id', ...$fields, 'created_at','updated_at'] as $field)
<label>{{ $labels[$field] }}
@if($field === 'paused' || $field === 'contract')
<select name="{{ $field }}"><option value="">Todos</option>
@foreach($field === 'paused' ? ['0'=>'Aberta','1'=>'Pausada'] : array_column(\App\Enums\Contract::cases(),'value','value') as $value => $label)
<option value="{{ $value }}" @selected((string) request($field) === (string) $value)>{{ $label }}</option>@endforeach</select>
@else<input type="{{ str_ends_with($field,'_at') ? 'date' : ($field === 'id' ? 'number' : 'text') }}" name="{{ $field }}" value="{{ request($field) }}">@endif
</label>@endforeach
<input type="hidden" name="per_page" value="{{ $records->perPage() }}"><button class="primary">Aplicar filtros</button><a href="{{ route($section.'.index') }}">Limpar</a></form></details>
<div class="tools"><strong>{{ $records->total() }} {{ $records->total() === 1 ? 'registro' : 'registros' }}</strong>
<form method="get">@foreach(request()->except('page','per_page') as $key=>$value)@if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach
<label>Por página <input class="small" type="number" name="per_page" min="1" max="100" value="{{ $records->perPage() }}"></label> <button>Exibir</button></form></div>
<form id="bulk" method="post" action="{{ route($section.'.bulk') }}" data-confirm="Excluir os registros selecionados e suas inscrições?">@csrf @method('DELETE')</form>
<div class="scroll"><table><thead><tr><th><input type="checkbox" data-all aria-label="Selecionar todos desta página"></th><th>{{ $vacancies ? 'Vaga' : 'Nome' }}</th><th>{{ $vacancies ? 'Contrato' : 'E-mail' }}</th>@if($vacancies)<th>Situação</th>@endif<th>Ações</th></tr></thead><tbody>
@forelse($records as $record)<tr><td><input type="checkbox" form="bulk" name="ids[]" value="{{ $record->id }}" aria-label="Selecionar registro {{ $record->id }}"></td>
<td><a href="{{ route($section.'.show',$record) }}">{{ $record->title ?? $record->name }}</a></td><td>{{ $record->contract ?? $record->email }}</td>
@if($vacancies)<td>@include('records.status')</td>@endif
<td><div class="actions"><a href="{{ route($section.'.edit',$record) }}">Editar</a><form method="post" action="{{ route($section.'.destroy',$record) }}" data-confirm="Excluir este registro e suas inscrições?">@csrf @method('DELETE')<button class="danger">Excluir</button></form></div></td></tr>
@empty<tr><td colspan="5">Nenhum registro encontrado.</td></tr>@endforelse</tbody></table></div>
<footer><button form="bulk" class="danger" data-bulk>Excluir seleção</button>@include('records.pages',['page'=>$records])</footer></section>
@endsection
