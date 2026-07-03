<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

test('google redirect works correctly', function () {
    $socialiteMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $socialiteMock->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($socialiteMock);

    $response = $this->get('/login/google');

    $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

test('google callback logs in existing user', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $googleUserMock = Mockery::mock('Laravel\Socialite\Two\User');
    $googleUserMock->shouldReceive('getId')->andReturn('google-id-123');
    $googleUserMock->shouldReceive('getEmail')->andReturn('test@example.com');
    $googleUserMock->shouldReceive('getName')->andReturn('Test User');

    $socialiteMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $socialiteMock->shouldReceive('user')->once()->andReturn($googleUserMock);

    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($socialiteMock);

    $response = $this->get('/login/google/callback');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh()->google_id)->toBe('google-id-123');
});

test('google callback registers admin user if email matches config', function () {
    config(['app.admin.email' => 'admin@devmisno.web.id']);

    $googleUserMock = Mockery::mock('Laravel\Socialite\Two\User');
    $googleUserMock->shouldReceive('getId')->andReturn('google-id-456');
    $googleUserMock->shouldReceive('getEmail')->andReturn('admin@devmisno.web.id');
    $googleUserMock->shouldReceive('getName')->andReturn('Admin User');

    $socialiteMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $socialiteMock->shouldReceive('user')->once()->andReturn($googleUserMock);

    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($socialiteMock);

    $response = $this->get('/login/google/callback');

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'admin@devmisno.web.id')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('google-id-456');
    expect($user->password)->toBeNull();
});

test('google callback rejects unregistered user if email does not match admin email', function () {
    config(['app.admin.email' => 'admin@devmisno.web.id']);

    $googleUserMock = Mockery::mock('Laravel\Socialite\Two\User');
    $googleUserMock->shouldReceive('getId')->andReturn('google-id-789');
    $googleUserMock->shouldReceive('getEmail')->andReturn('intruder@example.com');
    $googleUserMock->shouldReceive('getName')->andReturn('Intruder');

    $socialiteMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $socialiteMock->shouldReceive('user')->once()->andReturn($googleUserMock);

    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($socialiteMock);

    $response = $this->get('/login/google/callback');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
});
