<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/admin/users', 'POST', [
    'name' => 'test2', 
    'email' => 'test2@x.com', 
    'password' => 'password', 
    'roles' => [1]
]);

$controller = app(\App\Http\Controllers\Admin\UserController::class);
try {
    $response = $controller->store($request);
    echo "SUCCESS:\n" . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "ERROR:\n" . $e->getMessage() . "\n" . $e->getTraceAsString();
}
