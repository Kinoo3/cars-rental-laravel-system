<?php

namespace App\Http\Controllers;

use App\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $vehicles = Vehicle::latest()->paginate(10);
        return view('vehicles.index', compact('vehicles'))->with('i', (request()->input('page', 1) - 1) * 5);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Vehicle::class);
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'rental_agency_id' => 'required|integer',
            'manufacturer' => 'required|string',
            'model' => 'required|string',
            'year' => 'required',
            'chassi' => 'required|string|size:11',
            'status_id' => 'required|integer'
        ]);

        $dates =  $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $num = rand(1111, 9999);
            $dir = "img/vehicles/";
            $extension = $image->guessClientExtension();
            $imageName = "vc" . $num . "." . $extension;
            $image->move($dir, $imageName);
            $dates['image']  = $dir . "/" . $imageName;
        }

        Vehicle::create($dates);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->delete();

        return redirect('/vehicles')->with('success', 'Vehicle has been deleted Successfully');
    }
}
