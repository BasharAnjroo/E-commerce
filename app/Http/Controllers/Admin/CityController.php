<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
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
        abort_if(Gate::denies('City_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new CityResource(City::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('City_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $City = City::create($request->all());
        return (new CityResource($City))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('City_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $City = City::findOrFail($id);
        return new CityResource($City);
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
        $City = City::findOrFail($id);
        $City->update($request->all());
        return (new CityResource($City))
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
        abort_if(Gate::denies('City_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $City = City::findOrFail($id);
        $City->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
