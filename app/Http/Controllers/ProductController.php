<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductType;
use App\ProductCategory;
use App\ProductSubcategory;
use App\ProductImage;
use App\ProductDetails;
use Validator;
use Image;
use Storage;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(DB::table('products')->join('product_category', 'products.category', '=', 'product_category.id')->join('product_images', 'products.id', '=', 'product_images.product_id')->join('vendor_product', 'products.id' , '=', 'vendor_product.product_id')->select('products.*','product_category.*','vendor_product.*')->get(),200);
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
            'type'                   => 'required',
            'category'               => 'required',
            'sub_category'           => 'required',
            'created_by'             => 'required'
        );
        $messages = array(
            'name.required'            => 'Product name is required.',
            'price.required'           => 'Price is required.',
            'type.required'            => 'Product Type is required.',
            'category.required'        => 'Product Category is required.',
            'sub_category.required'    => 'Product Sub Category is required.',
            'created_by.required'      => 'Product Owner is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors()->first(),400);
        }

        $product = new Product;
        $product_type_obj = new ProductType;
        $product_category_obj  = new ProductCategory;
        $product_subcategory_obj  = new ProductSubcategory;
        $product_images = new ProductImage;

        $product_type_obj = ProductType::where('name',$request->get("type"))->first();
        $product_category_obj = ProductCategory::where('name',$request->get("category"))->first();
        $product_subcategory_obj = ProductSubcategory::where('name',$request->get("sub_category"))->first();
        
        $product->name = $request->get("name");
        $product->type = $product_type_obj->id;
        $product->category = $product_category_obj->id;
        $product->sub_category = $product_subcategory_obj->id;
        $product->created_by = $request->get("created_by");

        $product->price = $request->get("price");

        if(!empty($request->get("in_stock"))){
            $product->in_stock = 1;
        }else{
            $product->in_stock = 0;
        }

        if($request->file('picture') != null)
        {
            $image = $request->file('picture');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $img = Image::make($image->getRealPath());
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $filename);
            $product_images->picture_url = $filename;
        }
        DB::beginTransaction();
        try{
            $product->save();
            $product_images->product_id = $product->id;
            $product_images->save();
        }catch(Exception $e){
            DB::rollback();
            return response()->json(["message" => "Error occurs while processing request!"],400);
        }
        DB::commit();
        return response()->json($product,201);
    }

    public function storeViaModal(Request $request){
        $rules = array(
            'name'                   => 'required',
            'price'                  => 'required',
            'permalink'              => 'required',
            'type'                   => 'required',
            'category'               => 'required',
            'sub_category'           => 'required'
        );
        $messages = array(
            'name.required'            => 'Product name is required.',
            'price.required'           => 'Price is required.',
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
        if($request->get("sale_price") !== "null"){
            $product->sale_price = $request->get("sale_price");
        }else{
            $product->sale_price = 0;
        }
        if(!empty($request->get("in_stock"))){
            $product->in_stock = 1;
        }else{
            $product->in_stock = 0;
        }

        if($request->get("discount") !== "null"){
            $product->discount = $request->get("discount");
        }else{
            $product->discount = 0;
        }
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

    public function getProductTypes(){
        return response()->json(ProductType::all(),200);
    }

    public function getProductCategories(Request $request){
        $typeName = $request->get('typeName');
        $typeObj = DB::table('product_type')->where('product_type.name', '=', $typeName)->get()->first();
        $typeId = $typeObj->id;
        return response()->json(DB::table('product_type_category')->join('product_type', 'product_type.id', '=', 'product_type_category.product_type')->join('product_category','product_category.id', '=', 'product_type_category.product_category')->where('product_type_category.product_type','=',$typeId)->select('product_category.name','product_category.display_name')->get(),200);
    }

    public function getProductSubcategories(Request $request){
        $catName = $request->get('catName');
        $catObj = DB::table('product_category')->where('product_category.name', '=', $catName)->get()->first();
        $catId = $catObj->id;
        return response()->json(DB::table('product_category_subcategory')->join('product_category', 'product_category.id', '=', 'product_category_subcategory.product_category')->join('product_subcategory','product_subcategory.id', '=', 'product_category_subcategory.product_subcategory')->where('product_category_subcategory.product_category','=',$catId)->select('product_subcategory.name','product_subcategory.display_name')->get(),200);
    }

    public function getAdminProducts(Request $request){

    }
}
