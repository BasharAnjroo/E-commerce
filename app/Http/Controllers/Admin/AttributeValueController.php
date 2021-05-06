<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Attribute_valueResource;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('Attributevalue_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new Attribute_valueResource(AttributeValue::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('Attributevalue_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $Attribute = AttributeValue::create([
                'attribute_id' => $request->attribute_id,
                'value' => $request->value,
                'code' => $request->code,
            ]);
            return (new Attribute_valueResource($Attribute))
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
       abort_if(Gate::denies('Attributevalue_show'),Response::HTTP_FORBIDDEN,'403 Forbidden');
       return new Attribute_valueResource(AttributeValue::where('attribute_id','=',$id)->get());
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
        abort_if(Gate::denies('Attributevalue_edit'),Response::HTTP_FORBIDDEN,'403 Forbidden');

        $Attribute = AttributeValue::findOrFail($id);
        $Attribute->update($request->all());
        return (new Attribute_valueResource($Attribute))
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
        abort_if(Gate::denies('Attributevalue_delete'),Response::HTTP_FORBIDDEN,'403 Forbidden');

        $Attribute = AttributeValue::findOrFail($id);
        $Attribute->delete();
        return response()->json(response(null, Response::HTTP_NO_CONTENT));
    }
}
