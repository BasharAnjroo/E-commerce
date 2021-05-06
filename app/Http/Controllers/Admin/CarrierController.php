<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarrierRequest;
use App\Http\Requests\UpdateCarrierRequest;
use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Image;
class CarrierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('Carrier_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new CarrierResource(Carrier::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCarrierRequest $request)
    {
        abort_if(Gate::denies('Carrier_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carrier = Carrier::create($request->all());
        $this->image_store($carrier,$request);
        return (new CarrierResource($carrier))
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);
        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('Carrier_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new CarrierResource(Carrier::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCarrierRequest $request, $id)
    {
        abort_if(Gate::denies('Carrier_edit'),Response::HTTP_FORBIDDEN,'403 Forbidden');

        $currency = Carrier::findOrFail($id);
        $currency->update($request->all());
        return (new CarrierResource($currency))
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
        abort_if(Gate::denies('Carrier_delete'),Response::HTTP_FORBIDDEN,'403 Forbidden');
        $Carrier = Carrier::findOrFail($id);
        $image_path =public_path('img/Carriers/' . $Carrier->logo);
        unlink($image_path);
        $Carrier->delete();

        return response()->json(response(null, Response::HTTP_NO_CONTENT));
    }

    public function image_store($user ,$request)
    {
                    if ($request->hasFile('logo')) {
                        $file =$request->file('logo');

                        $filename  = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $filename   = $filename;
                        //move image to public/img folder
                        $image_resize = $user->update(['logo'=>$filename]);
                        $image_resize = Image::make($file->getRealPath());
                        $image_resize->resize(80, 80);
                        $path = public_path('img/Carriers/' . $filename);
                        $image_resize->save($path);
                    }
                    else {
                         $validated = $request->messages();
                        return response()->json(["message" => $validated]);
                    }

    }
}
