<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root path redirects to the resolved locale (default EN for a fresh visit).
     * The EN page itself should then return 200.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertRedirect('/en');
        $this->get('/en')->assertStatus(200);
    }
}
