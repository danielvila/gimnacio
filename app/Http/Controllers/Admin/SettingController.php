<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/Index', ['setting'=> Setting::first(), 'autorized' => auth()->user()->roles()->first()->name]);
    }

    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'name' => 'nullable|string|max:60',
            'ruc' => 'nullable|string|max:60',            
            'dv' => 'nullable|string|max:15',
            'logo' => 'image',
            'description' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:60',
            'phone' => 'nullable|string',
        ]);
        $image = '';
        if ($request->file('logo')) {
            $image = $request->file('logo')->store('public/setting');
        }
        $setting->update([
            'name' => $request->input('name'),
            'ruc' => $request->input('ruc'),
            'dv' => $request->input('dv'),
            'logo' => $image,
            'description' => $request->input('description'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);
       
        return to_route('settings.index');
    }

    public function destroy(Setting $setting)
    {
        if($setting->Logodelete){
            if(Storage::delete($setting->Logodelete)){
                $setting->update(['logo' => null]);
            }else{
                return redirect()->route('settings.index')
                        ->withErrors([
                            'error' => '¡No sepudo eliminar el logo!',
                        ]);
            }
        }else{
            return redirect()->route('settings.index')
                    ->withErrors([
                        'error' => '¡No se encontro el logo!',
                    ]);
        }
       
            
      
        return to_route('settings.index');
    }
}
