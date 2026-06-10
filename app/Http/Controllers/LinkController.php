<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Link;
use OpenApi\Attributes as OA;

class LinkController extends Controller
{
    #[OA\Post(
        path: "/api/links",
        tags: ["Links"],
        summary: "Create link",
        description: "Creates a link",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Link",
            content: new OA\JsonContent(
                required: ["url"],
                properties: [
                    new OA\Property(property: "url", type: "string", example: "https://www.google.com/"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Link created successfully",
    )]
    #[OA\Response(
        response:422,
        description: "Invalid request",
    )]
    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'url' => 'required|url:http,https'
        ]);
    
        if ($validator->fails()) {
            return response(['error'=>'URL введен некорректно.'], 422);
        } else {
            //check url in db, if exists send corresponding code
            $existingLink = Link::where('url', $request->input('url'))->first();
            
            if ($existingLink !== null) {
                $code = $existingLink['code'];

                $returnLink = [
                    'code' => $code,
                    'short_url' => env('APP_URL').'/'.$code,
                ];

                return response($returnLink, 201);
            } else {
                $model = new Link;
                $code = Str::random(6);
                //check for existing match
                while (Link::where('code', $code)->first() !== null) {
                    $code = Str::random(6);
                }
                
                $link = $model->create([
                    'code' => $code,
                    'url' => $request->input('url'),
                ]);

                $returnLink = [
                    'code' => $code,
                    'short_url' => env('APP_URL').'/'.$code,
                ];

                return response()->json($returnLink, 201);
            }
        }
    }

    #[OA\Get(
        path: "/{code}",
        summary: "Redirect to link by code",
        description: "Redirects to link",
        tags: ["Links"],
        parameters: [
            new OA\Parameter(
                name: "code",
                description: "Link code",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 302,
                description: "Redirect",
                headers: [
                    new OA\Header(
                        header: 'Location',
                        description: 'The URL the client is redirected to',
                        schema: new OA\Schema(type: 'string', format: 'uri')
                    )
                ]
            ),
            new OA\Response(
                response: 404,
                description: "Link not found"
            )
        ]
    )]
    public function show(string $code)
    {
        $validator = Validator::make(['code' => $code], [
            'code' => 'required|string|exists:links,code',
        ]);

        if ($validator->fails()) {
            return response(['error'=>'Нет такой ссылки.'], 404);
        } else {
            $updlLink = Link::where('code', $code)->first()?->increment('clicks');
            $link = Link::where('code', $code)->first();
            return redirect()->away($link['url']);
        }
       
    }
    #[OA\Get(
        path: "/api/links/{code}/stats",
        summary: "Get link stats",
        description: "Returns link statistics",
        tags: ["Links"],
        parameters: [
            new OA\Parameter(
                name: "code",
                description: "Link code",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(ref: "#/components/schemas/Link")
            ),
            new OA\Response(
                response: 404,
                description: "Link not found"
            )
        ]
    )]
    public function stats(string $code)
    {
        $validator = Validator::make(['code' => $code], [
            'code' => 'required|string|exists:links,code',
        ]);

        if ($validator->fails()) {
            return response(['error'=>'Нет такой ссылки.'], 404);
        } else {
             return Link::where('code', $code)->select('url', 'code', 'clicks', 'created_at')->first();
        }
       
    }
}
