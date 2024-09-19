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
        
        // Hashing the password before storing
        $formField['password'] = Hash::make($formField['password']);

        Admin::create($formField);
        return  response()->json(['message' => 'User registered successfully!'], 201);
    }

    public function login(Request $request) {
        // Validate input
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
    
        // Find the admin by email
        $user = Admin::where('email', $validated['email'])->first();
    
        // Check if user exists and password is correct
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }
    
        // Create a token for the user
        $token = $user->createToken($user->admin_lname);
    
        // Return the user object and token
        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken // Ensure token is returned properly
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
            'password' => 'required|confirmed|min:6', // Ensure password and confirmation are present
        ]);

        // Hash the password
        $data = $request->all();
        $data['password'] = bcrypt($request->password);

        // Create staff record
        $staff = Admin::create($data);

        return response()->json(['message' => 'Staff added successfully', 'staff' => $staff], 201);
    }



    public function updatestaff(Request $request, $id){
        $staff = admin::find($id);
        if(is_null($staff)){
            return response()->json(['message' => 'Employee not Found'], 404);
        }
        $staff->update($request->all());
        return response($response, 200);

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
