<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offers;

class OffersController extends Controller
{
    public function create(Request $request) {
        $offer = Offers::create([
            'offer_name' => $request->offer_name,
            'description' => $request->description,
            // 'room_types' => $request->room_types,
            'rates' => $request->rates,
            'image_ids' => json_encode($request->image_ids)
        ]);

        $message = $offer ? 'Offer record saved!' : 'Offer record save failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function update(Request $request) {
        $offer = Offers::where('offer_id', $request->offer_id)->first();
        $offer->offer_name = $request->offer_name;
        $offer->description = $request->description;
        // $offer->room_types = $request->room_types;
        $offer->rates = $request->rates;
        $offer->image_ids = json_encode($request->image_ids);

        $message = $offer->save() ? 'Offer record updated!' : 'Offer record update failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function delete(Request $request) {
        $offer = Offers::where('offer_id', $request->offer_id)->first();
        $message = $offer->delete() ? 'Offer record deleted!' : 'Offer record delete failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function allOffers(Request $request) {
        return Offers::all();
    }
}
