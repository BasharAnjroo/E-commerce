<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Mail\SendMail;
use App\Mail\verification;
use App\Models\User;
use App\Models\VerifyUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{
      /*
        Writen By Bashar Anjroo.

         * ######## SignUp Admin ##########

         * @param  [string] name
         * @param  [string] email
         * @param  [string] password
         * @param  [string] password_confirmation
         * @return [string] message
         */
     /*    public function Register(SignUpRequest $request)
        {
            // create new user :
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)

            ]);
            $user->save();
            // return message api user
            $success['name'] = $user->name;
            $tokenResult = $user->createToken($user->email.'-' .now(), ['*']);
            $success['token'] =    $tokenResult->accessToken;
            return response()->json(['success' => $success], 200);
        } */
    /*
        Writen By Bashar Anjroo.

         * ######## SignUp Admin ##########

         * @param  [string] name
         * @param  [string] email
         * @param  [string] password
         * @param  [string] password_confirmation
         * @return [string] message
         */
    public function UserRegister(SignUpRequest $request)
    {
         // create new user :
         $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)

        ]);

        $user->save();
        $user->roles()->sync(4);
        // return message api user
        $success['name'] = $user->name;
        $tokenResult = $user->createToken($user->email.'-' .now(), ['Customer']);
        $success['token'] =    $tokenResult->accessToken;
        $verifyUser = VerifyUser::create([
            'user_id' => $user->id,
            'token' => $tokenResult->accessToken
          ]);
          Mail::to($user->email)->send(new verification($user));
          Auth::logout();
        return response()->json(['success' => $success ,
        'status' =>'We sent you an activation code. Check your email and click on the link to verify.']
        , 200);
    }
    public function verifyUser($token)
    {
      $verifyUser = VerifyUser::where('token', $token)->first();
      if(isset($verifyUser) ){
        $user = $verifyUser->user;
        if(!$user->verified) {
          $verifyUser->user->verified = 1;
          $verifyUser->user->save();
          $status = "Your e-mail is verified. You can now login.";
        } else {
          $status = "Your e-mail is already verified. You can now login.";
        }
      } else {
        return response()->json('warning', "Sorry your email cannot be identified.");
      }
      return response()->json('status', $status);
    }

    /**
     * written By Bashar Anjroo .
     * ####### Login Admin and create token ############
     *
     * @param  [string] email
     * @param  [string] password
     * @return [string] access_token
     * @return [string] token_type
     */
    public function login(LoginRequest $request)
    {
        // insert inputs [email,password]
        $email = request('email');
        $password = request('password');

        if (!Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        $role = $user->roles()->first()->title;
        if ($role == "Admin") {
        if ($user) {
            $userRole = $user->roles()->first();
            if ($userRole) {
                $this->scope = $userRole->title;
            }
            if (Hash::check($request->password, $user->password)) {
                //$tokenResult = $user->createToken('Personal Access Token');
                $tokenResult = $user->createToken($user->email . '-' . now(), [$this->scope]);
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();
                $to_name = $user->name;
                $to_email = $user->email;
                $data = array('name'=>"Ogbonna Vitalis(sender_name)", 'body' => "A test mail");
                Mail::send([], [], function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                ->subject('Laravel Test Mail');
                $message->from('bashar.anjroo1995@gmail.com','Test Mail');
                });




                return response()->json([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                ]);
            } else {
                return response()->json(['message' => "Password mismatch"], 422);
            }
        } else {
            return response()->json(['message'  => 'User does not exist'], 422);
        }}
        return response()->json(['message'  => 'User does not exist'], 422);
    }

    /**
     * written By Bashar Anjroo .
     * ####### Login user and create token ############
     *
     * @param  [string] email
     * @param  [string] password
     * @return [string] access_token
     * @return [string] token_type
     */
    public function loginUser(LoginRequest $request)
    {
        // insert inputs [email,password]
        $email = request('email');
        $password = request('password');
        if (!Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = Auth::user();

        $role = $user->roles()->first()->title;
        if ($role == "Customer") {
            if (!$user->verified) {
                Auth::logout();
                return response()->json(['warning' =>'You need to confirm your account. We have sent you an activation code, please check your email.']);
              }
        if ($user) {
            $userRole = $user->roles()->first();
            if ($userRole) {
                $this->scope = $userRole->title;
            }
            if (Hash::check($request->password, $user->password)) {
                //$tokenResult = $user->createToken('Personal Access Token');
                $tokenResult = $user->createToken($user->email . '-' . now(), [$this->scope]);
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();
                return response()->json([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                ]);
            } else {
                return response()->json(['message' => "Password mismatch"], 422);
            }
        } else {
            return response()->json(['message'  => 'User does not exist'], 422);
        }}
        return response()->json(['message'  => 'User does not exist'], 422);
    }
    /**
     * Written Bashar Anjroo .
     * ###### Logout user (Revoke the token) ######
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out', 'loggedout' => true], 200);
    }
    /**
     * Written By Bashar Anjroo .
     *
     * ####### Get the authenticated User ######
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Written By Bashar Anjroo .
     *
     * ####### Get the authenticated User order by id  ######
     *
     * @return [json] user object
     */

    public function show(Request $request, $userId)
    {
        $user = User::find($userId);

        if ($user) {
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found!'], 404);
    }


    public function reset() {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["msg" => "Invalid token provided"], 400);
        }

        return response()->json(["msg" => "Password has been successfully changed"]);
    }

}
