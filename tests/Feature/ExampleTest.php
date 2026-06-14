<?php

test('the home page redirects to dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});

test('unauthenticated users are redirected to login from dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});
