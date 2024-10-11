<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Laundrycategorys;
use App\Models\Payments;
use App\Models\Customers;
use App\Models\Expenses;
use App\Models\Transactions;
use App\Models\Cashdetails;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function register(Request $request)
    {
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
    public function logout(Request $request) 
    {
        $request->user()->tokens()->delete();
    
        return response()->json([
            'message' => 'You are logged out'
        ], 200);
    }

    // STAFF
    public function displaystaff(){
        // return response()->json(Admin::all(), 200);
        return response()->json(Admin::orderBy('Admin_ID', 'desc')->get(), 200);
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
    public function updateProfileImage(Request $request, $id)
    {
        $request->validate([
            'Admin_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        $admin = Admin::findOrFail($id);
    
        if ($request->hasFile('Admin_image')) {
            if ($admin->Admin_image) {
                Storage::delete('public/profile_images/' . $admin->Admin_image);
                
                $htdocsImagePath = 'C:/xampp/htdocs/admin/profile_images/' . $admin->Admin_image;
                if (file_exists($htdocsImagePath)) {
                    unlink($htdocsImagePath);
                }
            }
    
            $extension = $request->Admin_image->extension();
            $imageName = time() . '_' . $admin->Admin_ID . '.' . $extension;
            $request->Admin_image->storeAs('public/profile_images', $imageName);
    
            $htdocsPath = 'C:/xampp/htdocs/admin/profile_images'; 
    
            if (!file_exists($htdocsPath)) {
                mkdir($htdocsPath, 0777, true);
            }
    
            $request->Admin_image->move($htdocsPath, $imageName);
    
            $admin->Admin_image = $imageName;
            $admin->save();
    
            return response()->json([
                'message' => 'Profile image updated successfully',
                'image_url' => asset('profile_images/' . $imageName) 
            ], 200);
        }
    
        return response()->json(['message' => 'No image file uploaded'], 400);
    }
    



    // pricemanagement
    public function pricedisplay()
    {
        // return response()->json(Laundrycategorys::all(), 200);
        return response()->json(Laundrycategorys::orderBy('Categ_ID', 'desc')->get(), 200);
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
    public function deletecateg(Request $request, $id)
    {
        $pricecateg = Laundrycategorys::find($id);
        if(is_null($pricecateg)){
            return response()->json(['message' => 'Employee not Found'], 404);
        }
        $pricecateg->delete();
        return response()->json(null,204);

    }
    public function updateprice(Request $request, $id)
    {
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
    public function displaystaffs(){
        // return response()->json(Admin::all(), 200);
        return response()->json(Admin::orderBy('Admin_ID', 'desc')->get(), 200);
    }
    public function cashinitial(Request $request)
    {
        // Validate the request input
        $request->validate([
            'Staff_ID' => 'required|string',
            'Initial_amount' => 'required|numeric|min:0',
        ]);

        DB::table('cashed')->insert([
            'Staff_ID' => $request->Staff_ID,
            'Initial_amount' => $request->Initial_amount,
            'Fund_status' => 'Pending',
            'Datetime_Changefund' => now(),
        ]);

        $staffList = DB::table('cashed')->orderBy('Datetime_Changefund', 'desc')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $staffList,
        ], 201);
    }
    public function remittance(Request $request)
    {
        // Validate the request input
        $request->validate([
            'Admin_ID' => 'required|string',
            'Remitance' => 'required|numeric|min:0',
        ]);

        DB::table('cashed')->insert([
            'Admin_ID' => $request->Admin_ID,
            'Remitance' => $request->Remitance,
            'Fund_status' => 'Pending',
            'Datetime_Remitance' => now(),
        ]);

        $staffList = DB::table('cashed')->orderBy('Datetime_Remitance', 'desc')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $staffList,
        ], 201);
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

    // TRANSACTIONS
    public function Transadisplay()
    {
        $price = TransactionDetails::all();

        $totalprice = $price->sum('Price');

        $data = Transactions::join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
        ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
        ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
        ->select(
            // 'transactions.Transac_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
            'transactions.Staffincharge',
            'customers.Cust_fname', 
            'customers.Cust_lname', 
            'admins.Admin_fname',
            'admins.Admin_mname',
            'admins.Admin_lname',
            DB::raw('GROUP_CONCAT(laundry_categorys.Category SEPARATOR ", ") as Category'),
            DB::raw('SUM(transaction_details.Price) as totalprice')
        )
        ->groupBy(
            // 'transactions.Transac_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
            'transactions.Staffincharge',
            'customers.Cust_fname', 
            'customers.Cust_lname', 
            'admins.Admin_fname',
            'admins.Admin_mname',
            'admins.Admin_lname'
        )
        ->get();

        return response()->json([
            'data' => $data,
            'totalsprice' => $totalprice,
        ], 200);
    }
    public function CountDisplay()
    {
        // Count occurrences of each unique Tracking_number
        $trackingCounts = Transactions::select('Tracking_number', DB::raw('count(*) as total_count'))
            ->groupBy('Tracking_number')
            ->get();
        
        // Count total occurrences of all unique Tracking_numbers
        $totalTrackingCount = $trackingCounts->count('Tracking_number');

        // Returning the tracking numbers with their counts and the total count as a JSON response
        return response()->json([
            'tracking_counts' => $trackingCounts,
            'total_count' => $totalTrackingCount
        ], 200);
    }
    public function findtrans($id)
    {
        {
            $price = TransactionDetails::all();
    
            $transaction = Transactions::where('customers.Cust_ID', $id)
            ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
            ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
            ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
            ->select(
                'transactions.Tracking_number',
                'transactions.Transac_date',
                'transactions.Transac_status',
                'transactions.Pickup_datetime',
                'transactions.Delivery_datetime',
                'transactions.Staffincharge',
                'customers.Cust_ID', 
                'customers.Cust_fname', 
                'customers.Cust_lname', 
                'admins.Admin_fname',
                'admins.Admin_mname',
                'admins.Admin_lname',
                DB::raw('GROUP_CONCAT(laundry_categorys.Category SEPARATOR ", ") as Category'),
                DB::raw('SUM(transaction_details.Price) as totalprice')
            )
            ->groupBy(
                'transactions.Tracking_number',
                'transactions.Transac_date',
                'transactions.Transac_status',
                'transactions.Pickup_datetime',
                'transactions.Delivery_datetime',
                'transactions.Staffincharge',
                'customers.Cust_ID', 
                'customers.Cust_fname', 
                'customers.Cust_lname', 
                'admins.Admin_fname',
                'admins.Admin_mname',
                'admins.Admin_lname'
            )
            ->get();
    
        if ($transaction->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    
            return response()->json(['trans' => $transaction], 200);
        }
    }
    public function printtrans($id)
    {
        $countprice = Transactions::where('transactions.Tracking_number', $id)
            ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
            ->select(DB::raw('SUM(transaction_details.Price) as totalPrice'))
            ->get();


        $groupcateg = Transactions::where('transactions.Tracking_number', $id)
            ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
            ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
            ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
            ->select(
                DB::raw('GROUP_CONCAT(laundry_categorys.Category SEPARATOR ", ") as Category') // Merge category into one field
            )
            ->get();
    
        if ($groupcateg->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
            
        $trackingCounts = Transactions::where('transactions.Tracking_number', $id)
            ->select('Tracking_number', DB::raw('count(*) as total_count'))
            ->groupBy('Tracking_number')
            ->get();
        
        $totalTrackingCount = $trackingCounts->count('Tracking_number');

        $transaction = Transactions::where('transactions.Tracking_number', $id)
            ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
            ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
            // ->join('payments', 'transactions.Transac_ID', '=', 'payments.Transac_ID')
            ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
            ->select(
                'transactions.Tracking_number',
                'transactions.Transac_status',
                'transactions.Delivery_datetime',
                'transactions.Staffincharge',
                'transaction_details.Qty',
                'transaction_details.Weight',
                'transaction_details.Price',
                'customers.Cust_Phoneno', 
                'admins.Admin_fname',
                'admins.Admin_mname',
                'admins.Admin_lname',
                DB::raw('GROUP_CONCAT(laundry_categorys.Category SEPARATOR ", ") as Category'),
            )
            ->groupBy(
                'transactions.Tracking_number',
                'transactions.Transac_status',
                'transactions.Delivery_datetime',
                'transactions.Staffincharge',
                'transaction_details.Qty',
                'transaction_details.Weight',
                'transaction_details.Price', 
                'customers.Cust_Phoneno',  
                'admins.Admin_fname',
                'admins.Admin_mname',
                'admins.Admin_lname',
            )
            ->get();


        if ($transaction->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['total' => $countprice, 'categ' => $groupcateg, 'tracking' => $trackingCounts, 'trans'  => $transaction ], 200);
    
        
       
    }
    public function calculateBalance($id) {
        $price = Transactions::where('transactions.Tracking_number', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
        ->select(DB::raw('CAST(SUM(transaction_details.Price) AS UNSIGNED) as totalPrice'))
        ->first();

        if (!$price || $price->totalPrice === null) { 
        return response()->json(['message' => 'Transactions not found'], 404);
        }

        $amount = Transactions::where('transactions.Tracking_number', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('payments', 'transactions.Tracking_number', '=', 'payments.Tracking_number')
        ->select(DB::raw('CAST(SUM(payments.Amount) AS UNSIGNED) as totalAmount'))
        ->first(); 

        if (!$amount || $amount->totalAmount === null) {
        return response()->json(['message' => 'Transactionss not found'], 404);
        }

        $total = $amount->totalAmount - $price->totalPrice;

        $transaction = Transactions::where('transactions.Tracking_number', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
        ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
        ->join('payments', 'transactions.Tracking_number', '=', 'payments.Tracking_number')
        ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
        ->select(
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            // 'transactions.Pickup_datetime',
            // 'transactions.Delivery_datetime',
            'transactions.Staffincharge',
            'transaction_details.Qty',
            'transaction_details.Price',
            'customers.Cust_fname', 
            'customers.Cust_lname', 
            'customers.Cust_Phoneno', 
            'customers.Cust_email', 
            'customers.Cust_address', 
            'admins.Admin_fname',
            'admins.Admin_mname',
            'admins.Admin_lname',
            'payments.Mode_of_Payment',
            'payments.Amount',
            DB::raw('GROUP_CONCAT(laundry_categorys.Category SEPARATOR ", ") as Category'),
            DB::raw('SUM(transaction_details.Price) as totalPrice')
        )
        ->groupBy(
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            // 'transactions.Pickup_datetime',
            // 'transactions.Delivery_datetime',
            'transactions.Staffincharge',
            'transaction_details.Qty',
            'transaction_details.Price',
            'customers.Cust_fname', 
            'customers.Cust_lname', 
            'customers.Cust_Phoneno',  
            'customers.Cust_email', 
            'customers.Cust_address', 
            'admins.Admin_fname',
            'admins.Admin_mname',
            'admins.Admin_lname',
            'payments.Mode_of_Payment',
            'payments.Amount',
        )
        ->get();


        if ($transaction->isEmpty()) {
            return response()->json(['message' => 'Transactionsss not found'], 404);
        }
        return response()->json([
            'trans' => $transaction,
            'totalprice' => $price->totalPrice,
            'amount' => $amount->totalAmount,
            'balance' => $total
            ], 200);
    }
    public function approvedtrans(Request $request, $id)
    {
        // Get total price from transaction details
        $totalprice = TransactionDetails::sum('Price');

        // Fetch transaction data along with related records
        $data = Transactions::join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
            ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
            ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
            ->select(
                'transactions.Tracking_number',
                'transactions.Transac_date',
                'transactions.Transac_status',
                'transactions.Pickup_datetime',
                'transactions.Delivery_datetime',
                'transactions.Staffincharge',
                'customers.Cust_fname', 
                'customers.Cust_lname', 
                'admins.Admin_fname',
                'admins.Admin_mname',
                'admins.Admin_lname',
                DB::raw('GROUP_CONCAT(laundry_categorys.Category SEPARATOR ", ") as Category'),
                DB::raw('SUM(transaction_details.Price) as totalprice')
            )
            ->groupBy(
                'transactions.Tracking_number',
                'transactions.Transac_date',
                'transactions.Transac_status',
                'transactions.Pickup_datetime',
                'transactions.Delivery_datetime',
                'transactions.Staffincharge',
                'customers.Cust_fname', 
                'customers.Cust_lname', 
                'admins.Admin_fname',
                'admins.Admin_mname',
                'admins.Admin_lname'
            )
            ->get();

        // Update the transaction with the specific ID to set the status as 'paid'
        Transactions::where('Tracking_number', $id)
            ->update(['Transac_status' => 'paid']);

        // Return the response
        return response()->json([
            'data' => $data,
            'totalprice' => $totalprice,
        ], 200);
    }
    public function remittanceapproved(Request $request)
    {
        // Fetch all prices and calculate the total price for all transactions
        $Expenses = Expenses::sum('Amount');
        $Initial = Cashdetails::sum('Initial_amount');
        $Remit = Cashdetails::sum('Remitance');
        $Payment = Payments::sum('Amount');
        


        // Query to get transaction details with total price per transaction
        $data = DB::table('admins')
        ->join('cashed', 'cashed.Staff_ID', '=', 'admins.Admin_ID')  
        ->join('expenses', 'expenses.Admin_ID', '=', 'admins.Admin_ID')
        ->join('payments', 'payments.Admin_ID','=','admins.Admin_ID')  
        // ->join('admins as staff', 'cashed.Staff_ID', '=', 'staff.Admin_ID')  // Self-join: link Staff_ID to Admin_ID in the same table
        ->select(
            'admins.Admin_fname as Admin_fname',
            'admins.Admin_mname as Admin_mname',
            'admins.Admin_lname as Admin_lname',
            'cashed.Datetime_Changefund',
            'cashed.Datetime_Remitance',
            'cashed.Initial_amount',
            'cashed.Remitance',
            'cashed.Admin_ID',
            'cashed.Staff_ID',
            'cashed.Fund_status',
            // 'payments.Amount',
            DB::raw('SUM(expenses.Amount) as totalexpense'),
            DB::raw('SUM(payments.Amount) as totalpayment'),
            // DB::raw('SUM(payments.Amount) - SUM(cashed.Remitance) as balance')            // 'expenses.Amount'
        )
        ->groupBy(
            'admins.Admin_fname',
            'admins.Admin_mname',
            'admins.Admin_lname',
            'cashed.Datetime_Changefund',
            'cashed.Datetime_Remitance',
            'cashed.Initial_amount',
            'cashed.Remitance',
            'cashed.Admin_ID', 
            'cashed.Staff_ID',
            'cashed.Fund_status',
            // 'payments.Amount',
            // 'expenses.Amount'
        )
        ->get();
        // Return the result as JSON response
        return response()->json([
            'Data' => $data,
            'Expenses' => $Expenses,
            'Initial' => $Initial,
            'Remit' => $Remit,
            'Payment' => $Payment
        ], 200);
    }
    public function printTransac($id){
        // $Transaction = Transactions::all();
        $Remit = Cashdetails::where('cashed.Staff_ID', $id)
                            ->join('transactions','cashed.Staff_ID','=','transactions.Admin_ID')
                            ->select('cashed.Remitance')
                            ->groupBy('cashed.Remitance')
                            ->get();

        $Data = Transactions::where('transactions.Admin_ID', $id)
        ->join('customers', 'transactions.Cust_ID','=','customers.Cust_ID')
        ->join('transaction_details', 'transactions.Tracking_number', '=', 'transaction_details.Tracking_number')
        ->join('cashed','cashed.Staff_ID','=','transactions.Admin_ID')    
        ->select(
            'transactions.Cust_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
            'transactions.Staffincharge',
            'customers.Cust_lname',
            'customers.Cust_fname',
            'customers.Cust_mname',
            // 'cashed.Remitance',
            // DB::raw('SUM(cashed.Remitance) as remit'),
            DB::raw('SUM(transaction_details.Price) as totalprice')
        ) ->groupBy(
            'transactions.Cust_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
            'transactions.Staffincharge',
            'customers.Cust_lname',
            'customers.Cust_fname',
            'customers.Cust_mname',
            // 'cashed.Remitance'
        )
        ->get();
        $TotalTrans = $Data->sum('totalprice');

        $InitialAmount = Cashdetails::where('cashed.Staff_ID', $id)
        ->join('admins', 'cashed.Staff_ID', '=', 'admins.Admin_ID')
        ->select(
            'cashed.Staff_ID',
            'cashed.Initial_amount',
            'cashed.Fund_status',
            'admins.Admin_fname',
            'admins.Admin_lname',
            'admins.Admin_mname',
            DB::raw('SUM(cashed.Initial_amount) as Amountinitial')
        )
        ->groupBy(
            'cashed.Staff_ID',
            'cashed.Initial_amount',
            'cashed.Fund_status',
            'admins.Admin_fname',
            'admins.Admin_lname',
            'admins.Admin_mname'
        )
        ->get();

        $TotalInitial = $TotalTrans  + $InitialAmount->sum('Amountinitial');
        

        $Expenses = Expenses::where('expenses.Admin_ID', $id)
        ->join('admins', 'expenses.Admin_ID', '=', 'admins.Admin_ID')
        ->select(
            'expenses.Admin_ID',
            // 'expenses.Amount',
            'admins.Admin_fname',
            'admins.Admin_lname',
            'admins.Admin_mname',
            DB::raw('SUM(expenses.Amount) as totalexpenses')
        )
        ->groupBy(
            'expenses.Admin_ID',
            // 'expenses.Amount',
            'admins.Admin_fname',
            'admins.Admin_lname',
            'admins.Admin_mname',
        )
        ->get();

        $OverAllTotal = $TotalInitial -  $Expenses->sum('Amount');



        // Return the response
        return response()->json([
            'Transac' => $Data, 
            'Initials' => $InitialAmount, 
            'Expenses' => $Expenses, 
            'Totalprice' => $TotalTrans, 
            'Totalinitials' => $TotalInitial, 
            'Overalltotal' => $OverAllTotal,
            'Remit' => $Remit,
        ], 200);
    }


    // REPORT
    public function displayexpenses()
    {
        $price = Expenses::join('admins', 'admins.Admin_ID', '=', 'expenses.Admin_ID')
            ->select('expenses.*','admins.*') 
            ->orderBy('expenses.Expense_ID', 'desc')  
            ->get();

        // Calculate the total sum of Amount from expenses
        $totalAmount = Expenses::sum('Amount');

        // Return the result in JSON format
        return response()->json(["price" => $price, 'totalAmount' => $totalAmount], 200);
    }
    public function displayincome()
    {
        $Payment =  DB::table('payments')
                        ->join('transactions','transactions.Admin_ID', '=','payments.Admin_ID')
                        ->select('payments.Admin_ID')
                        ->groupBy('payments.Admin_ID')  
                        ->get();

        return response()->json($Payment, 200);
    }

    public function sampledis(Request $request)
    { 
        // $Transaction = Transactions::all();
        $Payment = Transactions::join('transactions','transaction_details.Tracking_number','=','transactions.Tracking_number')
        ->join('payments','transactions.Tracking_number','=','payments.Tracking_number')
        ->join('transactions','expenses.Admin_ID','=','transactions.Admin_ID')
        ->select(
            DB::raw('SUM(transaction_details.Price) as totalpayment'),
            DB::raw('SUM(payments.Amount) as totalAmount'), 
            DB::raw('SUM(expenses.Amount) as totalExpenses')
            )
        ->get();

        // Return the response
        return response()->json($Payment, 200);
    }
}
