<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_change_their_own_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $this->actingAs($user)->get('/account')->assertOk();
        $this->patch('/account/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect('/account')->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_wrong_current_password_and_mismatched_confirmation_are_rejected(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $this->actingAs($user)->patch('/account/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors(['current_password', 'password']);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_guests_cannot_change_passwords(): void
    {
        $this->get('/account')->assertRedirect('/login');
        $this->patch('/account/password')->assertRedirect('/login');
    }
}
