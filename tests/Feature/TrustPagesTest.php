<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_trust_pages_render_successfully(): void
    {
        foreach ([
            'contact',
            'shipping-policy',
            'return-exchange',
            'faq',
            'about',
            'size-guide',
            'privacy-policy',
            'terms-conditions',
        ] as $slug) {
            $this->get(route('pages.show', $slug))
                ->assertOk();
        }
    }
}
