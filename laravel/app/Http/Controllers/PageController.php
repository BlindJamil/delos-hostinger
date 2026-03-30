<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function projects()
    {
        return view('pages.projects');
    }

    public function brands()
    {
        return view('pages.brands');
    }

    public function branches()
    {
        return view('pages.branches');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
