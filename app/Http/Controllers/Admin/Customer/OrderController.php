<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Models\category;
use App\Models\customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Product_order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{


    public function create($TranslationOf) {
        try {

            if (isset($TranslationOf) && filter_var($TranslationOf, FILTER_VALIDATE_INT)) {

                // Fetch customer data
                $customer = customer::select('translation_of', 'name', 'client_permissions')->TranslationOf($TranslationOf)->first();

                if (!isset($customer) || $customer->count() <= 0) {
                    return redirect()->route('admin.customer.index');
                }

                // Get all categories
                $categories = category::Selection()->LanguageSelect()->orderBy('name')->get();

                // Bring only 10 products from each section
                foreach ($categories as $key => $value) {
                    $products[] = Product::select('translation_of', 'category_id', 'name', 'pharmacist_price', 'selling_price',  'store')->where('category_id', $categories[$key]->translation_of)->Abbr()->take(ORDER_PRODUCTS)->orderBy('name')->get();
                }

                // Fetch all previous orders for this customer
                $orders = Order::Selection()->where('client_id', $TranslationOf)->with(['product_order' => function ($product_order) {
                    return $product_order->select('product_id', 'order_id')->with(['product' => function ($product) {
                        return $product->select('translation_of', 'name')->Abbr()->withTrashed();
                    }]);
                }])->paginate(PAGINATE_ORDERS);


                return view('admin.customers.orders.create')->with('customer', $customer)->with('categories', $categories)->with('products', $products)->with('orders', $orders);

            } else {
                return redirect()->route('admin.customer.index');
            }
        } catch (\Exception $ex) {
            return redirect()->route('admin.customer.index');
        }
    }

    public function search(Request $request)  {

        try {

            if ($request->has('customer_id') && filter_var($request->customer_id, FILTER_VALIDATE_INT)) {

                // Fetch customer data
                $customer = customer::select('translation_of', 'name', 'client_permissions')->where('translation_of', $request->customer_id)->abbr()->first();

                $search = $request->search;

                // Fetch the products you are looking for
                $products = Product::select('translation_of', 'name', 'pharmacist_price', 'selling_price',  'store')->Abbr()->orderBy('name')
                    ->where('name', 'like', '%' . $search . '%')
                    ->where('category_id', $request->category_id)->paginate(ORDER_PRODUCTS);

                if (isset($products) && $products->count() > 0) {
                    $products = collect($products)->merge($customer);

                    return json_encode($products);
                } else {
                    return json_encode(['status' => false, 'search' => $search]);
                }
            }
        } catch (\Exception $ex) {
            return ' ';
        }
    }


    public function store(OrderRequest $request) {
        try {

            // If there is a customer you want to add the order to
            if (isset($request->client_id) && filter_var($request->client_id, FILTER_VALIDATE_INT)) {

                // Search for the client and get his permissions || Its type is [Pharmacist - Client]
                $customer = customer::select('client_permissions')->where('translation_of', $request->client_id)->first();

                // If the customer already exists && there are orders
                if ($customer && $request->products) {

                    $total_price = 0;
                    // If the client is a pharmacist
                    if ($customer->client_permissions == 'pharmaceutical') {

                        foreach ($request->products as $product_translation_of => $quantity) {

                            // Fetch the pharmacist's product price
                            $product = Product::select('translation_of', 'pharmacist_price', 'store')->where('translation_of', $product_translation_of)->first();

                            if ($product) {
                                // Multiply product price by quantity
                                $totalProductPrice = $product->pharmacist_price * $quantity['quantity'];
                                // Add the total price of the product with the total price of the previous products
                                $total_price += $totalProductPrice;

                                // If the quantity in stock is greater than the quantity requested by the pharmacist
                                if ($product->store > $quantity['quantity']) {
                                    $store = $product->store - $quantity['quantity'];

                                    // Update the quantity in stock to the new quantity
                                    Product::where('translation_of', $product_translation_of)->update(['store' => $store]);
                                } else {  // If the quantity requested by the pharmacist is greater than the quantity in stock

                                    // Update the quantity in stock to the new quantity
                                    Product::where('translation_of', $product_translation_of)->update(['store' => 0]);
                                }
                            }   //  End If
                        }   //  End Of Foreach

                        // If the client is a customer normal
                    } elseif ($customer->client_permissions == 'customer') {

                        foreach ($request->products as $product_translation_of => $quantity) {

                            // Fetch the customer product price normal
                            $product = Product::select('translation_of', 'selling_price', 'store')->where('translation_of', $product_translation_of)->first();
                            if ($product) {
                                // Multiply product price by quantity
                                $totalProductPrice = $product->selling_price * $quantity['quantity'];
                                // Add the total price of the product with the total price of the previous products
                                $total_price += $totalProductPrice;

                                // If the quantity in stock is greater than the quantity requested by the pharmacist
                                if ($product->store > $quantity['quantity']) {
                                    $store = $product->store - $quantity['quantity'];

                                    // Update the quantity in stock to the new quantity
                                    $newProduct = Product::where('translation_of', $product_translation_of)->update(['store' => $store]);
                                } else {  // If the quantity requested by the pharmacist is greater than the quantity in stock

                                    // Update the quantity in stock to the new quantity
                                    $newProduct = Product::where('translation_of', $product_translation_of)->update(['store' => 0]);
                                }
                            }   //  End If
                        }   //  End Foreach

                    } else {   //  If the customer is a special customer

                        foreach ($request->products as $product_translation_of => $quantity) {

                            // Get the price of the product for the special customer
                            $product = Product::select('translation_of', 'selling_price', 'pharmacist_price', 'store')->where('translation_of', $product_translation_of)->first();

                            if ($product) {
                                // Multiply product price by quantity
                                $totalProductPrice = $product->ProductPriceAccordingToCustomerType * $quantity['quantity'];
                                // // Add the total price of the product with the total price of the previous products
                                $total_price += $totalProductPrice;

                                // If the quantity in stock is greater than the quantity requested by the pharmacist
                                if ($product->store > $quantity['quantity']) {
                                    $store = $product->store - $quantity['quantity'];

                                    // Update the quantity in stock to the new quantity
                                    $newProduct = Product::where('translation_of', $product_translation_of)->update(['store' => $store]);
                                } else {  // If the quantity requested by the pharmacist is greater than the quantity in stock

                                    // Update the quantity in stock to the new quantity
                                    $newProduct = Product::where('translation_of', $product_translation_of)->update(['store' => 0]);
                                }
                            }   //  End If
                        }   //  End Of Foreach
                    }   //  End Else

                    DB::beginTransaction();

                    // Adding a new order and fetching the [id] of the new order
                    $order_id = Order::create([
                        'client_id' =>  $request->client_id,
                        'total_price'   =>  $total_price,
                    ])->id;

                    $product_orders = [];
                    foreach ($request->products as $product => $quantity) {
                        $product_orders[]   = [
                            'product_id'   =>  $product,
                            'order_id'  =>  $order_id,
                            'quantity'  =>  $quantity['quantity'],
                        ];
                    }

                    Product_order::insert($product_orders);

                    DB::commit();

                    // customers-read
                    if (Auth::user()->hasPermission('orders-read')) {

                        return redirect()->route('admin.all_order.index');
                    } else {
                        return redirect()->route('admin.customer.index');
                    }
                } else {
                    return redirect()->route('admin.customer.index');
                }   //  End Of Else
            } else {
                return redirect()->route('admin.customer.index');
            }   //  End Of Else

        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('admin.customer.index');
        }   //  End Of Catch
    }   // The end of the Store function



    public function edit($id)
    {
        try {
            if (isset($id) && filter_var($id, FILTER_VALIDATE_INT)) {

                $order = Order::select('id', 'client_id')->where('id', $id)->first();


                if (!$order || $order->count() <= 0) {
                    return redirect()->route('admin.all_order.index');
                }

                // Get all categories
                $categories = category::Selection()->LanguageSelect()->orderBy('name')->get();

                // Bring only 10 products from each section
                foreach ($categories as $key => $value) {
                    $products[] = Product::select('translation_of', 'category_id', 'name', 'pharmacist_price', 'selling_price',  'store')->where('category_id', $categories[$key]->translation_of)->Abbr()->take(ORDER_PRODUCTS)->orderBy('name')->get();
                }

                // Bring all the products of the order to be modified
                $order = Order::select('id', 'client_id')->where('id', $id)->with(['customer' => function ($q) {
                    return $q->select('translation_of', 'name', 'client_permissions')->Abbr();
                }])->with(['product_order' => function ($product_order) {
                    return $product_order->select('product_id', 'order_id', 'quantity')->with(['product' => function ($product) {
                        return $product->select('translation_of', 'name', 'pharmacist_price', 'selling_price')->Abbr();
                    }]);
                }])->first();

                return view('admin.customers.orders.edit')->with('customer', $order->customer)->with('categories', $categories)->with('products' , $products)->with('order', $order);
            } else {
                return redirect()->route('admin.all_order.index');
            }
        } catch (\Exception $ex) {
            return redirect()->route('admin.all_order.index');
        }
    }



    public function update(OrderRequest $request)
    {
        try {

            // If there is a customer you want to add the order to
            if (isset($request->client_id) && filter_var($request->client_id, FILTER_VALIDATE_INT) && isset($request->order_id) && filter_var($request->order_id, FILTER_VALIDATE_INT)) {

                $order = Order::select('id')->where('id', $request->order_id)->where('client_id', $request->client_id)->first();

                if (!$order) {
                    return redirect()->route('admin.all_order.index');
                }
                // Search for the client and get his permissions || Its type is [Pharmacist - Client]
                $customer = customer::select('client_permissions')->where('translation_of', $request->client_id)->first();

                // If the customer already exists && there are orders
                if ($customer && $request->products) {

                    // Adding the quantity of the product in the order to the quantity in stock
                    foreach ($order->product_order as $product) {
                        $store = $product->product[0]->store + $product->quantity;
                        Product::where('translation_of', $product['product_id'])->update(['store' => $store]);
                    }

                    $total_price = 0;
                    // If the client is a pharmacist
                    if ($customer->client_permissions == 'pharmaceutical') {

                        foreach ($request->products as $product_translation_of => $quantity) {

                            // Fetch the pharmacist's product price
                            $product = Product::select('translation_of', 'pharmacist_price', 'store')->where('translation_of', $product_translation_of)->first();

                            if ($product) {
                                // Multiply product price by quantity
                                $totalProductPrice = $product->pharmacist_price * $quantity['quantity'];
                                // Add the total price of the product with the total price of the previous products
                                $total_price += $totalProductPrice;

                                // If the quantity in stock is greater than the quantity requested by the pharmacist
                                if ($product->store > $quantity['quantity']) {
                                    $store = $product->store - $quantity['quantity'];

                                    // Update the quantity in stock to the new quantity
                                    Product::where('translation_of', $product_translation_of)->update(['store' => $store]);
                                } else {  // If the quantity requested by the pharmacist is greater than the quantity in stock

                                    // Update the quantity in stock to the new quantity
                                    Product::where('translation_of', $product_translation_of)->update(['store' => 0]);
                                }
                            }   //  End If
                        }   //  End Of Foreach

                        // If the client is a customer normal
                    } elseif ($customer->client_permissions == 'customer') {

                        foreach ($request->products as $product_translation_of => $quantity) {

                            // Fetch the customer product price normal
                            $product = Product::select('translation_of', 'selling_price', 'store')->where('translation_of', $product_translation_of)->first();
                            if ($product) {
                                // Multiply product price by quantity
                                $totalProductPrice = $product->selling_price * $quantity['quantity'];
                                // Add the total price of the product with the total price of the previous products
                                $total_price += $totalProductPrice;

                                // If the quantity in stock is greater than the quantity requested by the pharmacist
                                if ($product->store > $quantity['quantity']) {
                                    $store = $product->store - $quantity['quantity'];

                                    // Update the quantity in stock to the new quantity
                                    Product::where('translation_of', $product_translation_of)->update(['store' => $store]);
                                } else {  // If the quantity requested by the pharmacist is greater than the quantity in stock

                                    // Update the quantity in stock to the new quantity
                                    Product::where('translation_of', $product_translation_of)->update(['store' => 0]);
                                }
                            }   //  End If
                        }   //  End Foreach

                    } else {   //  If the customer is a special customer

                        foreach ($request->products as $product_translation_of => $quantity) {

                            // Get the price of the product for the special customer
                            // 'translation_of' ,'selling_price' , 'pharmacist_price' , 'store'
                            $product = Product::select('translation_of', 'selling_price', 'pharmacist_price', 'store')->where('translation_of', $product_translation_of)->first();

                            if ($product) {
                                // Multiply product price by quantity
                                $totalProductPrice = $product->ProductPriceAccordingToCustomerType * $quantity['quantity'];
                                // // Add the total price of the product with the total price of the previous products
                                $total_price += $totalProductPrice;

                                // If the quantity in stock is greater than the quantity requested by the pharmacist
                                if ($product->store > $quantity['quantity']) {
                                    $store = $product->store - $quantity['quantity'];

                                    // Update the quantity in stock to the new quantity
                                    $newProduct = Product::where('translation_of', $product_translation_of)->update(['store' => $store]);
                                } else {  // If the quantity requested by the pharmacist is greater than the quantity in stock

                                    // Update the quantity in stock to the new quantity
                                    $newProduct = Product::where('translation_of', $product_translation_of)->update(['store' => 0]);
                                }
                            }   //  End If
                        }   //  End Of Foreach
                    }   //  End Else

                    DB::beginTransaction();

                    $product_orders = [];
                    foreach ($request->products as $product => $quantity) {
                        $product_orders[]   = [
                            'product_id'   =>  $product,
                            'order_id'  =>  $order->id,
                            'quantity'  =>  $quantity['quantity'],
                        ];
                    }

                    Order::where('id', $order->id)->update([
                        'total_price'   =>  $total_price,
                        'status'        =>  '0',
                    ]);


                    $old_product_order = Product_order::select('id')->where('order_id', $order->id)->get();
                    Product_order::destroy($old_product_order->toArray());

                    Product_order::insert($product_orders);

                    DB::commit();

                    return redirect()->route('admin.all_order.index');
                } else {
                    return redirect()->route('admin.customer.index');
                }   //  End Of Else
            } else {
                return redirect()->route('admin.customer.index');
            }   //  End Of Else

        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('admin.customer.index');
        }   //  End Of Catch
    }



    public function delete(Request $request)
    {
        try {

            if ($request->has('id') && filter_var($request->id, FILTER_VALIDATE_INT)) {

                $order = Order::select('id')->where('id', $request->id)->first();

                if ($order && $order->count() > 0) {

                    // Adding the quantity of the product in the order to the quantity in stock
                    foreach ($order->product_order as $product) {
                        $store = $product->product[0]->store + $product->quantity;
                        Product::where('translation_of', $product['product_id'])->update(['store' => $store]);
                    }

                    $product_order = Product_order::select('id')->where('order_id', $request->id)->get();

                    DB::beginTransaction();
                    Product_order::destroy($product_order->toArray());
                    $order->delete();
                    DB::commit();

                    return response()->json([
                        'status'    =>  'true',
                        'id'        =>  $request->id,
                    ]);
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                'status'    =>  'false',
            ]);
        }
    }
}
