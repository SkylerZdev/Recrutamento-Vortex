<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacancyRequest;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class VacancyController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        return $this->listing($request, Vacancy::query(), 'vagas', ['title', 'description', 'contract', 'paused']);
    }

    public function create(): View
    {
        return view('records.form', ['record' => new Vacancy, 'section' => 'vagas']);
    }

    public function store(VacancyRequest $request): RedirectResponse|JsonResponse
    {
        return $this->saved($request, Vacancy::create($request->validated()), 'vagas', 201);
    }

    public function show(Request $request, Vacancy $vacancy): View|JsonResponse
    {
        $related = $vacancy->candidates()->orderBy('id')->paginate(20);
        if ($request->is('api/*')) {
            return response()->json(['data' => $vacancy, 'candidates' => $related]);
        }

        return view('records.show', ['record' => $vacancy, 'section' => 'vagas', 'related' => $related]);
    }

    public function edit(Vacancy $vacancy): View
    {
        return view('records.form', ['record' => $vacancy, 'section' => 'vagas']);
    }

    public function update(VacancyRequest $request, Vacancy $vacancy): RedirectResponse|JsonResponse
    {
        $vacancy->update($request->validated());

        return $this->saved($request, $vacancy, 'vagas');
    }

    public function destroy(Request $request, Vacancy $vacancy): Response
    {
        $vacancy->delete();

        return $this->deleted($request, 'vagas');
    }

    public function bulkDestroy(Request $request): Response
    {
        return $this->deleteSelection($request, Vacancy::query(), 'vagas');
    }
}
