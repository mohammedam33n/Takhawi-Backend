<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Auth;
use App\Models\Admin;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:admin-list', ['only' => ['index','store']]);
         $this->middleware('permission:admin-create', ['only' => ['create','store']]);
         $this->middleware('permission:admin-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:admin-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $admins = Admin::get();
        return view('backend.admins.index',compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('backend.admins.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
            'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'roles' => 'required'
        ]);

        if (!empty($request->pic)) {
            // Upload Image
            $imageName = time().'.'.$request->pic->extension();
            $image = $request->pic->move('backend/media', $imageName);
        }else {
            $image = '';
        }

        $admin = new Admin;
        $admin->pic = $image;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt ($request['password']);
        $admin->mobile = $request->mobile;
        $admin->gender = $request->gender;

        if ($admin->save()) {
            $admin->assignRole($request->input('roles'));

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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = Admin::find($id);

        $roles = Role::pluck('name','name')->all();
        $userRole = $admin->roles->pluck('name','name')->all();

        return view('backend.admins.edit',compact('admin','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = Admin::find($id);

        if ($admin->email == $request->email) {
            $request->validate([
                'name' => 'required|string|max:20',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'roles' => 'required'
            ]);
        }else {
            $request->validate([
                'name' => 'required|string|max:20',
                'email' => 'required|email|unique:admins,email',
                'password' => 'required|min:6',
                'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'roles' => 'required'
            ]);
        }


        if (!empty($request->pic)) {
            File::delete(public_path($admin->pic));

            // Upload Image
            $imageName = time().'.'.$request->pic->extension();
            $image = $request->pic->move('backend/media', $imageName);
        }else {
            $image = '';
        }

        if (empty($request->pic)) {
            $admin->pic = $admin->pic;
        }else {
            $admin->pic = $image;
        }
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt ($request['password']);
        $admin->mobile = $request->mobile;
        $admin->gender = $request->gender;

        if ($admin->save()) {
            $admin->assignRole($request->input('roles'));

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
        $admin = Admin::find($id);
        File::delete(public_path($admin->pic));

        if ($admin->delete()) {
            return redirect()->back()->with('success', 'deleted successfully');
        }else {
            return redirect()->back()->with('erorrs', ' Not Deleted');
        }
    }
}
