<?php

namespace App\Http\Controllers\Api;

use App\Models\black_list_user;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BackEnd\BadWordController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use \App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Laravel\Sanctum\PersonalAccessToken;
use DB;

class UserController extends Controller
{
    function __construct() {
        if ( $rst = $this->testHeaders() ) {
            return $rst;
        }
    }

# ##########################################################
    /**
     * test , playground, check is file has no errors
     *
     * @return  array
     */
    function test() {
        return ["hello from"=>"test function", "time now is"=>date('Y-m-d H:i:s')];
    }
# ##########################################################


    /**
     * list all users
     *
     * @return  array user list
     */
    public function index()
    {
        return User::get();
    }
# ##########################################################
public function searchUsers(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "search"=> "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code'    => 1,
                'message' => $validator->messages()
            ], 422);
        }

        try {
            $users = User::search($request->search)->get();

            return response()->json([
                'data'          => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $e->getMessage()
            ], 500);
        }
    }
# ##########################################################

    /**
     * Store user data by phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception if an error occurs while storing the image or creating the user
     */
    public function storeByPhone(Request $request)
    {

        $request['token'] = sha1(uniqid());
        $request['user_status'] = 'Online';

        $validator = Validator::make(request()->all(), [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => 'required|unique:users,mobile',
            'pic' => 'nullable|image|max:2048', // max size of 2MB
            'password' => 'required|string|min:8',
            'country' => 'nullable|max:255',
            'gender' => 'nullable',
            'token' => 'nullable',
            'user_status' => 'nullable',
            "birth_date"=> ['nullable', 'date', 'before:today'],
            // 'otp' => 'required|integer|digits_between:3,8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $validator->messages()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();
            $validatedData['password'] = bcrypt($validatedData['password']);

            if (request()->hasFile('pic')) {
                $validatedData['pic'] = request()->file('pic')->store('public/users');
            }

            $user = User::create($validatedData);
            // $this->generateOtp($user->id, request('otp'));
            $this->loginById($user->id);

            $user->pic = asset($user->pic);

            // Get Bad Words
            $bad_words = BadWordController::checkBadWordsReport('User',$user->id,$user->name);

            return response()->json([
                'token'         => $user->createToken('app')->plainTextToken,
                'error_code'    => 0,
                'data'          => $user,
                'message'       => [],
                'reported' => $bad_words
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => [$e->getMessage()]
            ], 500);
        }
    }
# ##########################################################
    /**
     * Login or register a user using Facebook credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByFacebook(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'numeric'],
            'name' => ['required', 'string'],
            'pic' => ['nullable', 'string'],
        ]);

        // If neither email nor phone is provided, return an error response
        if (!$request->has('email') && !$request->has('mobile')) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Either email or mobile is required']
            ], 400);
        }
        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $validator->messages()
            ], 400);
        }

        // Find the user with the given email or mobile number
        $user = null;
        if ($request->has('email')) {
            if ( User::where(['email'=>$request->email])->where([["login_by",'!=', 'facebook']])->first() ) {
                return response()->json([
                    'error_code'    => 1,
                    'data'          => [],
                    'message' => ["email exists. are you trying to login by google?"]
                ], 400);
            }
            $user = User::where(['email'=> $request->email,'login_by' => 'facebook'])->first();
        } elseif ($request->has('mobile')) {
            if ( User::where(['mobile'=>$request->mobile])->first() ) {
                return response()->json([
                    'error_code'    => 0,
                    'data'          => [],
                    'errors' => ["mobile exists. are you trying to login by google?"]
                ], 400);
            }
            $user = User::where(['mobile'=>$request->mobile,'login_by' => 'facebook'])->first();
        }

        // If the user doesn't exist, create a new user
        if (!$user) {
            $user = User::create([
                'name'                => $request->name,
                'email'               => $request->email,
                'mobile'              => $request->mobile,
                'pic'                 => asset($request->pic),
                "email_verified_at"   => now() ,
                'login_by'            => 'facebook',
            ]);
            $status = 201; // Created
        } else {
            $user->update([ "pic" => $request->pic] );
            $status = 200; // OK
        }

        // Generate a new token for the user
        $token = $user->createToken('app')->plainTextToken;

        // Add token and update status user
        $user->update(['token'=> sha1(uniqid()), 'user_status'=>'Online' ]);
        $user->pic = asset($user->pic);
        UserController::_updateLastSeen($user->id);
        // Return a success response with the user data and token
        return response()->json([
            'token' => $token,
            'error_code'    => 0,
            'data'          => $user,
            'message'          => [],
        ], $status);
    }
# ##########################################################
    /**
     * Login or register a user using Google credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByGoogle(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'email' => ['nullable', 'email'],
            'name' => ['required', 'string'],
            'pic' => ['nullable', 'string'],
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $validator->messages()
            ], 400);
        }

        // Find the user with the given email
        $user = User::where(['email'=>$request->email,'login_by' => 'google'])->first();

        // If the user doesn't exist, create a new user
        if (!$user) {
            if ( User::where(['email'=>$request->email])->first() ) {
                return response()->json([
                    'error_code'    => 1,
                    'data'          => [],
                    'message' => ["email exists. are you trying to login by facebook?"]
                ], 400);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'pic' => asset($request->pic),
                'login_by' => 'google',
                'email_verified_at' => now(),
            ]);
            $status = 201; // Created
        } else {
            $status = 200; // OK
        }

        // Generate a new token for the user
        $token = $user->createToken('app')->plainTextToken;

        // Add token and update status user
        $user->update(['token'=> sha1(uniqid()), 'user_status'=>'Online' ]);
        $user->pic = asset($user->pic);

        UserController::_updateLastSeen($user->id);
        // Return a success response with the user data and token
        return response()->json([
            'token'         => $token,
            'error_code'    => 0,
            'data'          => $user,
            'message'       => [],
        ], $status);
    }
# ##########################################################

    /**
     * store new user
     *
     * @return  array new created user
     */
    public function store()
    {
        // dd(request());
        // Validate the request data
        $valid = Validator::make(request()->all(),[
            'name' => ['required','string','max:255'],
            'mobile' => 'nullable',
            'email' => 'string|nullable|email|max:255|unique:users',
            'country' => 'required|max:255',
            'gender' => 'required',
            'password' => 'required|string|min:8',
        ]);
        if($valid->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                "message"=>$valid->messages(),
                "header_code"=>403
            ], 403);
        }else{
            $attr= request()->validate([
                'name' => ['required','string','max:255'],
                'mobile' => 'nullable',
                'email' => 'nullable|email|max:255|unique:users',
                'country' => 'required|max:255',
                'gender' => 'required',
                'password' => 'required|string|min:8',
            ]);
            $attr['password']= bcrypt ($attr['password']) ;
            // Create a new user instance
            $user= User::create($attr);
            UserController::_updateLastSeen($user->id);
            // login user
            /*if ( $attr['mobile'] != null ) {
                $this->generateOtp($user->id);
            }*/
            $this->loginById($user->id);
            return response()->json([
                'error_code'    => 0,
                'data'          => $user,
                'message'       => [],
                "header_code"   =>201
            ], 201);
        }
    }
# ##########################################################

/**
 * Update the authenticated user's data.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */
public function updateUserData(Request $request)
{
    $valid = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'mobile' => 'required|string|max:255',
        'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'password' => 'nullable|string|min:6',
        'level_id' => 'required|sometimes|exists:levels,id',
        'country' => 'nullable|string|max:255',
        'gender' => 'nullable|string|max:255',
        'birth_date' => 'nullable|date',
    ]);

    if ($valid->fails()) {
        return response()->json([
            'error_code'    => 1,
            'data'          => [],
            'message' => $valid->messages(),
        ], 403);
    }

    $user = Auth::user();
    $user->name = $request->input('name');
    $user->mobile = $request->input('mobile');
    $user->country = $request->input('country');
    $user->gender = $request->input('gender');
    $user->birth_date = $request->input('birth_date');
    $user->level_id = $request->input('level_id');

    if ($request->hasFile('pic')) {
        $pic = $request->file('pic');
        $filename = time() . '_' . $pic->getClientOriginalName();
        $pic->move(public_path('users'), $filename);
        $user->pic = $filename;
    } elseif (!$request->has('pic')) {
        $user->pic = $user->old_pic;
    }

    if ($request->has('password')) {
        $user->password = Hash::make($request->input('password'));
    }

    $user->save();

    // Get Bad Words
    $bad_words = BadWordController::checkBadWordsReport('User',$user->id,$user->name);

    $user->pic = asset($user->pic);
    UserController::_updateLastSeen($user->id);
    return response()->json([
        'error_code'    => 0,
        'data'          => $user,
        'message' => ['User data updated successfully',],
        'reported' => $bad_words
    ], 201);
}
# ##########################################################
    /**
     * Update the authenticated user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserImage(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'pic' => 'required|image|max:2048',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $valid->messages(),
            ], 403);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => 'Invalid token',
            ], 401);
        }

        if (!empty($request->pic)) {
            File::delete(public_path($user->pic));
            // Upload pic
            $picName = time().'.'.$request->pic->extension();
            $pic = $request->pic->move('users', $picName);
        }else {
            $pic = $user->pic;
        }

        $user->pic = $pic;

        $user->save();
        $user->pic = asset($user->pic);
        UserController::_updateLastSeen($user->id);
        return response()->json([
            'error_code'    => 0,
            'data'          => $user,
            'message' => ['User image updated successfully',]
        ], 201);
    }
# ##########################################################



    /**
     * destroy (remove) user by ID
     *
     * @param   int  $id  user.id
     *
     * @return  array    message
     */
    public function destroy(int $id)
    {
        // Find the user by id
        $user = User::find($id);

        // Check if the user exists
        if ($user) {
            // Delete the user
            $user->delete();

            // Return a success message
            return response()->json([
                'error_code'    => 0,
                'data'          => $user,
                'message' => ['User deleted']
            ], 200);
        } else {
            // Return an error message
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['User not found']
            ], 404);
        }
    }
# ##########################################################


    /**
     * display user data
     *
     * @param   int  $id  user.id
     *
     * @return  object    user data
     */
    function show (int $id) {
        if ( $this->_isBlaskListed($id) )
        {
            $logged_id= auth()->user()->id;
            $logged_block= black_list_user::where(["from_user"=>$id, "to_user"=>$logged_id])->count();
            $current_block= black_list_user::where(["to_user"=>$id, "from_user"=>$logged_id])->count();
            $block= [
                "logged_block"=> $logged_block?true:false,
                "current_block"=> $current_block?true:false,
                "block_status"=> ($current_block || $logged_block)?true:false,
                "block_both"=> ($current_block && $logged_block)?true:false,
            ];
            return response()->json([
                'error_code'    => 1,
                "data"=> User::select(["id","name","pic"])->where("id",$id)->first(),
                'message' => "blocked",
                "blocked"=> $block,
                "blocked_messages"=>[
                    "logged_block"=> $logged_block?"logged in user blocks current user":"",
                    "current_block"=> $current_block?"current user blocks logged in":"",
                    "block_status"=> $block["block_status"]?true:false,
                    "block_both"=> $block["block_both"]?"both logged in and current user blocked each other":"",
                ]
            ], 404);
        }
        $logged_user_id= Auth::user()->id;
        return User::findOrFail($id)
            -> with([
                'createdByRooms',
                'roomGoins',
                'roomFolloing',
                'user_level',
                'user_badge',
                'posts',
                'likesPosts',
                'comments',
                'likesComments',
                'frindRequestTo',
                'frindRequestFrom',
                'frindFollowTo',
                'frindFollowFrom',
                'subCategoriesSubscripes',
                'black_list',
                'giftsTo',
                'giftsFrom',
            ])
            ->select([
                "users.*",
                DB :: RAW ("(select count(*) FROM friend_follows WHERE to_user='".$id."') as follow_count"),
                DB :: raw ("(select COUNT(*) FROM friend_requests WHERE is_accepted='1' AND to_user='".$id."') friend_count"),
                DB :: raw ("(
                    SELECT
                        if(is_accepted=1,true,false)
                    FROM
                        friend_requests
                    WHERE
                        (from_user='". $id ."' AND to_user='".$logged_user_id."') ||
                        (from_user='". $logged_user_id ."' AND to_user='".$id."')
                    ) AS is_friend"),
                DB :: raw ("(select COUNT(*) FROM friend_follows WHERE from_user='". $id ."' OR to_user='".$id."') is_followed"),
                DB :: raw("1 AS user_level"),
                DB :: raw("'badge' AS user_badge"),
                DB :: raw("'[{\"wallet_name\":\"wallet_1\", \"coins\": 1250},{\"wallet_name\":\"wallet_2\", \"coins\": 153}]' AS user_wallets"),
                // DB :: raw("'[{\"gift_name\":\"gift_1\", \"from_user_id\": 1,\"from_user_name\":\"user2\",\"gift_date\":\"2023-06-02\"}]' AS user_gifts")
            ])
            -> where('id', $id)
            -> get();
    }
# ##########################################################
    /**
     * get current loggedin user
     *
     * @return  array  userdata
     */
    function getCurrentUser( Request $request ) {

        $user = User::
        with([
            'createdByRooms',
            'roomGoins',
            'roomFolloing',
            'posts',
            'likesPosts',
            'comments',
            'likesComments',
            'frindRequestTo',
            'frindRequestFrom',
            'frindFollowTo',
            'frindFollowFrom',
            'subCategoriesSubscripes'
        ])
        -> where('id', auth()->user()->id)
        -> first();

        if (!$user) {
            UserController::_updateLastSeen($user->id);
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Invalid token'],
            ], 401);
        }
        return response()->json([
            'error_code'    => 0,
            'data'          => $user,
            'message'       => []
        ]);
    }
    # ##########################################################
    /**
     * get current loggedin user
     *
     * @return  array  userdata
     */
    function friendCurrent( Request $request ) {

        $user = User::
        with([
            'friendCurrent',
        ])
        -> where('id', auth()->user()->id)
        -> first();

        if (!$user) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Invalid token'],
            ], 401);
        }
        return response()->json([
            'error_code'    => 0,
            'data'          => $user,
            'message'       => []
        ]);
    }
    # ##########################################################
    /**
     * get current loggedin user
     *
     * @return  array  userdata
     */
    function requestsToFriends( Request $request ) {

        $user = User::
        with([
            'requestsToFriends',
        ])
        -> where('id', auth()->user()->id)
        -> first();

        if (!$user) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Invalid token'],
            ], 401);
        }
        return response()->json([
            'error_code'    => 0,
            'data'          => $user,
            'message'       => []
        ]);
    }
    # ##########################################################
    /**
     * get current loggedin user
     *
     * @return  array  userdata
     */
    function requestsFromFriends( Request $request ) {

        $user = User::
        with([
            'requestsFromFriends',
        ])
        -> where('id', auth()->user()->id)
        -> first();

        if (!$user) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Invalid token'],
            ], 401);
        }
        return response()->json([
            'error_code'    => 0,
            'data'          => $user,
            'message'       => []
        ]);
    }
# ##########################################################
    /**
     * login user by id, useful when login by facebook,twitter,appleID
     *
     *
     * @method POST
     * @param int id user.id
     * @param string _token
     *
     */
    public function appLoginById()
    {
        $id= request('id');
        if ( ! $id ) return response()->json([
            'error_code'    => 1,
            'data'          => [],
            "message"=> ["missing id",],
            "header_code"=>401
        ], 401);
        if ( $this->loginById($id) ) {
            UserController::_updateLastSeen($id);
            return $this->getCurrentUser();
        }
        return response()->json([
            'error_code'    => 1,
            'data'          => [],
            "message"=> ["not logged in.invalid id"],
            "header_code"=>401
        ], 401);
    }
# ##########################################################
    /**
         * Authenticate a user using email and password.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\JsonResponse
         */
    public function loginEmail(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|string',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $valid->messages(),
            ], 403);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();

            $token = $user->createToken('app')->plainTextToken;

            // Add token and update status user
            Auth::user()->update(['token'=> sha1(uniqid()), 'user_status'=>'Online' ]);

            UserController::_updateLastSeen($user->id);

            return response()->json([
                'token'         => $token,
                'error_code'    => 0,
                'data'          => $user,
                'message'       => []
            ], 201);
        } else {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Invalid email or password'],
            ], 403);
        }
    }
# ##########################################################
    /**
     * Authenticate a user using mobile number and password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByMobile(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'mobile' => 'required|string|max:255|exists:users,mobile',
            'password' => 'required|string',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => $valid->messages(),
            ], 403);
        }

        $mobile = $request->input('mobile');
        $password = $request->input('password');

        $guard = Auth::guard('web');

        if ($guard->attempt(['mobile' => $mobile, 'password' => $password])) {
            $user = $guard->user();
            $token = $user->createToken('app')->plainTextToken;

            // Add token and update status user
            Auth::user()->update(['token'=> sha1(uniqid()), 'user_status'=>'Online' ]);
            $user->pic = asset($user->pic);
            UserController::_updateLastSeen($user->id);
            return response()->json([
                'token'         => $token,
                'error_code'    => 0,
                'data'          => $user,
                'message'       => []
            ], 201);
        } else {
            return response()->json([
                'error_code'    => 1,
                'data'          => [],
                'message' => ['Invalid mobile number or password'],
            ], 403);
        }
    }
    # ##########################################################
    /**
     * Authenticate a user using mobile number and password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Add token and update status user
        Auth::user()->update(['user_status'=>'Offline']);

        auth()->user()->currentAccessToken()->delete();
        return response()->json([
            'error_code'    => 0,
            'data'          => [],
            'message' => ['Logged Out']
        ], 200);
    }
# ##########################################################

/**
     * test headers
     *
     * @return  void
     */
    private function testHeaders() {
        $headers = request()->header();
        //406 Not Acceptable
            // The requested resource is capable of generating only content not acceptable according
            // to the Accept headers sent in the request
        if ( ! array_key_exists("app-name",$headers)  || ! array_key_exists("app-key",$headers) ) {
            header('Content-type: text/json');
            http_response_code(406);
            echo json_encode([
                'error_code'    => 1,
                'data'          => [],
                "message"=>["KEYS ARE MISSING"],
                "header_code"=>406
            ], 406);
            die;
        }elseif(
            current($headers['app-name']) != env('APP_NAME') ||
            current($headers['app-key'])!= env('APP_KEY')
        ) {
            header('Content-type: text/json');
            http_response_code(406);
            echo json_encode([
                'error_code'    => 1,
                'data'          => [],
                "message"       => ["WRONG KEY VALUES"],
                "header_code"   => 406
            ], 406);
            die;
        }
    }
# ##########################################################

    /* **********************************************************
       * [PRIVATE METHODS ] **************************************
       ***********************************************************
    */
    private function loginById( int $id ) {
        $user= User::find($id); if ( $user === null ) return false;

        Auth::loginUsingId($id);
        return response()->json([
            'error_code'    => 0,
            'data'          => Auth::check(),
            'message' => []
        ], 200);
    }

    # ##########################################################
    private function getTokenFromRequest( ) {
        return request('token');
    }
    # ##########################################################
    /**
     * Retrieve the user associated with the given access token.
     *
     * @param string $token The access token string.
     * @return User|null The user associated with the token, or null if the token is invalid or the user cannot be found.
     * @throws InvalidArgumentException If the $token parameter is empty or not a string.
    */
    function _getUserByToken(string $token): ?User
    {
        // Check if the token parameter is empty or not a string
        if (empty($token)) {
            throw new InvalidArgumentException('Token parameter cannot be empty.');
        }

        // Find the token object corresponding to the given token
        $accessToken = PersonalAccessToken::findToken($token);

        // If the token is invalid, return null
        if (!$accessToken) {
            return null;
        }

        // Get the user associated with the token
        $tokenable = $accessToken->tokenable;

        // If the tokenable property is not an instance of the User class, return null
        if (!$tokenable instanceof User) {
            return null;
        }

        // Return the user associated with the token
        return $tokenable;
    }
    # ##########################################################
    private static function _updateLastSeen ($user_id) {
        $r= User::where(['id'=> $user_id])->update(["last_seen"=> \Carbon\Carbon::now()]);
    }

    # ##########################################################
    private function _isBlaskListed(int $id)
    {
        return black_list_user::
            where("from_user",$id)
            // orWhere("to_user",$id)
            ->count();
    }
    # ##########################################################
}
