<?php

namespace App\Http\Controllers;

use App\Mail\ContactanosMailable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(){
        return Inertia::render('Contact/Index', [
            'canLogin' => Route::has('login'),
            'message' => Session::get('flash.message'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string|max:255', 
            'phone' => 'nullable|string',
            'subject' => 'nullable|string'
        ]);
        Mail::to('admin@gimnacio.com')
        ->send(new ContactanosMailable($request->all()));
        
        return to_route('contact.index');
    }
}
