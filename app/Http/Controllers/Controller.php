<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Link', description: 'Link service')]
#[OA\Info(
    version: '1.0',
    title: 'Short link service',
    description: 'Provides short links',
    contact: new OA\Contact(name: 'Swagger API Team'),
)]
#[OA\Server(
    url: 'http://127.0.0.1:81',
    description: 'API server',
)]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
