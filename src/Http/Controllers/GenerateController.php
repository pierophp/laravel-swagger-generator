<?php

namespace LaravelSwaggerGenerator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use LaravelSwaggerGenerator\Core\Generator;

class GenerateController extends Controller
{
    /**
     * Generate
     *
     * @param \Illuminate\Http\Request $request
     */
    public function generate(Request $request)
    {
        $replace = false;
        if ($request->get('replace')) {
            $replace = true;
        }
        
        $generator = new Generator();
        $generator->generate($replace);
    }
}
