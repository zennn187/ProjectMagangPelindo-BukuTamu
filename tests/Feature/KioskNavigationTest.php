<?php

namespace Tests\Feature;

use Tests\TestCase;

class KioskNavigationTest extends TestCase
{
    public function test_guest_kiosk_navigation_links_to_staff_login(): void
    {
        $this->get(route('kiosk'))
            ->assertOk()
            ->assertSee(route('login'), false)
            ->assertSee('Login admin atau petugas');
    }
}
