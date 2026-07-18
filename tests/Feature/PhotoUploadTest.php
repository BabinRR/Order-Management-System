<?php

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can upload a profile photo', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('admin.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'title' => $user->title,
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 400, 400),
        ])
        ->assertRedirect(route('admin.profile.edit'));

    $user->refresh();

    expect($user->avatar_url)->not->toBeNull()
        ->and($user->avatar_public_id)->toStartWith('local:');

    Storage::disk('public')->assertExists(substr($user->avatar_public_id, 6));
});

test('admin can upload a menu item photo', function () {
    Storage::fake('public');

    $this->actingAs(User::factory()->create())
        ->post(route('admin.menu.store'), [
            'name' => 'Wood Fired Pizza',
            'category' => 'Mains',
            'price' => 1200,
            'description' => 'Crispy pizza with wood-fired crust.',
            'status' => 'Available',
            'image' => UploadedFile::fake()->image('pizza.jpg', 640, 480),
        ])
        ->assertRedirect(route('admin.menu.index'));

    $item = MenuItem::query()->where('name', 'Wood Fired Pizza')->first();

    expect($item)->not->toBeNull()
        ->and($item->image_url)->not->toBeNull()
        ->and($item->image_public_id)->toStartWith('local:');

    Storage::disk('public')->assertExists(substr($item->image_public_id, 6));
});
