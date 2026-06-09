<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Link;

class LinkController extends Controller
{
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
                return response($existingLink, 201);
            } else {
                $model = new Link;
                $code = Str::random(6);
                //check for existing match
                while (Link::where('code', $code)->first() !== null) {
                    $code = Str::random(6);
                }
                
                $link = $model->create([
                    'url' => $request->input('url'),
                    'code' => $code,
                ])->select('url', 'code');

                return response($link, 201);
            }
        }
    }

    public function show($code)
    {
        $validator = Validator::make(['code' => $code], [
            'code' => 'required|string|exists:links,code',
        ]);

        if ($validator->fails()) {
            return response(['error'=>'Нет такой ссылки.'], 404);
        } else {
            $link = Link::where('code', $code)->first()?->increment('clicks');
            return redirect()->away($link['url']);
        }
       
    }

    public function stats($code)
    {
        $validator = Validator::make(['code' => $code], [
            'code' => 'required|string|exists:links,code',
        ]);

        if ($validator->fails()) {
            return response(['error'=>'Нет такой ссылки.'], 404);
        } else {
             return Link::where('code', $code)->first();
        }
       
    }
}
