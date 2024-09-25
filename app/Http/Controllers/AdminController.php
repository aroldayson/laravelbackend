<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Laundrycategorys;
use App\Models\Payments;
use App\Models\Customers;
use App\Models\Expenses;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB; 


class AdminController extends Controller
{
    public function register(Request $request){
        $formField = $request->validate([
            'Admin_lname' => 'string|max:255',
            'Admin_fname' => 'string|max:255',
            'Admin_mname' => 'string|max:255',
            'Admin_image' => 'string|max:255',
            'Birthdate' => 'date',  
            'Phone_no' => 'string|max:15', 
            'Address' => 'string|max:255',
            'Role' => 'string|max:255',
            'Email' => 'required|email|max:255|unique:admins',
            'Password' => 'required|confirmed|min:8', 
        ]);
        
        $formField['Password'] = Hash::make($formField['Password']);

        Admin::create($formField);
        return  response()->json(['message' => 'User registered successfully!'], 201);
    }

    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'Email' => 'required|Email',
            'Password' => 'required'
        ]);

        // Find the user based on the email
        $user = Admin::where('Email', $request->Email)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        // Create a token for the authenticated user
        $token = $user->createToken($user->Admin_lname);

        // Return the token and user details
        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
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
    public function findstaff(Request $request, $id)
    {   
        $staff = Admin::find($id);
        
        if (is_null($staff)) {
            return response()->json(['message' => 'Staff not found'], 404);
        }

        return response()->json($staff, 200);

    }
    public function addstaff(Request $request)
    {
        $request->validate([
            'Admin_lname' => 'required|string|max:255',
            'Admin_fname' => 'required|string|max:255',
            'Admin_mname' => 'nullable|string|max:255',
            'Admin_image' => 'string',
            'Birthdate' => 'nullable|date',
            'Phone_no' => 'required|string|max:15',
            'Address' => 'required|string|max:255',
            'Role' => 'nullable|string|max:255',
            'Email' => 'required|email|max:255|unique:admins',
            'Password' => 'required|confirmed|min:6', 
        ]);

        $data = $request->all();
        $data['Password'] = bcrypt($request->Password);

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

    public function getUser(Request $request)
    {
        // Get user ID from query string
        $userId = $request->query('Admin_ID');
        
        $user = Admin::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }


    // pricemanagement
    public function pricedisplay(){
        return response()->json(Laundrycategorys::all(), 200);
    }
    public function addprice(Request $request)
    {
        $request->validate([
            'Category' => 'required|string',
            'Per_kilograms' => 'required|numeric',
        ]);

        DB::table('laundry_categorys')->insert([
            'Category' => $request->Category,
            'Per_kilograms' => $request->Per_kilograms,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staffList = DB::table('laundry_categorys')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $staffList,
        ], 201);
    }
    public function deletecateg(Request $request, $id){
        $pricecateg = Laundrycategorys::find($id);
        if(is_null($pricecateg)){
            return response()->json(['message' => 'Employee not Found'], 404);
        }
        $pricecateg->delete();
        return response()->json(null,204);

    }
    public function updateprice(Request $request, $id){
        $pricecateg = Laundrycategorys::find($id);
        if(is_null($pricecateg)){
            return response()->json(['message' => 'Laundrycategorys not Found'], 404);
        }
        $pricecateg->update($request->all());
        return response($pricecateg, 200);
    }
    public function findprice($id)
    {   
        $pricecateg = Laundrycategorys::find($id);
        
        if (is_null($pricecateg)) {
            return response()->json(['message' => 'Staff not found'], 404);
        }

        return response()->json($pricecateg, 200);
    }


    // DASHBOARD
    public function dashdisplays()
    {
        $payments = Payments::all();

        $totalAmount = $payments->sum('Amount');

        $totals = [
            'gcash' => 0,
            'cash' => 0,
            'bpi' => 0,
        ];

        $paymentsByMethod = [
            'gcash' => [],
            'cash' => [],
            'bpi' => [],
        ];

        foreach ($payments as $payment) {
            if (strtolower($payment->Mode_of_Payment) === 'gcash') {
                $totals['gcash'] += $payment->Amount;
                $paymentsByMethod['gcash'][] = $payment;
            } elseif (strtolower($payment->Mode_of_Payment) === 'cash') {
                $totals['cash'] += $payment->Amount;
                $paymentsByMethod['cash'][] = $payment;
            } elseif (strtolower($payment->Mode_of_Payment) === 'bpi') {
                $totals['bpi'] += $payment->Amount;
                $paymentsByMethod['bpi'][] = $payment;
            }
        }

        return response()->json([
            'payments' => $paymentsByMethod,
            'totals' => $totals,
            'total_amount' => $totalAmount
        ], 200);
    }
    public function expensendisplays(){
        // return response()->json(Expenses::all(), 200);

        $payments = Expenses::all();

        $totalAmount = $payments->sum('Amount');

        return response()->json([
            'total_amount' => $totalAmount,
            'expenses_det' =>  $payments
        ], 200);
    }


    // CUSTOMERS
    public function customerdisplay(){
        return response()->json(Customers::all(), 200);
    }
    public function findcustomer($id)
    {   
        $customer = Customers::find($id);
        
        if (is_null($customer)) {
            return response()->json(['message' => 'Staff not found'], 404);
        }

        return response()->json($customer, 200);
    }

}
