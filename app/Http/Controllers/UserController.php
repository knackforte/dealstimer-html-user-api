<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserDetails;
use App\Role;
use DB;
use Validator;
use Exception;
use Image;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('role_user')->join('users', 'role_user.user_id', '=', 'users.id')->join('roles','roles.id', '=', 'role_user.role_id')->select('users.id','users.first_name','users.last_name','users.email','users.username','roles.display_name')->get(),200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'first_name'                 => 'required',
            'last_name'                  => 'required',
            'email'                      => 'required|email|unique:users',
            'password'                   => 'required|min:8|confirmed',
            'password_confirmation'      => 'required|min:8',
            'username'                   => 'required|unique:users'
        );
        $messages = array(
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
            'username.required' => 'Username is required.',
            'username.unique:users' => 'Entered Username already exists.',
            'password_confirmation.required' => 'Confim Password is required.',
            'email.email' => 'Invalid Email format.',
            'email.unique:users' => 'Entered Email already exists.',
            'password.min:8' => 'Password should contain minimum of 8 characters.',
            'password.confirmed' => 'Password and confirm password should match.',
            'password_confirmation.min:8' => 'Confirm Password should contain minimum of 8 characters.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors()->first(),400);
        }

        $role = $request->get("role");

        if($role === "user"){
            $user = new User;
            $user->first_name = $request->get("first_name");
            $user->last_name = $request->get("last_name");
            $user->email = $request->get("email");
            $user->password = Hash::make($request->get("password"));
            $user->username = $request->get("username");
            $role = Role::where('name','=','user')->first();
            if($user->save()){
                $user->attachRole($role);
                return response()->json($user,201);
            }else{
                return response()->json(["message" => "Error occurs while processing request!"],400);
            }
            
        }else if($role === "vendor"){
            $user = new User;
            $user->first_name = $request->get("first_name");
            $user->last_name = $request->get("last_name");
            $user->email = $request->get("email");
            $user->password = Hash::make($request->get("password"));
            $user->username = $request->get("username");

            DB::beginTransaction();
            try{
                $role = Role::where('name','=','vendor')->first();
                $user->save();
                $user->attachRole($role);
                $user_details = new UserDetails;
                $user_details->store_name = $request->get("store_name");
                $user_details->user_id = $user->id;
                if(!empty($request->get("cell_no"))){
                    $user_details->cell_no = $request->get("cell_no");
                }
                if(!empty($request->get("address"))){
                    $user_details->address = $request->get("address");
                }
                if(!empty($request->get("gender"))){
                    $user_details->gender = $request->get("gender");
                }
                if(!empty($request->get("store_url"))){
                    $user_details->store_url = $request->get("store_url");
                }
                if(!empty($request->get("country"))){
                    $user_details->country = $request->get("country");
                }
                if(!empty($request->get("state"))){
                    $user_details->state = $request->get("state");
                }
                if(!empty($request->get("city"))){
                    $user_details->city = $request->get("city");
                }
                if(!empty($request->get("zip"))){
                    $user_details->zip = $request->get("zip");
                }
                if(!empty($request->get("latitude"))){
                    $user_details->latitude = $request->get("latitude");
                }
                if(!empty($request->get("longitude"))){
                    $user_details->longitude = $request->get("longitude");
                }
                if(!empty($request->get("street_address"))){
                    $user_details->street_address = $request->get("street_address");
                }
                if($request->file('picture') != null)
                {
                    $image = $request->file('picture');
                    $filename = time().'.'.$image->getClientOriginalExtension();
                    $img = Image::make($image->getRealPath());
                    $destinationPath = public_path('/images');
                    $image->move($destinationPath, $filename);
                    $user_details->picture = $filename;
                }
                $user_details->save();
            }catch(Exception $e){
                DB::rollback();
                return response()->json(["data" => "Error occurs while processing request!"],400);
            }
            DB::commit();
            $returnUser = User::join('user_details', 'user_details.user_id', '=', 'users.id')
            ->select('users.*','user_details.*')
            ->where('users.id','=',$user->id)
            ->get()->first();
            return response()->json($returnUser,200);
        }
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(["message" => "Record not found!"],404);
        }
        if($user->hasRole('user')){
            $user = User::find($id);
            return response()->json($user,200);
        }else if($user->hasRole('vendor')){
            $returnUser = User::join('user_details', 'user_details.user_id', '=', 'users.id')
            ->select('users.*','user_details.*')
            ->where('users.id','=',$id)
            ->get()->first();
            return response()->json($returnUser,200);
        }
    }

    public function getApiStores(){
        return response()->json(DB::table('user_details')->where('api_enabled',1)->join('users', 'user_details.user_id', '=', 'users.id')->select('users.id','user_details.picture','user_details.store_name','user_details.store_url')->get(),200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(["message" => "Record not found!"],404);
        }
        if($user->hasRole('user')){
            $user->update($request->only('first_name','last_name','email','password'));
            return response()->json($user,200);
        }else if($user->hasRole('vendor')){
            $user->update($request->only('first_name','last_name','email','password'));
            $user_details = UserDetails::where('user_id',$id);
            $user_details->update($request->only('username','cell_no','address','gender','store_name','store_url','country','state','city','zip','latitude','longitude','street_address'));
            if(!is_null($request->picture)){
                $user_details->update(['picture' => $request->picture]);
            }
            $returnUser = User::join('user_details', 'user_details.user_id', '=', 'users.id')
            ->select('users.*','user_details.*')
            ->where('users.id','=',$id)
            ->get()->first();
            return response()->json($returnUser,200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(["message" => "Record not found!"],404);
        }
        $user->delete();
        return response()->json($user,204);
    }

    public function verifyUser(Request $request){
        $email = $request->email;
        $password = $request->password;
        $user = DB::table('users')->where("email","=",$email)->first();
        if (!is_null($user)) {
            if(Hash::check($password, $user->password)){
                return response()->json($user,200);
            }else{
                return response()->json(["message" => "Invalid Email or Password!"],400);
            }
        }
        else
        {
            return response()->json(["message" => "Record not found!"],404);
        }
    }
}
