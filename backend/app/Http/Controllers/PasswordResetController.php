<?php

namespace App\Http\Controllers;

use App\Mail\RestPasswordMail;
use App\User;
use Illuminate\Http\Request;
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

        Mail::to($email)->send(new RestPasswordMail());

    }


    public  function  validateEmail($email) {
        return   !!User::where('email',$email)->first();
    }
}
