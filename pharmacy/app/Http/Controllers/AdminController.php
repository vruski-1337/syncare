<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $companyCount = \App\Models\Company::count();
        $subscriptionCount = \App\Models\Subscription::count();
        $usersCount = \App\Models\User::count();
        return view('admin.dashboard', compact('companyCount','subscriptionCount','usersCount'));
    }

    public function settings()
    {
        $keys = ['gmail_client_id','gmail_client_secret','database_uri','footer_text'];
        $settings = [];
        foreach($keys as $k){
            $settings[$k] = \App\Models\Setting::get($k);
        }
        return view('admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'gmail_client_id' => 'nullable|string',
            'gmail_client_secret' => 'nullable|string',
            'database_uri' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
        ]);
        foreach($data as $key=>$value){
            \App\Models\Setting::set($key,$value);
        }
        return back()->with('success','Settings saved');
    }

    /**
     * Reset credentials for a company owner or manager. To simplify, accepts user id.
     */
    public function resetCredentials(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = \App\Models\User::find($data['user_id']);
        $user->password = bcrypt($data['password']);
        $user->save();
        return back()->with('success','Password reset');
    }
}

