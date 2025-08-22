<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;


    function __construct()
    {
        $macAddress = exec('getmac');
    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    public function showResetForm(Request $request, $token = null)
    {


        return view('backend.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $macId = Request::ip();

        $request->validate($this->rules(), $this->validationErrorMessages());

        // Reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($macId, $request) {
                // Hash the new password and update the user model
                $user->password = Hash::make($password);
                // dd($user);
                $user->save();
            }
        );

        // If the password was successfully reset, redirect to the login page
        if ($status === Password::PASSWORD_RESET) {
            // Password reset successful
            return redirect()->route('login')->with('status', 'Password reset successfully');
        } else if ($status === Password::INVALID_USER) {
            // User not found or invalid token
            return back()->withErrors(['email' => [trans($status)]]);
        } else {
            // Other error occurred (e.g., invalid password, token expired)
            return back()->withErrors(['email' => [trans($status)]]);
        }
    }
}
