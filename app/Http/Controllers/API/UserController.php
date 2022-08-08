<?php

namespace App\Http\Controllers\API;

use Session;
use App\Models\User;
use App\Events\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\{Hash, Log, Config};
use App\Http\Controllers\Controller;
use App\Http\Requests\{RegistrationUser, Login, CreateUser, UpdateUser};
use App\Traits\ApiResponser;

class UserController extends Controller
{
    use ApiResponser;
    /**
     * Register
     */
    public function register(RegistrationUser $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            event(new SendMail($user));

            return $this->success(Config::get('constants.USER.SIGNUP_SUCCESS'), 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            Log::error($ex->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Login
     */
    public function login(Login $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            return $this->success(Config::get('constants.USER.USER_LOGIN_SUCCESS'), 200);
        } else {
            return $this->error(Config::get('constants.USER.USER_LOGIN_FAIL'), 200);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        try {
            Session::flush();
            return $this->success(COnfig::get('constants.USER.USER_LOGOUT_SUCCESS'), 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            Log::error($ex->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $users = User::withCount('post')
                ->where('type', 'User')
                ->get();
            return $this->success(Config::get('constants.USER.USER_LIST'), 200, $users);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(CreateUser $request)
    {
        try{
            $input_array = $request->all();
            $input_array['password'] = Hash::make($request->password);
            $user = User::create($input_array);
            return $this->success(Config::get('constants.USER.USER_ADD_SUCCESS'), 200, $user);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * edit the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $user = User::find($id);
            return $this->success(Config::get('constants.USER.USER_DETAILS'), 200, $user);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateUser $request)
    {
        try{
            $user = User::find($id);
            $input_array = $request->all();
            if($request->password){
                $input_array['password'] = Hash::make($request->password);  
            }
            $user->update($input_array);
            return $this->success(Config::get('constants.USER.USER_EDIT_SUCCESS'), 200, $user);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try{
            $user = User::find($id);
            $user->delete();
            return $this->success(Config::get('constants.USER.USER_DELETE_SUCCESS'), 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
       
    }
}
