<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CandidateController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        return $this->listing($request, Candidate::query(), 'candidatos', ['name', 'email']);
    }

    public function create(): View
    {
        return view('records.form', ['record' => new Candidate, 'section' => 'candidatos']);
    }

    public function store(CandidateRequest $request): RedirectResponse|JsonResponse
    {
        return $this->saved($request, Candidate::create($request->validated()), 'candidatos', 201);
    }

    public function show(Request $request, Candidate $candidate): View|JsonResponse
    {
        $related = $candidate->vacancies()->orderBy('id')->paginate(20);
        if ($request->is('api/*')) {
            return response()->json(['data' => $candidate, 'vacancies' => $related]);
        }

        return view('records.show', ['record' => $candidate, 'section' => 'candidatos', 'related' => $related]);
    }

    public function edit(Candidate $candidate): View
    {
        return view('records.form', ['record' => $candidate, 'section' => 'candidatos']);
    }

    public function update(CandidateRequest $request, Candidate $candidate): RedirectResponse|JsonResponse
    {
        $candidate->update($request->validated());

        return $this->saved($request, $candidate, 'candidatos');
    }

    public function destroy(Request $request, Candidate $candidate): Response
    {
        $candidate->delete();

        return $this->deleted($request, 'candidatos');
    }

    public function bulkDestroy(Request $request): Response
    {
        return $this->deleteSelection($request, Candidate::query(), 'candidatos');
    }
}
