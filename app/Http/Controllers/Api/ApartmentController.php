<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all apartments owned by a logged in user
        $apartments = Apartment::with('user', 'services', 'sponsorships')->get();

        return $apartments;
    }

    public function show($id){
        
        try {
            $apartment = Apartment::where('id', $id)->with('services', 'user', 'sponsorships')->firstOrFail();
            return $apartment;  
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response([
                'error' => '404 Apartment Not Found',
            ], 404);
        }

        return $apartment;
    }
}
