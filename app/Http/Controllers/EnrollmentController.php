<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnrollmentController extends Controller
{
    public function store(Request $request, Vacancy $vacancy): Response
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        DB::transaction(function () use ($data, $vacancy) {
            $locked = Vacancy::query()->lockForUpdate()->findOrFail($vacancy->id);
            if (! $locked->acceptsApplications()) {
                throw ValidationException::withMessages(['vacancy' => 'Esta vaga está pausada.']);
            }
            $candidate = Candidate::where('email', strtolower($data['email']))->lockForUpdate()->first();
            if (! $candidate) {
                throw ValidationException::withMessages(['email' => 'Cadastre o candidato antes de inscrevê-lo.']);
            }
            if ($locked->candidates()->whereKey($candidate->id)->exists()) {
                throw ValidationException::withMessages(['email' => 'Este candidato já está inscrito nesta vaga.']);
            }
            $locked->candidates()->attach($candidate);
        }, 3);

        return $request->is('api/*') ? response()->json(['message' => 'Inscrição realizada.'], 201) : back()->with('status', 'Inscrição realizada.');
    }
}
