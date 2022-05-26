<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function pay($id) {

        try {

            if (! Auth::user()->hasPermission('payment-create')) {
                return redirect()->route('admin.customer.index') ;
            }

            // Fetch customer data
            $customer = customer::selection()->TranslationOf($id)->first() ;

            $payments = Payment::where('customer_id' , $customer->translation_of)->orderBy('created_at' , 'DESC')->paginate(PAGINATE_PAYMENT) ;

            if ($customer && $payments) {
                return view('admin.customers.payments.create')->with('customer' , $customer)->with('payments' , $payments) ;
            }  else     {
                return redirect()->route('admin.customer.index') ;
            }
        } catch (\Exception $ex) {
            return redirect()->route('admin.customer.index') ;
        }
    }


    public function store(PaymentRequest $request)  {
        try {

            if (! Auth::user()->hasPermission('payment-create')) {
                return redirect()->route('admin.customer.index') ;
            }

            $translation_of = $request->translation_of ;
            $amount = $request->amount ;

            if (isset($translation_of) && filter_var($translation_of , FILTER_VALIDATE_INT) && isset($amount) && (filter_var($amount , FILTER_VALIDATE_INT) || filter_var($amount , FILTER_VALIDATE_FLOAT))) {

                // Fetch customer data
                $customer = customer::select('translation_of' , 'previous_account')->TranslationOf($translation_of)->first() ;
                $totalAccount = $customer->previous_account ;  // Fetch the customer's previous account

                // If the total of the customer's account is greater than the amount to be paid
                if ($totalAccount >= $amount) {

                    DB::beginTransaction() ;

                        // Add the amount to be paid in the database
                        Payment::create([
                            'customer_id'   =>  $customer->translation_of ,
                            'payment'       =>  $amount ,
                        ]) ;

                        $newAccount = $totalAccount - $amount ;

                        // Modify the customer's account total
                        customer::where('translation_of' , $translation_of)->update(['previous_account' => $newAccount]) ;

                    DB::commit() ;

                    return response()->json([
                        'status'        =>  1 ,
                        'newAccount'    =>  $newAccount ,
                    ]) ;

                }   else    {
                    DB::rollBack() ;
                    return response()->json([
                        'status'        =>  0 ,
                        'amountBig'    =>  __('customers.amount_big') ,
                    ]) ;
                }
            }   else    {
                return response()->json([
                    'status'    =>  -1 ,
                ]) ;
            }

        } catch (\Exception $ex) {
            return redirect()->route('admin.customer.index') ;
        }
    }


    public function edit ($id)  {
        try {

            if (! Auth::user()->hasPermission('payment-update')) {
                return redirect()->route('admin.customer.index') ;
            }

            if (isset($id) && filter_var($id , FILTER_VALIDATE_INT)) {
                $payment = Payment::find($id) ;

                if ($payment) {
                    return view('admin.customers.payments.edit')->with('payment' , $payment) ;
                }   else    {
                    return redirect()->route('admin.customer.index') ;
                }
            }   else    {
                return redirect()->route('admin.customer.index') ;
            }
        } catch (\Exception $ex) {
            return redirect()->route('admin.customer.index') ;
        }
    }


    public function update(PaymentRequest $request)    {
        try {

            if (! Auth::user()->hasPermission('payment-update')) {
                return redirect()->route('admin.customer.index') ;
            }

            $payment_id = $request->id ;
            $editPayment = $request->amount ;

            if (isset($payment_id) && filter_var($payment_id , FILTER_VALIDATE_INT) && isset($editPayment) && (filter_var($editPayment , FILTER_VALIDATE_INT) || filter_var($editPayment , FILTER_VALIDATE_FLOAT))) {

                // // Bring the amount paid
                $payment = Payment::find($payment_id) ;

                if (! $payment) {
                    return redirect()->route('admin.customer.index') ;
                }

                $customer_id = $payment->customer->translation_of ;

                // Fetch the data of the customer who paid this amount
                $customer = customer::select('id' , 'previous_account')->TranslationOf($customer_id)->first() ;
                $totalPayment = $customer->previous_account ;

                // If the total account of the customer is greater than or equal to the amount to be added
                if ($totalPayment >= $editPayment) {

                    DB::beginTransaction() ;

                        // Modify the amount paid by the customer
                        Payment::where('id' , $payment_id)->update([
                            'payment'       =>  $editPayment ,
                        ]) ;

                        $oldPayment = $payment->payment ;
                        $totalPayment = ($totalPayment + $oldPayment) - $editPayment ;

                        // Modify the customer's total account
                        customer::where('translation_of' , $customer_id)->update(['previous_account' => $totalPayment]) ;

                    DB::commit() ;

                    return redirect()->route('admin.payments.create' , $customer_id) ;

                }   else    {
                    DB::rollBack() ;
                    return redirect()->route('admin.customer.index') ;
                }
            }   else    {
                return redirect()->route('admin.customer.index') ;
            }

        } catch (\Exception $ex) {
            return redirect()->route('admin.customer.index') ;
        }
    }



    public function delete( Request $request )  {
        try {

            if (! Auth::user()->hasPermission('payment-delete')) {
                return false ;
            }

            $id = $request->id ;

            if (isset($id) && filter_var($id , FILTER_VALIDATE_INT)) {

                // Bring the amount paid
                $payment = Payment::find($id) ;

                if ($payment) {
                    $totalPayment = $payment->customer->previous_account + $payment->payment ;

                    DB::beginTransaction() ;

                        // Modify the customer's account total
                        customer::where('translation_of' , $payment->customer_id)->update(['previous_account' => $totalPayment]) ;

                        $payment->delete() ;

                    DB::commit() ;

                    return response()->json([
                        'status'    =>  true ,
                        'id'        =>  $id ,
                        'payment'   =>  $totalPayment ,
                    ]) ;
                }
            }


        } catch (\Exception $ex) {
            DB::rollBack() ;
            return false ;
        }
    }
}
