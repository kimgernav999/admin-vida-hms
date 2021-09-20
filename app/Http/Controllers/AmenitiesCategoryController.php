<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AmenitiesCategory;
use App\Models\Amenities;

class AmenitiesCategoryController extends Controller
{
    public function create(Request $request) {
        $category = AmenitiesCategory::create($request->all());
        $message = $category ? 'Amenities Category record saved!' : 'Amenities Category record save failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function update(Request $request) {
        $category = AmenitiesCategory::where('category_id', $request->category_id)->first();
        $category->category_name = $request->category_name;
        $category->description = $request->description;
        $message = $category->save() ? 'Amenities Category record updated!' : 'Amenities Category record update failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function delete(Request $request) {
        $category = AmenitiesCategory::where('category_id', $request->category_id)->first();
        $amenities = Amenities::where('category_id', $request->category_id)->first();
        $message = ($category->delete() && $amenities->delete()) ? 'Amenities Category with its related Amenities record deleted!' : 'Amenities Category record delete failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function allAmenitiesCategories(Request $request) {
        return AmenitiesCategory::all();
    }
}
