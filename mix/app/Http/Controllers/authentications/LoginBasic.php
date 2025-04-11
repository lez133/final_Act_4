<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class LoginBasic extends Controller
{

  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function redirectToGoogle()
  {
    return Socialite::driver('google')->redirect();
  }


  public function handleGoogleCallback()
  {
    try {
      $googleUser = Socialite::driver('google')->user();

      if (!$googleUser || !$googleUser->getId()) {
        return redirect()->route('auth-login-basic')->with('error', 'Unable to retrieve Google user data.');
      }

      $user = User::firstOrCreate(
        ['google_id' => $googleUser->getId()],
        [
          'name' => Crypt::encryptString($googleUser->getName()),
          'email' => Crypt::encryptString($googleUser->getEmail()),
          'password' => bcrypt(Str::random(16)),
        ]
      );


      Auth::login($user);


      return redirect()->route('dashboard-analytics');
    } catch (\Exception $e) {

      Log::error('Google login error: ' . $e->getMessage());
      return redirect()->route('auth-login-basic')->with('error', 'There was an error during the login process.');
    }
  }

  public function userList()
  {
    $users = User::all();
    return view('content.tables.tables-basic', compact('users'));
  }

  public function updateUser(Request $request, $id)
  {
    $user = User::findOrFail($id);

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,' . $id,
    ]);

    $user->update([
      'name' => Crypt::encryptString($request->input('name')),
      'email' => Crypt::encryptString($request->input('email')),
    ]);

    return redirect()->route('user-list')->with('success', 'User updated successfully.');
  }
}
