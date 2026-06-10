<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Link",
    title: "Link",
    description: "Link model",
    required: ["url", "code"],
    properties: [
        new OA\Property(property: "url", type: "string", example: "https://www.google.com/"),
        new OA\Property(property: "code", type: "string", example: "10jnbg"),
        new OA\Property(property: "clicks", type: "integer", example: 0),
    ]
)]

class Link extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'url',
        'code', 
        'clicks',
    ];      
}
