<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;

use Illuminate\Http\Request;
use App\Http\Controllers\Enum\MessageEnum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserProfileController extends Controller
{
 
    public function show()
    {
      $id=Auth::id();
      $user=User::with(['profile'])->find($id);
    
        return view('profile', ['user' => $user]);
    }

    public function save_profile(Request $request){
        Validator::validate($request->all(),[
            'address'             =>'required|string|between:3,20',
            'phone'               =>'required|regex:/^([0-9]*)$/|not_regex:/[a-z]/|min:9|max:9|starts_with:77,73,71,70',
            'avatar'              =>'required',
        ],[
            'between'             => $this->messageBetween(3, 20),
            'required'            => MessageEnum::REQUIRED,
            'not_regex'           => MessageEnum::MESSAGE_NUMBERS,
            'phone.min'           => 'يجب ان لا يقل عن 9 ارقام',
            'phone.max'           => 'يجب ان لا يزيد عن 9 ارقام',
            'phone.starts_with'   => 'يمكنك ادخال 77 او 73 او 71 او 70 في البداية',
        ]);
        // echo $request;
        $profile = new UserProfile;
        if($request->hasFile('avatar'))
          $profile->avatar=$this->uploadFile($request->file('avatar'));
        $profile->address = $request->address;
        $profile->phone = $request->phone;
        $profile->user_id  = Auth::id();
        return $this->messageRedirectAdd($profile->save(), 'profile');
    }

    public function save_edit_profile(Request $request){
      Validator::validate($request->all(),[

          'name'                =>'required|string|between:3,20',
          'email'               =>'required|email',
          'address'             =>'required|string|between:3,20',
          'phone'               =>'required|regex:/^([0-9]*)$/|not_regex:/[a-z]/|min:9|max:9|starts_with:77,73,71,70',
      ],[
          'between'             => $this->messageBetween(3, 20),
          'required'            => MessageEnum::REQUIRED,
          'string'              => MessageEnum::MESSAGE_STRING,
          'email.email'         => 'البريد الالكتروني غير صحيح',
          'not_regex'           => MessageEnum::MESSAGE_NUMBERS,
          'phone.min'           => 'يجب ان لا يقل عن 9 ارقام',
          'phone.max'           => 'يجب ان لا يزيد عن 9 ارقام',
          'phone.starts_with'   => 'يمكنك ادخال 77 او 73 او 71 او 70 في البداية',
      ]);
        $name                   = $request->name;
        $email                  = $request->email;
        $userSave = User::where('id',Auth::id())->update([
          'name'    => $name,
          'email'   => $email,
        ]);
        $address                = $request->address;
        $phone                  = $request->phone;
        $profileSave = UserProfile::where('user_id', Auth::id())->update([
          'address'   => $address,
          'phone'     => $phone
        ]);
        return $this->messageRedirectUpdate($userSave && $profileSave, 'profile');
    }

    public function editImageProfile(Request $request){
      Validator::validate($request->all(),[
          'image'           =>'required|mimes:jpeg,png,jpg,gif,svg|max:6000',
      ],[
          'image.required'  =>MessageEnum::REQUIRED,
          'image.mimes'     => MessageEnum::MESSAGE_IMAGES,
      ]);
      if($request->hasFile('image'))
          $image = $this->uploadFile($request->file('image'));
      $imageSave = UserProfile::where('user_id', Auth::id())->update(['avatar' => $image]);
      
      return $this->messageRedirectUpdate($imageSave, 'profile');
    }

    public function messageRedirectUpdate($success ,$route){
      if($success){
          return redirect($route)
          ->with(['success'=>MessageEnum::MESSAGE_UPDATE_SUCCESS]);
      }else{
          return back()->with(['error'=>MessageEnum::MESSAGE_UPDATE_ERROR]);
      }
  }
}
