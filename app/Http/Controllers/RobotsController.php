<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RobotsController extends Controller
{
    public function index()
    {
        $production = app()->environment('production');
        
        $content = view('robots.index', [
            'production' => $production,
        ])->render();
        
        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
