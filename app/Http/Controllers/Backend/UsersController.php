<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackEnd\BadWordController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\User;

class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user-list', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::get();
        return view('backend.users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('backend.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'required|min:6',
            'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            "birth_date"=> ['nullable', 'date', 'before:today'],
            'level_id' => 'required',
        ]);

        if (!empty($request->pic)) {
            // Upload Image
            $imageName = time().'.'.$request->pic->extension();
            $image = $request->pic->move('backend/media', $imageName);
        }else {
            $image = '';
        }

        $user = new User;
        $user->name = $request->name;
        $user->level_id = $request->level_id;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->pic = $image;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->country = $request->country;
        $user->token = sha1(uniqid());
        $user->user_status = 'Offline';
        $user->password = bcrypt($request['password']);

        if ($user->save()) {

            BadWordController::checkBadWordsReport('users',$user->id,$user->name,"");

            return back()->with('success', 'Created successfully.');
        }else {
            return back()->with('errors', 'Not created successfully.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function roomsByInvitation(string $id)
    {
        $invitation = RoomInvitation::where('from_user', $id)->get();
        $invit = 1;
        return view('backend.users.roomsInvitation',compact('invitation', 'invit'));
    }

    /**
     * Display the specified resource.
     */
    public function roomsFromInvitation(string $id)
    {
        $invitation = RoomInvitation::where('to_user', $id)->get();
        $invit = 0;
        return view('backend.users.roomsInvitation',compact('invitation', 'invit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $levels = Level::get();
        return view('backend.users.edit',compact('user', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if ($user->email == $request->email || $user->mobile == $request->mobile) {
            $user->email = $user->email;
            $user->mobile = $user->mobile;

            $request->validate([
                'name' => 'required|string|max:20',
                'password' => 'required|min:6',
                'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                "birth_date"=> ['nullable', 'date', 'before:today'],
            ]);
        }else {
            $request->validate([
                'name' => 'required|string|max:20',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|unique:users,mobile',
                'password' => 'required|min:6',
                'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                "birth_date"=> ['nullable', 'date', 'before:today'],
                'level_id' => 'required',
            ]);
        }


        if (!empty($request->pic)) {
            File::delete(public_path($user->pic));

            // Upload Image
            $imageName = time().'.'.$request->pic->extension();
            $image = $request->pic->move('backend/media', $imageName);
        }else {
            $image = $user->pic;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->level_id = $request->level_id;
        $user->mobile = $request->mobile;
        $user->pic = $image;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->country = $request->country;
        $user->token = sha1(uniqid());
        $user->user_status = 'Offline';
        $user->password = bcrypt($request['password']);

        if ($user->save()) {
            BadWordController::checkBadWordsReport('users',$user->id,$user->name,"");

            return back()->with('success', 'Updated successfully.');
        }else {
            return back()->with('errors', 'Not updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        File::delete(public_path($user->pic));

        // condtion delete by report bad words
        if (empty($comment)) {
            $RBW = ReportBadWord::where('module_id', $id)->first();
            if (!empty($RBW)) {
                $RBW->delete();
                return redirect()->back()->with('success', 'deleted successfully');
            }else {
                return redirect()->back()->with('erorrs', ' Not Deleted');
            }
        }

        $RBW = ReportBadWord::where('module_id', $id)->first();
        if (!empty($RBW)) {
            $RBW->delete();
        }

        if ($user->delete()) {
            return redirect()->back()->with('success', 'deleted successfully');
        }else {
            return redirect()->back()->with('erorrs', ' Not Deleted');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delRoomsInvitation(string $id)
    {
        $invitation = RoomInvitation::find($id);
        if ($invitation->delete()) {
            return redirect()->back()->with('success', 'deleted successfully');
        }else {
            return redirect()->back()->with('erorrs', ' Not Deleted');
        }
    }
}
