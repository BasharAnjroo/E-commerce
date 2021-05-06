<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\AttributeSet;
use App\Models\AttributeValue;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductImage;
use App\Models\SpecificPrice;
use Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Resource_;
use Symfony\Component\HttpFoundation\Response;
use Image;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
{
    /*
    * ========= Writen By Bashar Anjroo.=========
    */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return response()->json(new ProductResource(Product::with('images')->with('city')->with('categories')->with('attributes')->with('specificPrice')->with('tax')->get()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request, ProductGroup $productGroup, SpecificPrice $specificPrice)
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $productGroup = $productGroup->create();
        $request->merge([
            'group_id' => $productGroup->id
        ]);
        $Product = Product::create($request->all());

        $name_attributes_Value = AttributeSet::findOrFail($Product->attribute_set_id)->first()->name;

        $city_id = $request->input('city_id');
        $city = City::findOrFail($city_id)->first();

        $Product->update([
            'City' => $city->name
        ]);
        $Product->update([
            'attribute_set' => $name_attributes_Value
        ]);

        $this->image_store($Product, $request);
        // Save product's attribute values
        if ($request->input('category_id')) {
            $Product->categories()->attach($request->input('category_id'));
        }
        if ($request->input('attributes')) {
            foreach ($request->input('attributes') as $key) {
                foreach ($request->input('attributes_values') as $id_value) {
                    $value = AttributeValue::findOrFail($id_value)->value;

                    if ($value) {
                        $Product->attributes()->attach([$key => ['value' => $value]]);
                    } else {
                        $Product->attributes()->attach([$key => ['value' => null]]);
                    }
                }
            }
        }
        if ($request->input('discount_type') != null) {
            $productId = $Product->id;
            $reduction = $request->input('reduction');

            $discountType = $request->input('discount_type');
            $startDate = $request->input('start_date');
            $expirationDate = $request->input('expiration_date');
            if (!$request->has('start_date') || !$request->has('expiration_date')) {

                return response()->json("start_date and expiration_date not null ");
            }

            // Check if a specific price reduction doesn't already exist in this period
            if (!$this->validateProductDates($productId, $startDate, $expirationDate)) {
                $product = Product::find($productId);
                $productName = $product->name;
                return response()->json("wrong dates ");
            }

            // Check if the price after reduction is not less than 0
            if ($request->has('reduction') && $request->has('discount_type')) {

                if (!$this->validateReductionPrice(
                    $productId,
                    $reduction,
                    $discountType
                )) {
                    return response()->json("reduction price not ok");
                } else {
                    // Save specific price
                    $specificPrice->discount_type = $discountType;
                    $specificPrice->reduction = $reduction;
                    $specificPrice->start_date = $startDate;
                    $specificPrice->expiration_date = $expirationDate;
                    $specificPrice->product_id = $productId;
                    $specificPrice = $specificPrice->save();
                }
            }
        }
        return response()->json("Success ADD ");
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $product = Product::where('id', '=', $id)->with('images')->with('categories')->with('attributes')->with('specificPrice')->with('tax')->first();
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, ProductGroup $productGroup, SpecificPrice $specificPrice)
    {

        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $product = Product::findOrFail($id);

        $name_attributes_Value = AttributeSet::findOrFail($request->attribute_set_id)->first()->name;

        $product->update([
            'attribute_set' => $name_attributes_Value
        ]);
        $this->image_store($product, $request);

        // Save product's attribute values
        if ($request->input('category_id')!=null) {
            $product->categories()->detach();
            $product->categories()->attach($request->input('category_id'));
        }
        $product->update($request->all());
        if ($request->input('attributes')) {
            foreach ($request->input('attributes') as $key) {
                foreach ($request->input('attributes_values') as $id_value) {
                    $value = AttributeValue::findOrFail($id_value)->value;
                    if ($value) {
                        $product->attributes()->attach([$key => ['value' => $value]]);
                    } else {
                        $product->attributes()->attach([$key => ['value' => null]]);
                    }
                }
            }
        }
        if ($request->input('discount_type') != null) {
            $productId = $product->id;
            $reduction = $request->input('reduction');

            $discountType = $request->input('discount_type');
            $startDate = $request->input('start_date');
            $expirationDate = $request->input('expiration_date');
            if (!$request->has('start_date') || !$request->has('expiration_date')) {

                return response()->json("start_date and expiration_date not null ");
            }

            // Check if a specific price reduction doesn't already exist in this period
            if (!$this->validateProductDates($productId, $startDate, $expirationDate)) {
                $product = Product::find($productId);
                $productName = $product->name;
                return response()->json("wrong dates ");
            }

            // Check if the price after reduction is not less than 0
            if ($request->has('reduction') && $request->has('discount_type')) {

                if (!$this->validateReductionPrice(
                    $productId,
                    $reduction,
                    $discountType
                )) {
                    return response()->json("reduction price not ok");
                } else {
                    // Save specific price
                    $specificPrice->discount_type = $discountType;
                    $specificPrice->reduction = $reduction;
                    $specificPrice->start_date = $startDate;
                    $specificPrice->expiration_date = $expirationDate;
                    $specificPrice->product_id = $productId;
                    $specificPrice = $specificPrice->save();
                }
            }
        }
        return response()->json("Success ADD ");
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $product = Product::findOrFail($id);
        $product->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * delete image Products (id)
     */
    public function image_delete($id)
    {

        $image = ProductImage::findOrFail($id);

        if (!empty($image)) {

            Storage::disk('uploads')->delete($image->name);
            $image->delete();
            return response()->json('Success Delete Image');
        } else {
            return response()->json('no image');
        }
    }

    /**
     * Store image Products
     */
    public function image_store($Product, $request)
    {
        if ($request->input('fileSource')) {
            foreach ($request->fileSource as $key => $value) {
                $extension = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];   // .jpg .png .pdf

                $replace = substr($value, 0, strpos($value, ',') + 1);

                // find substring fro replace here eg: data:image/png;base64,

                $image = str_replace($replace, '', $value);

                $image = str_replace(' ', '+', $image);

                $imageName = Str::random(10) . '.' . $extension;
                $img = Image::make($image);

                // Resize
                $img->resize(70, 70);

                // Base64 encoded stream. Also supports 'jpg', 'png' and more...
                $dataUrl = (string) $img->stream();
                Storage::disk('uploads')->put($imageName, $dataUrl);
                $image = $Product->images()->create([
                    'product_id' => $Product->id,
                    'name' => $imageName,
                ]);
            }
        }
    }
    /**
     * Validate if the price after reduction is not less than 0
     *
     * @return boolean
     */
    public function validateReductionPrice(
        $productId,
        $reduction,
        $discountType
    ) {

        $product = Product::find($productId);
        $oldPrice = $product->price;
        if ($discountType == 'Amount') {
            $newPrice = $oldPrice - $reduction;
        }
        if ($discountType == 'Percent') {
            $newPrice = $oldPrice - $reduction / 100.00 * $oldPrice;
        }

        if ($newPrice < 0) {
            return false;
        }
        return true;
    }

    /**
     * Check if it doesn't already exist a specific price reduction for the same
     * period for a product
     *
     * @return boolean
     */
    public function validateProductDates($productId, $startDate, $expirationDate)
    {
        $specificPrice = SpecificPrice::where('product_id', $productId)->get();

        foreach ($specificPrice as $item) {
            $existingStartDate = $item->start_date;
            $existingExpirationDate = $item->expiration_date;
            if (
                $expirationDate >= $existingStartDate
                && $startDate <= $existingExpirationDate
            ) {
                return false;
            }
            if (
                $expirationDate >= $existingStartDate
                && $startDate <= $existingExpirationDate
            ) {
                return false;
            }
            if (
                $startDate <= $existingStartDate
                && $expirationDate >= $existingExpirationDate
            ) {
                return false;
            }
        }

        return true;
    }
}
