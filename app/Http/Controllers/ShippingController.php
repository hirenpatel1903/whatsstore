<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shipping;
use App\Models\Store;
use App\Exports\ShippingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $user = \Auth::user()->current_store;
        
        $shippings = Shipping::where('store_id', $user)->where('created_by', \Auth::user()->creatorId())->get();
        $locations = Location::where('store_id', $user)->where('created_by', \Auth::user()->creatorId())->get();

        return view('shipping.index', compact('shippings', 'locations'));
    }


      public function export()
    {
        $name = 'Orders_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ShippingExport(), $name . '.xlsx');

        return $data;
    }






    public function create()
    {
        // $locations = Location::get()->pluck('name', 'id');

        $current_store = session()->get('current_store');
        $user = Auth::user();
        if($current_store)
        {
            $store_id = $current_store;
        }
        else
        {
            $store_id = $user->current_store;
        }
        $locations = Location::where('store_id', $store_id)->get()->pluck('name', 'id');


        return view('shipping.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request, [
                        'name' => 'required|max:40',
                        'price' => 'required|numeric',
                    ]
        );

        $shipping              = new Shipping();
        $shipping->name        = $request->name;
        $shipping->price       = $request->price;
        $shipping->location_id = implode(',', $request->location);
        $shipping->store_id    = \Auth::user()->current_store;
        $shipping->created_by  = \Auth::user()->creatorId();
        $shipping->save();

        return redirect()->back()->with('success', __('Shipping added!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Shipping $shipping
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Shipping $shipping)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Shipping $shipping
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Shipping $shipping)
    {
        $locations = Location::get()->pluck('name', 'id');

        return view('shipping.edit', compact('shipping', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Shipping $shipping
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shipping $shipping)
    {
        $this->validate(
            $request, [
                        'name' => 'required|max:40',
                        'price' => 'required|numeric',
                    ]
        );

        $shipping->name        = $request->name;
        $shipping->price       = $request->price;
        $shipping->location_id = implode(',', $request->location);
        $shipping->created_by  = \Auth::user()->creatorId();
        $shipping->save();

        return redirect()->back()->with('success', __('Shipping Updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Shipping $shipping
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shipping $shipping)
    {

        $shipping->delete();

        return redirect()->back()->with('success', __('Shipping Deleted!'));
    }
}
