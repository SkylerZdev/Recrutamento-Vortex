<?php

namespace Tests\Unit;

use App\Models\Vacancy;
use PHPUnit\Framework\TestCase;

class VacancyTest extends TestCase
{
    public function test_availability_follows_the_pause_state(): void
    {
        $vacancy = new Vacancy;
        $this->assertTrue($vacancy->acceptsApplications());
        $vacancy->paused = true;
        $this->assertFalse($vacancy->acceptsApplications());
        $vacancy->paused = false;
        $this->assertTrue($vacancy->acceptsApplications());
    }
}
