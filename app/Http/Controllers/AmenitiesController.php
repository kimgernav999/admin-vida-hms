<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amenities;
use App\Models\AmenitiesCategory;

class AmenitiesController extends Controller
{
    public function create(Request $request) {
        $category = AmenitiesCategory::where('category_name', $request->category_name)->first();

        $amenities = Amenities::create([
            'amenities_name' => $request->amenities_name,
            'category_id' => $category->category_id,
            'description' => $request->description,
            'features' => json_encode($request->features),
            'image_ids' => json_encode($request->image_ids)
        ]);

        $message = $amenities ? 'Amenities record saved!' : 'Amenities record save failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function update(Request $request) {
        $category = AmenitiesCategory::where('category_name', $request->category_name)->first();
        $amenities = Amenities::where('amenities_id', $request->amenities_id)->first();
        $amenities->amenities_name = $request->amenities_name;
        $amenities->category_id = $category->category_id;
        $amenities->description = $request->description;
        $amenities->features = json_encode($request->features);
        $amenities->image_ids = json_encode($request->image_ids);
        $message = $amenities->save() ? 'Amenities record updated!' : 'Amenities record update failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function delete(Request $request) {
        $amenities = Amenities::where('amenities_id', $request->amenities_id)->first();
        $message = $amenities->delete() ? 'Amenities record deleted!' : 'Amenities record delete failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function allAmenities(Request $request) {
        return Amenities::with('category')->get();
    }
}
