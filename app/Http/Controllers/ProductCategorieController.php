<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user()->current_store;

        $product_categorys = ProductCategorie::where('store_id', $user)->where('created_by', \Auth::user()->creatorId())->get();

        return view('product_category.index', compact('product_categorys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product_category.create');
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
        $pro_cat = ProductCategorie::where('name', $request->name)->where('store_id', Auth::user()->current_store)->first();

        if(!empty($pro_cat))
        {
            return redirect()->back()->with('error', __('Product Category Already Exist!'));
        }

        $this->validate(
            $request, ['name' => 'required|max:40',]
        );

        $name                           = $request['name'];
        $productCategorie               = new ProductCategorie();
        $productCategorie->name         = $name;
        $productCategorie['store_id']   = \Auth::user()->current_store;
        $productCategorie['created_by'] = \Auth::user()->creatorId();
        $productCategorie->save();

        return redirect()->back()->with('success', __('Product Category added!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProductCategorie $productCategorie
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategorie $productCategorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ProductCategorie $productCategorie
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategorie $productCategorie)
    {
        return view('product_category.edit', compact('productCategorie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ProductCategorie $productCategorie
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCategorie $productCategorie)
    {
        $this->validate(
            $request, ['name' => 'required|max:40',]
        );
        $productCategorie['name']       = $request->name;
        $productCategorie['created_by'] = \Auth::user()->creatorId();
        $productCategorie->update();

        return redirect()->back()->with(
            'success', __('Product Category updated!')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ProductCategorie $productCategorie
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCategorie $productCategorie)
    {
        $product = Product::where('product_categorie', $productCategorie->id)->get();

        if($product->count() != 0)
        {
            return redirect()->back()->with(
                'error', __('Category is used in products!')
            );
        }
        else
        {
            $productCategorie->delete();

            return redirect()->back()->with(
                'success', __('Product Category Deleted!')
            );
        }
    }
}
