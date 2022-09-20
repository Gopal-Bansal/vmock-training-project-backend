<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
//use Tymon\JWTAuth\Facades\JWTAuth;



use Illuminate\Support\Facades\Auth;
use  App\Models\User;

class AuthController extends Controller
{  


    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout','showAllUsers','create']]);
        $this->middleware('auth:api', ['except' => ['login', 'emailVerify','logout','create','updateuser','deleteuser','showAllUsers']]);
        
    }

    public function create(Request $request)
    {

        
       

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        $data=$request->all();
        //print(json_encode($data));
        //$opts=["cost"=>15,"salt"=>'randomsaltherezdfghjsadf'];
       // $data['password'] = password_hash($data['password'],PASSWORD_BCRYPT,$opts);
       $data['password']= Hash::make($request->input('password'));
       $user = User::create($data); //data created in table

        

        

       // print(response()->json($user, 201));
        return response()->json($user, 201);
    }
    public function updateuser(Request $request,$id){
        //$userid=$request->id;
        $user=User::findOrFail($id);
        if($request->name){
            $user->name  = $request->name;
        $user->save();
        echo ($user->name);
          //  $user->update($request->only(['name']));
        }
        return response()->json($user);
    }
    public function deleteusers($id)
    {
        // dd($request->userID);
        $user = User::findOrFail($id);
        
        if ($user != null) {
            $user->status = 'deleted';
        }
        $user->save();
         return response($user, 200);
    }



    /**
  * Request an email verification email to be sent.
  *
  * @param  Request  $request
  * @return Response
  */
  







 
  public function emailRequestVerification(Request $request)
  {
    if ( $request->user()->hasVerifiedEmail() ) {
        return response()->json('Email address is already verified.');
    }
    
    $request->user()->sendEmailVerificationNotification();
    
    return response()->json('Email request verification sent to '. Auth::user()->email);
  }
  
/**
  * Verify an email using email and token from email.
  *
  * @param  Request  $request
  * @return Response
  */
  public function emailVerify(Request $request)
  {
    //print($request);
   // $this->validate($request, ['token' => 'required|string',]);
\Tymon\JWTAuth\Facades\JWTAuth::getToken();
    \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
if ( ! $request->user() ) {
        return response()->json('Invalid token', 401);
    }
    
    if ( $request->user()->hasVerifiedEmail() ) {
        return response()->json('Email address '.$request->user()->getEmailForVerification().' is already verified.');
    }
$request->user()->markEmailAsVerified();
return response()->json('Email address '. $request->user()->email.' successfully verified.');
  }
 //from email verification tutorial



   
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only(['email','password']);
        $token = Auth::attempt($credentials);
      

        if (! $token) {
            return response()->json(['message' => 'Unauthorized, not authorised'], 401);
        }
        return $this->respondWithToken($token);

        
    }
        
    
    

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
    public function showAllUsers(Request $request)
    {
        $user = DB::table('users');
        //filtering
        if(strtolower($request->role) === 'admin'){
            $user = DB::table('users')->where('role', '=', 'admin');
        } elseif(strtolower($request->role) === 'normal'){
            $user = DB::table('users')->where('role','=','normal');
        }
        if(strtolower($request->deleted_by) === '1'){
            $user = DB::table('users')->where('deleted_by','<>', 'active');
        }
        //sorting
        if(strtolower($request->sort) === 'name'){
            $user = DB::table('users')->orderBy('name', 'desc');
        } elseif(strtolower($request->sort) === 'email'){
            $user = DB::table('users')->orderBy('email', 'asc');
        } elseif(strtolower($request->sort) === 'created_at'){
            $user = DB::table('users')->orderBy('created_at', 'desc');
        }
        $user = $user->get();
       // return view('user.index', ['users' => $user]);
        return response()->json($user);
    }
    
    

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /*
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    */

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}