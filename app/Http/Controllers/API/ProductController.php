<?php
       
namespace App\Http\Controllers\API;
       
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
       
class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $take = $request->input('take', 10);
        $skip = $request->input('skip', 0);
        $search = $request->input('search', '');

        $products = Product::where('name', 'like', '%' . $search . '%')
                     ->take($take)
                     ->skip($skip)
                     ->get();
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'detail' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ]);
        }

        $products = Product::create($request->all());
       
        return $this->sendResponse(new ProductResource($products), 'Product created successfully.');
    } 
     
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
      
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
       
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }
      
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'detail'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Validation Error',
                'error'     => $validator->errors()
            ], 422);
        }
        $product = Product::where('id', $id)->update([
                'name' => $request->get('name'),
                'detail' => $request->get('detail'),
        ]);
        return response()->json([
            'status'    => 'success',
            'message'   => 'Product update data successfully!',
            'data'   => $product
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        if ($product) {
            $product->delete();
            return $this->sendResponse([], 'Product soft deleted successfully.');
        } else {
            return $this->sendError('Product not found.');
        }
    }
}