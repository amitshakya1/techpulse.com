<?php

namespace App\Http\Controllers\Api\N8N;

use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index()
    {
        return $this->successResponse([
            'data' => ['testing'],
        ], 'News fetched successfully');
    }
}
