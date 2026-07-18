<?php

test('the application redirects to the admin dashboard', function () {
    $this->get('/')->assertRedirect('/admin');
});

test('the admin dashboard loads successfully', function () {
    $this->get('/admin')->assertOk();
});
