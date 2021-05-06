<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemCollection as CartItemCollection;
use App\Models\Carrier;
use App\Models\City;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    /**
     * Store a newly created Cart in storage and return the data to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
            $userID = Auth::user()->getKey();
        }
        $cart = Cart::create([
            'id' => md5(uniqid(rand(), true)),
            'key' => md5(uniqid(rand(), true)),
            'user_id' => isset($userID) ? $userID : null,
        ]);
        return response()->json([
            'Message' => 'A new cart have been created for you!',
            'cartToken' => $cart->id,
            'cartKey' => $cart->key,
        ], 201);

    }

    /**
     * Display the specified Cart.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart, Request $request)
    {
        if(Auth::check()){
        $validator = Validator::make($request->all(), [
            'cartKey' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $cartKey = $request->input('cartKey');
        if ($cart->key == $cartKey) {
            return response()->json([
                'cart' => $cart->id,
                'Items' => new CartItemCollection($cart->items),
            ], 200);
        } else {

            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }
    }else{
        return response()->json([
            'message' => 'please login or regiter',
        ], 400);
    }
    }

    /**
     * Remove the specified Cart from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cartKey' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $cartKey = $request->input('cartKey');

        if ($cart->key == $cartKey) {
            $cart->delete();
            return response()->json(null, 204);
        } else {

            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }

    }

    /**
     * Adds Products to the given Cart;
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return void
     */
    public function addProducts(Cart $cart, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cartKey' => 'required',
            'productID' => 'required',
            'quantity' => 'required|numeric|min:1|max:10',
            'currency' => 'required|',
            'value' => 'required|',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }
        $cartKey = $request->input('cartKey');
        $productID = $request->input('productID');
        $quantity = $request->input('quantity');
        $currency = $request->input('currency');
        $value = $request->input('value');
        //Check if the CarKey is Valid
        if ($cart->key == $cartKey) {
            //Check if the proudct exist or return 404 not found.
            try { $Product = Product::findOrFail($productID);} catch (ModelNotFoundException $e) {
                return response()->json([
                    'message' => 'The Product you\'re trying to add does not exist.',
                ], 404);
            }
            //check if the the same product is already in the Cart, if true update the quantity, if not create a new one.
            $cartItem = CartItem::where(['cart_id' => $cart->getKey(), 'product_id' => $productID])->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                CartItem::where(['cart_id' => $cart->getKey(), 'product_id' => $productID])
                ->update(['quantity' => $quantity,'currency'=> $currency , 'value' =>$value]);
            } else {
                CartItem::create(['cart_id' => $cart->getKey(),
                 'product_id' => $productID, 'quantity' => $quantity ,
                 'currency'=> $currency, 'value' =>$value]);
            }

            return response()->json(['message' => 'The Cart was updated with the given product information successfully'], 200);

        } else {

            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }

    }

    /**
     * checkout the cart Items and create and order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return void
     */
    public function checkout(Cart $cart, Request $request)
    {


        if (Auth::check()) {
            $userID = Auth::user()->getKey();
        }

        $validator = Validator::make($request->all(), [
            'cartKey' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'comment' => '',
            'country' => 'required',
            'address' => 'required',
            'phone' => 'required',
       /*      'credit card number' => 'required',
            'expiration_year' => 'required',
            'expiration_month' => 'required',
            'cvc' => 'required', */
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $cartKey = $request->input('cartKey');
        if ($cart->key == $cartKey) {
            $name = Auth::user()->name;
            $email = Auth::user()->email;
            $ip = $request->ip();
            $user_agent = $request->header('User-Agent');
            $domain = $request->root();
            $comment = $request->input('comment');
            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');
            $country_id = $request->input('country');
            $country = City::where('id','=',$country_id)->first()->name;
            $carrier = Carrier::where('City_id','=',$country_id)->first()->price;
            $phone = $request->input('phone');
            $adress = $request->input('address');
            $TotalPrice = (float) 0.0;
            $total_discount = (float) 0.0;
            $total_price_without_tax= (float) 0.0;
            $total_tax_all = (float) 0.0;
            $total_shipping = $carrier;
            $items = $cart->items;
            $status_id = OrderStatus::findOrFail(1)->first();
            if(Auth::user()->currency_id){
                $currency = Currency::findOrFail(Auth::user()->currency_id)->first();
            }
            $currency = Currency::where('iso','=','EUR')->first();

            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                $price = $product->price;
                $price_with_quantity = $price * $item->quantity;
                $tax_id = $product->tax()->first()->id;
                $tax =  $product->tax()->first()->value;
                $tax_total= ($tax/100) * $price;
                $price_with_tax = $tax_total+$price;

                $price_with_tax_and_quantity = $price_with_tax*$item->quantity;
                $discount = (int)$product->specificPrice()->first()->reduction;

                if($product->specificPrice()->first()->discount_type == "Amount"){
                $TotalPrice = (($TotalPrice + $price_with_tax_and_quantity )-$discount)+$total_shipping;

                }
                else{
                $discount = ($discount/100 * $price) * $item->quantity;
                $TotalPrice = (($TotalPrice + $price_with_tax_and_quantity )-$discount)+$total_shipping;

                }
                $total_discount =$total_discount + $discount;
                $total_price_without_tax =  $total_price_without_tax +  $price_with_quantity;
                $total_tax_all = $total_tax_all + ($tax_total * $item->quantity);
            }

            /**
             * Credit Card information should be sent to a payment gateway for processing and validation,
             * the response should be dealt with here, but since this is a dummy project we'll
             * just assume that the information is sent and the payment process was done succefully,
             */

            $PaymentGatewayResponse = true;
            $transactionID = md5(uniqid(rand(), true));

            if ($PaymentGatewayResponse) {
                $order = Order::create([
                  'products' => json_encode(new CartItemCollection($items)),
                    'total' => $TotalPrice,
                    'user_id' => isset($userID) ? $userID : null,
                    'domain' => $domain,
                    'status_id' => $status_id->id,
                    'status' => $status_id->name,
                    'currency_id' =>  $currency->id,
                    'currency' =>  $currency->iso,
                    'comment' => $comment,
                    'invoice-no' => null,
                    'invoice_date' => null,
                    'delivery_date' => null,
                    'subtotal' => $total_price_without_tax,
                    'total_discount' => $total_discount,
                    'tax_id' => $tax_id,
                    'total_tax' => $total_tax_all ,
                    'total_shipping' => $total_shipping,
                    'name_sender' => $name,
                    'email' => $email,
                    'first_name_receiver' => $first_name,
                    'last_name_receiver' => $last_name,
                    'country_id'=>$country_id,
                    'country' => $country,
                    'address1' => $adress,
                    'phone' => $phone,
                    'user_agent' => $user_agent,
                    'ip' => $ip,
                ]);

              /*   $cart->delete(); */

                return response()->json([
                    'message' => 'you\'re order has been completed succefully, thanks for shopping with us!',
                    'orderID' => $order->getKey(),
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }

    }

}
