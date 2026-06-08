<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Motorzad API Documentation",
    version: "1.0.0",
    description: "API Documentation for Motorzad Car Auctions Platform.",
    contact: new OA\Contact(email: "support@motorzad.com")
)]
#[OA\Server(
    url: "http://localhost/Motorzad/public",
    description: "XAMPP Apache Server (Local)"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Artisan Development Server (php artisan serve)"
)]
#[OA\Server(
    url: "/",
    description: "Production Server (Root Domain)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
abstract class Controller
{
    //
}
