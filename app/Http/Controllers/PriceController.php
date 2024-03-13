<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{


    public function getALLPrices()
    {
        $prices = Price::all();

        return response()->json([
            'code' => 200,
            'prices' => $prices,
        ]);
    }




    public function getPrice(Request $request)
    {

        $code = $request->query('code');
        $price = Price::where('code',$code)->first();

        if (!$price) {
            return response()->json([
                'code' => 404,
                'message' => 'Price not found for the provided country code.',
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'price' => $price,
        ]);


    }





    public function addPrice(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:prices',
            'price' => 'required|numeric',
        ]);

        $price = Price::create($request->all());

        return response()->json([
            'code' => 200,
            'message' => 'Price created successfully.',
        ], 200);
    }





    public function editPrice(Request $request, string $id)
    {
        $price = Price::find($id);
        if (!$price) {
            return response()->json([
                'code' => 404,
                'message' => 'Price not found.',
            ], 404);
        }

        $request->validate([
            'code' => 'required|string|unique:prices,code,' . $price->id,
            'price' => 'required|numeric',
        ]);

        $price->update($request->all());

        return response()->json([
            'code' => 200,
            'message' => 'Price updated successfully.',
        ]);
    }





    public function deletePrice(string $id)
    {
        $price = Price::find($id);
        if (!$price) {
            return response()->json([
                'code' => 404,
                'message' => 'Price not found.',
            ], 404);
        }

        $price->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Price deleted successfully.',
        ]);
    }
}
