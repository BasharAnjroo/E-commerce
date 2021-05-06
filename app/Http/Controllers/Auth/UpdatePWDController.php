<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdatePWDController extends Controller
{



//============ update Password =================================
   public function updatePassword(RequestHelper $request){
    return $this->validateToken($request)->count() > 0 ? $this->changePassword($request) : $this->noToken();
}
//============ Validate Token =================================
private function validateToken($request){
    return DB::table('password_resets')->where([
        'email' => $request->email,
        'token' => $request->passwordToken
    ]);
}
//============ If no or yes Token =================================
private function noToken() {
    return response()->json([
      'error' => 'Email or token does not exist.'
    ],Response::HTTP_UNPROCESSABLE_ENTITY);
}
//============ Change Password =================================

private function changePassword($request) {

    $user = User::whereEmail($request->email)->first();
    $user->update([
      'password' => bcrypt($request->password)
    ]);
    $this->validateToken($request)->delete();
    return response()->json([
      'data' => 'Password changed successfully.'
    ],Response::HTTP_CREATED);
}

}
