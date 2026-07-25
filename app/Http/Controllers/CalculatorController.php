<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class CalculatorController extends Controller
{
    public function index()
    {
        return Inertia::render('Calculator/Index');
    }
}