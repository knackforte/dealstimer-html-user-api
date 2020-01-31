<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductType;
use App\ProductCategory;
use App\ProductSubcategory;
use Validator;
use Image;
use Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name'                   => 'required',
            'price'                  => 'required',
            'discount'               => 'required',
            'sale_price'             => 'required',
            'permalink'              => 'required',
            'type'                   => 'required',
            'category'               => 'required',
            'sub_category'           => 'required'
        );
        $messages = array(
            'name.required'            => 'Product name is required.',
            'price.required'           => 'Price is required.',
            'discount.required'        => 'Discount is required.',
            'sale_price.required'      => 'Sale Price is required.',
            'permalink.required'       => 'Product URl is required.',
            'type.required'            => 'Product Type is required.',
            'category.required'        => 'Product Category is required.',
            'sub_category.required'    => 'Product Sub Category is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors()->first(),400);
        }

        $product = new Product;
        $product_type_obj = new ProductType;
        $product_category_obj  = new ProductCategory;
        $product_subcategory_obj  = new ProductSubcategory;

        $product->name = $request->get("name");
        $product->permalink = $request->get("permalink");
        $product->price = $request->get("price");
        $product->sale_price = $request->get("sale_price");
        if(!empty($request->get("in_stock"))){
            $product->in_stock = 1;
        }else{
            $product->in_stock = 0;
        }
        $product->discount = $request->get("discount");

        $product_type_obj = ProductType::where('name',$request->get("type"))->first();
        $product_category_obj = ProductCategory::where('name',$request->get("category"))->first();
        $product_subcategory_obj = ProductSubcategory::where('name',$request->get("sub_category"))->first();
        
        $product->type = $product_type_obj->id;
        $product->category = $product_category_obj->id;
        $product->sub_category = $product_subcategory_obj->id;

        if($request->file('picture') != null)
        {
            $image = $request->file('picture');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $img = Image::make($image->getRealPath());
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $filename);
            $product->picture = $filename;
        }

        if($product->save()){
            return response()->json($product,201);
        }else{
            return response()->json(["message" => "Error occurs while processing request!"],400);
        }
    }

    public function storeViaModal(Request $request){
        $rules = array(
            'name'                   => 'required',
            'price'                  => 'required',
            'discount'               => 'required',
            'sale_price'             => 'required',
            'permalink'              => 'required',
            'type'                   => 'required',
            'category'               => 'required',
            'sub_category'           => 'required'
        );
        $messages = array(
            'name.required'            => 'Product name is required.',
            'price.required'           => 'Price is required.',
            'discount.required'        => 'Discount is required.',
            'sale_price.required'      => 'Sale Price is required.',
            'permalink.required'       => 'Product URl is required.',
            'type.required'            => 'Product Type is required.',
            'category.required'        => 'Product Category is required.',
            'sub_category.required'    => 'Product Sub Category is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors()->first(),400);
        }

        $product = new Product;
        $product_type_obj = new ProductType;
        $product_category_obj  = new ProductCategory;
        $product_subcategory_obj  = new ProductSubcategory;

        $product->name = $request->get("name");
        $product->permalink = $request->get("permalink");
        $product->price = $request->get("price");
        $product->sale_price = $request->get("sale_price");
        if(!empty($request->get("in_stock"))){
            $product->in_stock = 1;
        }else{
            $product->in_stock = 0;
        }
        $product->discount = $request->get("discount");

        $product_type_obj = ProductType::where('name',$request->get("type"))->first();
        $product_category_obj = ProductCategory::where('name',$request->get("category"))->first();
        $product_subcategory_obj = ProductSubcategory::where('name',$request->get("sub_category"))->first();
        
        $product->type = $product_type_obj->id;
        $product->category = $product_category_obj->id;
        $product->sub_category = $product_subcategory_obj->id;

        $imageURL = $request->get('picture');
        $contents = file_get_contents($imageURL);
        $tmpExt = explode(".",$imageURL);
        $filename = time().'.'.end($tmpExt);
        $file = '/'.$filename;
        Storage::put($file, $contents);

        if(!Storage::disk('public_uploads')->put($file, $contents)) {
            return response()->json(["message" => "Error occurs while processing request!"],400);
        }

        $product->picture = $filename;

        if($product->save()){
            return response()->json($product,201);
        }else{
            return response()->json(["message" => "Error occurs while processing request!"],400);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getProductsFromSharafDGAPI(Request $request){
        $data = $request->getContent();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://9khjlg93j1-dsn.algolia.net/1/indexes/*/queries?x-algolia-agent=Algolia%2520for%2520vanilla%2520JavaScript%25203.24.9%253BJS%2520Helper%25202.23.2&x-algolia-application-id=9KHJLG93J1&x-algolia-api-key=e81d5b30a712bb28f0f1d2a52fc92dd0",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                "Referer: https://uae.sharafdg.com/",
                'Content-Type: application/x-www-form-urlencoded'
            ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
