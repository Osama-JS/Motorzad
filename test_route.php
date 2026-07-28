<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/contacts/data', 'GET');
// We need to bypass auth for this test, or login as admin
$admin = \App\Models\User::where('email', 'admin@motorzad.com')->first();
auth()->login($admin);
$response = $kernel->handle($request);
echo $response->getContent();
