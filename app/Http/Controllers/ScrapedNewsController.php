<?php

namespace App\Http\Controllers;

use App\Models\ScrapedNews;
use Illuminate\Http\Request;

class ScrapedNewsController extends Controller
{
    public function index(){
        return inertia('Welcome',[
            'data'=> ScrapedNews::select('scrapedId', 'title', 'link', 'points', 'created_date')->get()
        ]);
    }
}
