<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Laundrycategorys;
use App\Models\Payments;
use App\Models\Customers;
use App\Models\Expenses;
use App\Models\Transactions;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;

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

    // STAFF
    public function display(){
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
            // Delete old image if exists in storage and htdocs
            if ($admin->Admin_image) {
                // Delete from storage
                Storage::delete('public/profile_images/' . $admin->Admin_image);
                
                // Delete from htdocs folder
                $htdocsImagePath = 'C:/xampp/htdocs/admin/profile_images/' . $admin->Admin_image;
                if (file_exists($htdocsImagePath)) {
                    unlink($htdocsImagePath);
                }
            }
    
            // Get the image extension and store the new image name
            $extension = $request->Admin_image->extension();
            $imageName = time() . '_' . $admin->Admin_ID . '.' . $extension;
            $request->Admin_image->storeAs('public/profile_images', $imageName);
    
            // Define the path for the htdocs folder in your local machine
            $htdocsPath = 'C:/xampp/htdocs/admin/profile_images'; // Replace with your actual project folder name
    
            // Ensure the directory exists, if not, create it
            if (!file_exists($htdocsPath)) {
                mkdir($htdocsPath, 0777, true);
            }
    
            // Save the image in the htdocs project folder
            $request->Admin_image->move($htdocsPath, $imageName);
    
            // Update admin's profile image name and extension in the database
            $admin->Admin_image = $imageName;
            $admin->save();
    
            return response()->json([
                'message' => 'Profile image updated successfully',
                'image_url' => asset('profile_images/' . $imageName) // URL to the htdocs folder
            ], 200);
        }
    
        return response()->json(['message' => 'No image file uploaded'], 400);
    }
    



    // pricemanagement
    public function pricedisplay(){
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

    // TRANSACTIONs
    public function Transadisplay()
    {
        $price = TransactionDetails::all();

        $totalprice = $price->sum('Price');

        $data = Transactions::join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
        ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
        ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
        ->select(
            'transactions.Transac_ID',
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
            'transactions.Transac_ID',
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

    public function findtrans($id)
    {
        $price = TransactionDetails::all();

        $totalprice = $price->sum('Price');

        $transaction = Transactions::where('customers.Cust_ID', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
        ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
        ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
        ->select(
            'transactions.Transac_ID',
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
            'transactions.Transac_ID',
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


        if ($transaction->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['trans' => $transaction, 'totalprice' =>$totalprice], 200);
    }

    public function printtrans($id)
    {
        // $price = TransactionDetails::all();
        $price = Transactions::where('customers.Cust_ID', $id)
            ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
            ->select(DB::raw('SUM(transaction_details.Price) as totalPrice')) 
            ->get();
        if ($price->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $amount = Transactions::where('customers.Cust_ID', $id)
            ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            // ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
            ->join('payments', 'transactions.Transac_ID', '=', 'payments.Transac_ID')
            ->select('payments.Amount') 
            ->get();
        if ($amount->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        

        // $totalprice = $price->sum('Price');

        $transaction = Transactions::where('customers.Cust_ID', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
        ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
        ->join('payments', 'transactions.Transac_ID', '=', 'payments.Transac_ID')
        ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
        ->select(
            'transactions.Transac_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
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
            'transactions.Transac_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
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
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['trans' => $transaction, 'totalprice' =>$price, 'amount' =>$amount], 200);
       
    }
    public function calculateBalance($id) {
        $price = Transactions::where('customers.Cust_ID', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
        ->select(DB::raw('CAST(SUM(transaction_details.Price) AS UNSIGNED) as totalPrice'))
        ->first(); // Use first() instead of get() to get a single result

        if (!$price || $price->totalPrice === null) { // Check if price is null or not found
        return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Fetch total amount from payments
        $amount = Transactions::where('customers.Cust_ID', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('payments', 'transactions.Transac_ID', '=', 'payments.Transac_ID')
        ->select(DB::raw('CAST(SUM(payments.Amount) AS UNSIGNED) as totalAmount'))
        ->first(); // Use first() instead of get() to get a single result

        if (!$amount || $amount->totalAmount === null) { // Check if amount is null or not found
        return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Calculate total
        $total = $amount->totalAmount - $price->totalPrice;

        // Return the JSON response
       
    

        // $totalprice = $price->sum('Price');

        $transaction = Transactions::where('customers.Cust_ID', $id)
        ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
        ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
        ->join('admins', 'admins.Admin_ID', '=', 'transactions.Admin_ID')
        ->join('payments', 'transactions.Transac_ID', '=', 'payments.Transac_ID')
        ->join('laundry_categorys', 'transaction_details.Categ_ID', '=', 'laundry_categorys.Categ_ID')
        ->select(
            'transactions.Transac_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
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
            'transactions.Transac_ID',
            'transactions.Tracking_number',
            'transactions.Transac_date',
            'transactions.Transac_status',
            'transactions.Pickup_datetime',
            'transactions.Delivery_datetime',
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
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        return response()->json([
            'trans' => $transaction,
            'totalprice' => $price->totalPrice,
            'amount' => $amount->totalAmount,
            'balance' => $total
            ], 200);
    }
    
    


    public function sampledis(){
        $customerId = 3; // Set this to the ID you want to query

        $transaction = DB::table('transactions')
            ->join('customers', 'transactions.Cust_ID', '=', 'customers.Cust_ID')
            ->join('transaction_details', 'transactions.Transac_ID', '=', 'transaction_details.Transac_ID')
            ->select(
                'transactions.Cust_ID',
                DB::raw('SUM(transaction_details.Price) as totalPrice') // Sum of prices for this customer
            )
            ->where('customers.Cust_ID', $customerId) // Filter by specific customer ID
            ->groupBy('transactions.Cust_ID') // Group by the customer ID
            ->first(); // Get a single record
        return response()->json($transaction, 200);
    }


}
