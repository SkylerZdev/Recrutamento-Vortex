<?php

namespace App\Http\Requests;

use App\Enums\Contract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyRequest extends FormRequest
{
    public function rules(): array
    {
        $required = $this->isMethod('patch') ? ['sometimes', 'required'] : ['required'];

        return [
            'title' => [...$required, 'string', 'max:255'],
            'description' => [...$required, 'string', 'max:10000'],
            'contract' => [...$required, Rule::enum(Contract::class)],
            'paused' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
