<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function listing(Request $request, Builder $query, string $section, array $fields): View|JsonResponse
    {
        $rules = [
            'id' => ['nullable', 'integer', 'min:1'],
            'created_at' => ['nullable', 'date_format:Y-m-d'],
            'updated_at' => ['nullable', 'date_format:Y-m-d'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
        foreach ($fields as $field) {
            $rules[$field] = $field === 'paused' ? ['nullable', 'boolean'] : ['nullable', 'string', 'max:255'];
        }
        $filters = $request->validate($rules);
        foreach ($filters as $field => $value) {
            if ($value === null || in_array($field, ['page', 'per_page'], true)) {
                continue;
            }
            if (in_array($field, ['created_at', 'updated_at'], true)) {
                $query->whereDate($field, $value);
            } elseif (in_array($field, ['id', 'contract', 'paused'], true)) {
                $query->where($field, $value);
            } else {
                $query->whereLike($field, '%'.$value.'%');
            }
        }
        $records = $query->orderByDesc('id')->paginate($filters['per_page'] ?? 20)->withQueryString();

        return $request->is('api/*') ? response()->json($records) : view('records.index', compact('section', 'fields', 'records'));
    }

    protected function saved(Request $request, Model $record, string $section, int $status = 200): RedirectResponse|JsonResponse
    {
        return $request->is('api/*') ? response()->json($record, $status) : redirect()->route($section.'.show', $record)->with('status', 'Cadastro salvo.');
    }

    protected function deleteSelection(Request $request, Builder $query, string $section): Response
    {
        $data = $request->validate(['ids' => ['required', 'array', 'list', 'min:1', 'max:100'], 'ids.*' => ['required', 'integer', 'min:1', 'distinct']]);
        $query->whereKey($data['ids'])->delete();

        return $this->deleted($request, $section);
    }

    protected function deleted(Request $request, string $section): Response
    {
        return $request->is('api/*') ? response()->noContent() : redirect()->route($section.'.index')->with('status', 'Exclusão concluída.');
    }
}
