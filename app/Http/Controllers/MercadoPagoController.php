<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MercadoPagoController extends Controller
{
    public function createPreference(Request $request)
    {
        \MercadoPago\MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        $preference = new \MercadoPago\Resources\Preference();
        $item = new \MercadoPago\Resources\Item();
        $item->title = "Pago en Aesthectic";
        $item->quantity = 1;
        $item->unit_price = (float) $request->amount;
        $preference->items = [$item];

        $preference->back_urls = [
            "success" => "https://aesthectic.com/payment-success",
            "failure" => "https://aesthectic.com/payment-failure",
            "pending" => "https://aesthectic.com/payment-pending"
        ];
        $preference->auto_return = "approved";

        $preference->save();

        return response()->json(['init_point' => $preference->init_point]);
    }
}
