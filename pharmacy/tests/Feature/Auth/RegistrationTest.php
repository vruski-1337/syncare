<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertStatus(404);
});

test('users without a company are provisioned and redirected from dashboard', function () {
    $user = User::factory()->create([
        'role' => 'manager',
        'company_id' => null,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $user->refresh();

    expect($user->company_id)->not->toBeNull();
    $response->assertRedirect(route('company.dashboard', absolute: false));
});
