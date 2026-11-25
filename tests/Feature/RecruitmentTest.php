<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecruitmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_and_api_authentication(): void
    {
        $this->followingRedirects()->get('/')->assertOk()->assertSee('Senha');
        $this->getJson('/api/vagas')->assertUnauthorized();
        $user = User::factory()->create();
        $this->post('/entrar', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/entrar', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/vagas');
        $this->assertAuthenticatedAs($user);
        $this->post('/sair')->assertRedirect('/entrar');
        $this->assertGuest();
        $this->withBasicAuth($user->email, 'password')->getJson('/api/vagas')->assertOk();
    }

    public function test_vacancy_and_candidate_forms_and_crud(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withBasicAuth($user->email, 'password');
        foreach ([
            ['vagas', Vacancy::class, ['title' => 'Analista', 'contract' => 'CLT', 'description' => 'Analisar dados.'], ['title' => 'Analista sênior']],
            ['candidatos', Candidate::class, ['name' => 'Ana', 'email' => 'ANA@example.org'], ['name' => 'Ana Silva']],
        ] as [$section, $model, $data, $changes]) {
            $url = '/'.$section;
            $this->get($url.'/create')->assertOk()->assertSee('Salvar');
            $this->post($url, [])->assertSessionHasErrors(array_keys($data));
            $this->post($url, $data)->assertSessionHasNoErrors();
            $record = $model::firstOrFail();
            $detail = $url.'/'.$record->id;
            $this->get($url)->assertOk()->assertSee(reset($data));
            $this->get($detail)->assertOk();
            $this->get($detail.'/edit')->assertOk()->assertSee(reset($data));
            $this->put($detail, array_merge($data, $changes))->assertRedirect($detail);
            $this->getJson('/api'.$detail)->assertOk()->assertJsonPath('data.'.array_key_first($changes), reset($changes));
            $this->delete($detail)->assertRedirect($url);
            $this->assertModelMissing($record);
        }
    }

    public function test_api_validation_and_partial_updates(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withBasicAuth($user->email, 'password');
        $candidate = Candidate::factory()->create();
        $this->postJson('/api/candidatos', ['name' => 'Outro', 'email' => strtoupper($candidate->email)])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->postJson('/api/vagas', ['contract' => 'Outro', 'paused' => 'talvez'])->assertUnprocessable()->assertJsonValidationErrors(['title', 'description', 'contract', 'paused']);
        $id = $this->postJson('/api/candidatos', ['name' => 'Bia', 'email' => 'BIA@example.org'])->assertCreated()->assertJsonPath('email', 'bia@example.org')->json('id');
        $this->patchJson('/api/candidatos/'.$id, ['name' => 'Beatriz'])->assertOk()->assertJsonPath('name', 'Beatriz');
        $this->deleteJson('/api/candidatos/'.$id)->assertNoContent();
    }

    public function test_filters_and_pagination_work_together(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withBasicAuth($user->email, 'password');
        Vacancy::factory(24)->create(['title' => 'Design', 'paused' => true]);
        $vacancy = Vacancy::factory()->create(['title' => 'Engenharia', 'contract' => 'CLT']);
        $this->getJson('/api/vagas')->assertJsonCount(20, 'data')->assertJsonPath('total', 25);
        $this->getJson('/api/vagas?paused=1&per_page=10&page=3')->assertJsonCount(4, 'data');
        $query = http_build_query(['id' => $vacancy->id, 'title' => 'Engen', 'description' => $vacancy->description, 'contract' => 'CLT', 'paused' => 0, 'created_at' => today()->toDateString(), 'updated_at' => today()->toDateString()]);
        $this->getJson('/api/vagas?'.$query)->assertJsonPath('total', 1)->assertJsonPath('data.0.id', $vacancy->id);
        $this->getJson('/api/vagas?per_page=0')->assertUnprocessable();
        $candidate = Candidate::factory()->create();
        $this->getJson('/api/candidatos?'.http_build_query(['name' => $candidate->name, 'email' => $candidate->email]))->assertJsonPath('total', 1);
    }

    public function test_enrollment_respects_pause_duplicates_and_cascade_deletion(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withBasicAuth($user->email, 'password');
        [$first, $second] = Vacancy::factory(2)->create();
        $candidate = Candidate::factory()->create();
        $data = ['email' => $candidate->email];
        $url = '/api/vagas/'.$first->id;
        $this->postJson($url.'/inscricoes', $data)->assertCreated();
        $this->postJson($url.'/inscricoes', $data)->assertUnprocessable();
        $this->postJson('/api/vagas/'.$second->id.'/inscricoes', $data)->assertCreated();
        $this->patchJson($url, ['paused' => true])->assertOk();
        $other = Candidate::factory()->create();
        $this->postJson($url.'/inscricoes', ['email' => $other->email])->assertUnprocessable()->assertJsonValidationErrors('vacancy');
        $this->get('/vagas/'.$first->id)->assertDontSee('Inscrever candidato')->assertSee($candidate->name);
        $this->assertDatabaseCount('candidate_vacancy', 2);
        $this->patchJson($url, ['paused' => false])->assertOk();
        $this->postJson($url.'/inscricoes', ['email' => $other->email])->assertCreated();
        $this->deleteJson('/api/vagas/lote', ['ids' => [$first->id, $second->id]])->assertNoContent();
        $this->assertDatabaseCount('candidate_vacancy', 0);
        $this->assertDatabaseCount('candidates', 2);
        $this->deleteJson('/api/candidatos/lote', ['ids' => []])->assertUnprocessable();
        $this->deleteJson('/api/candidatos/lote', ['ids' => [$other->id]])->assertNoContent();
        $this->assertModelExists($candidate);
    }
}
