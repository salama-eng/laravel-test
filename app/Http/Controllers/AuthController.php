<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
// use Illuminate\Foundation\Validation\ValidatesRequests;


class AuthController extends Controller
{

    public function listAll()
    {
        return view('admin.users.list_users');
    }

    public function showLogin()
    {
        // return view('admin.login');
        if (Auth::check())
            return redirect()->route($this->checkRole());
        else
            return view('login');
    }

    public function login(Request $request)
    {
  
        Validator::validate($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ], [
            'required' =>'required',
            'email.email' => 'error',
            'email.exists' => 'error',
        ]);
        $user = User::where('email', '=', $request->email)->first();
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('login')->with(['password'=>false, 'message' => 'not correct']);
         }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (Auth::user()->role == 'admin')
                return redirect()->route('users_list');
            else
                return redirect()->route('user_profile');
        } else {
            return redirect()->route('login')->with([
                'message' => '  عذرا! يمكنك  تفعيل حسابك اولا',
            ]);
        }
    }



    public function showregister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        //   return request();

        Validator::validate($request->all(), [
            'name' => ['required', 'min:3', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:5'],
            'confirm_pass' => ['same:password'],

        ], [
            'required' => 'this field is required',
            'name.min' => 'min 3 letters',
            'email.unique' => 'not available ',
            'email.email' => 'not correct',
            'password.min' => 'min 3 letters',
            'confirm_pass.same' => 'not the same',
        ]);


        $v = $request->password;
        $u = new User();
        $u->name = $request->name;
        $u->password = Hash::make($request->password);
        $u->email = $request->email;
        $u->role = "client";
        $token = Str::uuid();
        $u->remember_token = $token;
        if ($u->save()) {
        $profile = new User();
        $profile->avatar = "no_png.png";
        $profile->user_id = Auth::id();

        $profile->save();
            $email_data = array(
                'name' => $request->name, 'email' => $request->email, 'password' => $v,
                'activation_url' => URL::to('/') . "/verify_email/" . $token . "/".$v
            );
            //  try {

            //     Mail::to($request->email)->send(new VerificationEmail($email_data));
            //     return view('mail.resend_email', [
            //         'email_data' => $email_data,
            //     ]);

            // } catch (\Exception ) {

            //     return back()->with(['message'=>'تأكد من كتابة البيانات بالشكل الصحيح ']);
            // }
            return back()->with(['message'=>'login successfully']);
        }
        else {
            return back()->with(['message'=>'تأكد من كتابة البيانات بالشكل الصحيح ']);
        }
    }

    public function resendEmail(Request $request)
    {
        $email_data = array(
            'name' => $request->name, 'email' => $request->email,
            'activation_url' => $request->activation_url
        );

        // try {

        //         Mail::to($request->email)->send(new VerificationEmail($email_data));
        //         return view('mail.resend_email', [
        //             'email_data' => $email_data,
        //         ]);

        //     } catch (\Exception ) {

        //         return back()->with(['message'=>'تأكد من كتابة البيانات بالشكل الصحيح ']);
        //     }
    }

    public function logout(){

        Auth::logout();
        return redirect()->route('login');
    }

    public function checkRole()
    {
        if (Auth::user()->hasRole('admin'))
            return 'home';
        else
            return 'home';
    }

    
}
