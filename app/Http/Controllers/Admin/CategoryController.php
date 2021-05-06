<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Image;

class CategoryController extends Controller
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
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new CategoryResource(Category::with('parent')->get());
    }
/*
    public function SubCat_index()
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new CategoryResource(Category::where('parent_id','>',0)->get());
    } */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        abort_if(Gate::denies('category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $request->messages();

            $category = Category::create([
                'name' => $request['name'],
                'slug' => $request['slug'],
                'parent_id' => $request['parent_id'],
            ]);
            $this->image_store_update($category,$request);
            return response()->json([
                'message' => 'successfully added',
                'category' => $category
            ], 201);
        }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        abort_if(Gate::denies('category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $this->showOne($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        abort_if(Gate::denies('category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'successfully updated',
            'category' => $category,
        ], 201)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        abort_if(Gate::denies('category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $category->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function image_store($player ,$request)
    {
                    if ($request->hasFile('file')) {

                        $file =$request->file('file');
                        $filename  = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $picture   = $filename;
                        //move image to public/img folder
                        $image_resize = Image::make($file->getRealPath());
                        $image_resize->resize(50, 50);
                        $image_resize->save(public_path('img/Category/' . $picture));


                    }
                    else {
                         $validated = $request->messages();
                        return response()->json(["message" => $validated]);
                    }

    }
    public function image_store_update($player ,$request)
    {
                    if ($request->hasFile('url')) {
                        $file =$request->file('url');
                        $filename  = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $picture   = $filename;
                        //move image to public/img folder
                        $image_resize = Image::make($file->getRealPath());
                        $image_resize->resize(50, 50);
                        $image_resize->save(public_path('img/Category/' . $picture));
                    }


    }
}
