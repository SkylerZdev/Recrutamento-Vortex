<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        return $this->saveCandidate($request, new Candidate);
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
        return $this->saveCandidate($request, $candidate);
    }

    private function saveCandidate(CandidateRequest $request, Candidate $candidate): RedirectResponse|JsonResponse
    {
        try {
            $candidate->fill($request->validated())->save();
        } catch (UniqueConstraintViolationException $exception) {
            if (! Candidate::where('email', $candidate->email)->exists()) {
                throw $exception;
            }
            throw ValidationException::withMessages(['email' => 'Este e-mail já está cadastrado.']);
        }

        return $this->saved($request, $candidate, 'candidatos', $candidate->wasRecentlyCreated ? 201 : 200);
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
