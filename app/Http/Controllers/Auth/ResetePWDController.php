<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ResetePWDController extends Controller
{
   //============ Forgot Password =================================
   public function forgot(Request $request) {
    $credentials = request()->validate(['email' => 'required|email']);
    $user = User::where('email','=',$request->email)->first();

    if(empty($user)){
        return response()->json(["msg" => 'You are not registered']);
    }
    $user->update(['']);
    $this->sendEmail($request->email);
        return response()->json([
            'message' => 'Password reset mail has been sent.'
        ], Response::HTTP_OK);
}

//============ Send Email =================================
public function sendEmail($email){
    $token = $this->createToken($email);
    Mail::to($email)->send(new SendMail($token));
}
//============ Send Email =================================

public function validEmail($email) {
    return !!User::where('email', $email)->first();
 }
//============ Create Token for reset password =================================
 public function createToken($email){

    $isToken = DB::table('password_resets')->where('email', $email)->first();

    if($isToken) {
      return $isToken->token;
    }
    $user = User::where('email','=',$email)->first();
    $userRole = $user->roles()->first();
    if ($userRole) {
        $this->scope = $userRole->title;
    }
    $tokenResult = $user->createToken($user->email.'-' .now(), [$this->scope]);
    $this->saveToken($tokenResult->accessToken, $email);
    return $tokenResult->accessToken;
  }
//============ Save Token for reset password =================================
  public function saveToken($token, $email){
    DB::table('password_resets')->insert([
        'email' => $email,
        'token' => $token,
        'created_at' => Carbon::now()
    ]);
}
}
