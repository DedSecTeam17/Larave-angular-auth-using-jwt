<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePassword\ChangePasswordRequest;
use App\Mail\RestPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetController extends Controller
{
    //

    public  function  sendEmail(Request $request){

        if ($this->validateEmail($request->email))
        {
            $this->sendEmailToUser($request->email);
            return \response()->json(['message'=>'email sent'],Response::HTTP_OK);
        }else{
            return \response()->json(['message'=>'email not found'],Response::HTTP_NOT_FOUND);
        }


    }



    public  function  sendEmailToUser($email){


         $token=$this->createToken($email);
        Mail::to($email)->send(new RestPasswordMail($token));

    }

    public function createToken($email){

        $oldToken=DB::table('password_resets')->where('email',$email)->first();
        if (!$oldToken){
            $token=\str_random(60);
            $this->saveToken($token,$email);

            return $token;
        }else{
            return $oldToken->token;
        }
    }

    public  function  saveToken($token,$email){

        DB::table('password_resets')->insert([
            'token'=>$token,
            'email'=>$email,
            'created_at'=>Carbon::now()
        ]);

    }


    public  function  changePassword(ChangePasswordRequest $request){
        if ($this->validateToken($request)){

            $user=User::whereEmail($request->email)->first();
            $user->update(['password'=>\bcrypt($request->password)]);

            $this->deletePreviousToken($request);

            return \response()->json(['message'=>'password updated successfully'],200);

//            change password

        }else{

            return \response()->json(['message'=>'invalid url please ask again for password reset'],Response::HTTP_NOT_FOUND);
        }

    }



    public  function  deletePreviousToken($request){
       $resetPasswordRow= DB::table('password_resets')->where(['token'=>$request->resetToken,'email'=>$request->email]);
       $resetPasswordRow->delete();
    }

    public  function  validateToken($request){
        $saveToken=DB::table('password_resets')->where(['token'=>$request->resetToken,'email'=>$request->email])->first();
        if ($saveToken){
            return true;
        }else{
            return false;
        }

    }

    public  function  validateEmail($email) {
        return   !!User::where('email',$email)->first();
    }
}
