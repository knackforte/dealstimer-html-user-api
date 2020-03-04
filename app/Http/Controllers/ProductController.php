<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Product;
use App\ProductCategory;
use App\ProductImage;
use App\AllCategory;
use Validator;
use Image;
use Storage;
use DB;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('product')
                                ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
                                ->leftJoin('product_category', 'product.category_id', '=', 'product_category.category_id')
                                ->select('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id as product_parent','product.created_by','product.created_at','product.updated_at',
                                         'product_category.category_name','product_category.display_name','product_category.parent_id as category_parent',
                                          DB::raw("group_concat(product_images.picture_url) as images"))
                                ->groupBy('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id','product.created_by','product.created_at','product.updated_at',
                                          'product_category.category_name','product_category.display_name','product_category.parent_id')
                                ->get(),200);
        //return response()->json(DB::table('product_images')->select(DB::raw("group_concat(product_images.picture_url) as images"))->groupBy('product_images.product_id')->get(),200);
    }

    public function getCategories()
    {
        return response()->json($this->allCategory(0));   //0 means null
    }
    public function allCategory($parent_id)
    {
        if($parent_id === 0){
            $result = DB :: table('product_category')->select('product_category.*')->where('parent_id')->get();
        }else{
            $result = DB :: table('product_category')->select('product_category.*')->where('parent_id','=',$parent_id)->get();
        }
        $Categories = [];
        if($result !== null)
        {
            foreach($result as $res)
            {
                $obj = new AllCategory;
                $obj->name = $res->display_name;
                $obj->value = $res->category_name;
                $obj->children = $this->allCategory($res->category_id);
                $Categories[] = $obj;
            }
        }
        return $Categories;
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
            'name'         => 'required',
            'category_id'  => 'required',
            'created_by'   => 'required',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        );
        $messages = array(
            'name.required'            => 'Product name is required.',
            'category_id.required'     => 'Product Category is required.',
            'created_by.required'      => 'Product Owner is required.',
            'images.required'          => 'picture is required'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors()->first(),400);
        }

        $product = new Product;
             

        $product->name = $request->get("name");
        $product->category_id = $request->get("category_id");
        $product->created_by =  $request->get("created_by");

        DB::beginTransaction();
        try
        {
            $product->save();
            if($request->hasfile('images'))
            {
                foreach($request->file('images') as $image)   
                {
                    $product_images = new ProductImage;
                    $product_images->product_id = $product->id; 
                    //get file name with extension
                    $fileNameWithExtension = $image->getClientOriginalName();
                    //get just file name
                    $filename = pathinfo($fileNameWithExtension,PATHINFO_FILENAME);
                    //get just extension
                    $extension = $image->getClientOriginalExtension();
                    //file name to store
                    $FileNameToStore= $filename.'_'.time().'.'.$extension;

                    $product_images->picture_url = $FileNameToStore;
                    //upload image

                    $img = Image::make($image->getRealPath());
                    $destinationPath = public_path('/images');
                    $image->move($destinationPath, $FileNameToStore);
                    
                    $product_images->save();
                }
            }   
    }
    catch(Exception $e){
        DB::rollback();
        return response()->json(["message" => "Error occurs while processing request!"],400);
    }
    DB::commit();
        return response()->json(["message" => "Success"],200);
    }

    public function storeViaModal(Request $request){
        $rules = array(
            'name'              => 'required',
            'product_parent_id' => 'required',
            'permalink'         => 'required',
            'picture'           => 'required',
            'category_id'       => 'required',
            'created_by'        => 'required'
        );
        $messages = array(
            'name.required'              => 'Product name is required.',
            'product_parent_id.required' => 'Parent id is required.',
            'permalink.required'         => 'Product URl is required.',
            'picture.required'            => 'Image is required.',
            'category_id.required'            => 'Category Id is required.',
            'created_by.required'            => 'Created By Id is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors()->first(),400);
        }

        $product = new Product;
        $product->name = $request->get("name");
        $product->permalink = $request->get("permalink");
        $product->parent_id = $request->get("product_parent_id");
        $product->category_id = $request->get("category_id");
        $product->created_by = $request->get("created_by");
        

        $imageURL = $request->get('picture');
        $contents = file_get_contents($imageURL);
        $tmpExt = explode(".",$imageURL);
        $filename = time().'.'.end($tmpExt);
        $file = '/'.$filename;
        Storage::put($file, $contents);

        if(!Storage::disk('public_uploads')->put($file, $contents)) {
            return response()->json(["message" => "Error occurs while processing request!"],400);
        }
        DB::beginTransaction();
        try
        {
            $product->save();
            $product_images = new ProductImage;
            $product_images->product_id = $product->id; 
            $product_images->picture_url = $filename;
            $product_images->save();
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json(["message" => "Error occurs while processing request!"],400);
        }
        DB::commit();
        return response()->json(["message" => "success"],200);
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

    public function getProductsFromApiOrScrapper(Request $request)
    {
        $response = '';
        switch($request->user_id)
        {
            case 2:    //sharatDG
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
            break;

            case 3:
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => "http://localhost/twitter-php-scraper/examples/getProductdetail.php?".$request->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            break;
        }
        
        return $response;
    }
    
    public function getProductsByCategoryId(Request $request)
    {
        return response()->json(DB::table('product')
                        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
                        ->leftJoin('product_category', 'product.category_id', '=', 'product_category.category_id')
                        ->select('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id as product_parent','product.created_by','product.created_at','product.updated_at',
                                    'product_category.category_name','product_category.display_name','product_category.parent_id as category_parent',
                                    DB::raw("group_concat(product_images.picture_url) as images"))
                        ->groupBy('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id','product.created_by','product.created_at','product.updated_at',
                                    'product_category.category_name','product_category.display_name','product_category.parent_id')
                        ->where('product.category_id','=',$request->category_id)
                        ->get(),200);
        //return response()->json(DB::table('product_images')->select(DB::raw("group_concat(product_images.picture_url) as images"))->groupBy('product_images.product_id')->get(),200);
    }

    public function getProductsByVendorId(Request $request)
    {
        return response()->json(DB::table('product')
                        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
                        ->leftJoin('product_category', 'product.category_id', '=', 'product_category.category_id')
                        ->select('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id as product_parent','product.created_by','product.created_at','product.updated_at',
                                    'product_category.category_name','product_category.display_name','product_category.parent_id as category_parent',
                                    DB::raw("group_concat(product_images.picture_url) as images"))
                        ->groupBy('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id','product.created_by','product.created_at','product.updated_at',
                                    'product_category.category_name','product_category.display_name','product_category.parent_id')
                        ->where('product.created_by','=',$request->vendor_id)
                        ->get(),200);
    }

    public function getVendorProductsByCategoryId(Request $request)
    {
        return response()->json(DB::table('product')
                        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
                        ->leftJoin('product_category', 'product.category_id', '=', 'product_category.category_id')
                        ->select('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id as product_parent','product.created_by','product.created_at','product.updated_at',
                                    'product_category.category_name','product_category.display_name','product_category.parent_id as category_parent',
                                    DB::raw("group_concat(product_images.picture_url) as images"))
                        ->groupBy('product.product_id','product.name','product.price','product.permalink','product.category_id','product.parent_id','product.created_by','product.created_at','product.updated_at',
                                    'product_category.category_name','product_category.display_name','product_category.parent_id')
                        ->where('product.created_by','=',$request->vendor_id)
                        ->where('product.category_id','=',$request->category_id)
                        ->get(),200);
    }
}
