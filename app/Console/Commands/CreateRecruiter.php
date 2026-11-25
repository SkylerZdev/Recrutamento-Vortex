<?php

namespace App\Console\Commands;

use App\Models\User;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateRecruiter extends Command
{
    protected $signature = 'recrutamento:usuario';

    protected $description = 'Criar uma conta de acesso ao recrutamento';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Nome'),
            'email' => strtolower(trim((string) $this->ask('E-mail'))),
            'password' => $this->secret('Senha (mínimo de 12 caracteres)'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['bail', 'required', 'string', 'min:12', static function (string $attribute, string $value, Closure $fail): void {
                if (strlen($value) > 72) {
                    $fail('A senha deve ter até 72 bytes.');
                }
            }],
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        User::create($validator->validated());
        $this->info('Conta criada.');

        return self::SUCCESS;
    }
}
