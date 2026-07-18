<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests are redirected to login from admin pages', function () {
    $this->get('/admin')->assertRedirect(route('login'));
});

test('admin can log in and reach the dashboard', function () {
    $user = User::factory()->create([
        'email' => 'owner@kitchen.local',
        'password' => 'password',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('admin can view profile settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.profile.edit'))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->email);
});

test('admin can update profile details', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('admin.profile.update'), [
            'name' => 'Updated Admin',
            'email' => 'updated@kitchen.local',
            'phone' => '+977 980-999-8888',
            'title' => 'General Manager',
        ])
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->fresh())
        ->name->toBe('Updated Admin')
        ->email->toBe('updated@kitchen.local')
        ->phone->toBe('+977 980-999-8888')
        ->title->toBe('General Manager');
});

test('admin can update password', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->actingAs($user)
        ->put(route('admin.profile.password'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect(route('admin.profile.edit'));

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('password update requires the current password', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->actingAs($user)
        ->put(route('admin.profile.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasErrors('current_password');
});
