<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
{
    public function register(Request $request){
        $formField = $request->validate([
            'admin_lname' => 'string|max:255',
            'admin_fname' => 'string|max:255',
            'admin_mname' => 'string|max:255',
            'admin_image' => 'string|max:255',
            'birthdate' => 'date',  
            'phone_no' => 'string|max:15', 
            'address' => 'string|max:255',
            'role' => 'string|max:255',
            'email' => 'required|email|max:255|unique:admins',
            'password' => 'required|confirmed|min:8', 
        ]);
        
        $formField['password'] = Hash::make($formField['password']);

        Admin::create($formField);
        return  response()->json(['message' => 'User registered successfully!'], 201);
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
    
        $user = Admin::where('email', $validated['email'])->first();
    
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        $token = $user->createToken($user->admin_lname);
    
        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
    
        return response()->json([
            'message' => 'You are logged out'
        ], 200);
    }

    public function display(){
        return response()->json(Admin::all(), 200);
    }
    public function findstaff($id){   
        $staff = admin::find($id);
        if(is_null($staff)){
            return response()->json(['message' => 'Staff not Found'], 404);
        }
        return response()->json($staff::find($id),200);
    }
    public function addstaff(Request $request)
    {
        $request->validate([
            'admin_lname' => 'required|string|max:255',
            'admin_fname' => 'required|string|max:255',
            'admin_mname' => 'nullable|string|max:255',
            'admin_image' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'phone_no' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:admins',
            'password' => 'required|confirmed|min:6', 
        ]);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);

        $staff = Admin::create($data);

        $staffList = Admin::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Staff added successfully',
            'staff' => $staff,
            'staffList' => $staffList
        ], 201);
    }

    public function updatestaff(Request $request, $id){
        $staff = admin::find($id);
        if(is_null($staff)){
            return response()->json(['message' => 'Employee not Found'], 404);
        }
        $staff->update($request->all());
        return response($staff, 200);

    }
    public function deletestaff(Request $request, $id){
        $staff = admin::find($id);
        if(is_null($staff)){
            return response()->json(['message' => 'Employee not Found'], 404);
        }
        $staff->delete();
        return response()->json(null,204);

    }
}
