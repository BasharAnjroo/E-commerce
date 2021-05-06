<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest as StoreR;
use App\Http\Requests\UserUpdateRequest as UpdateR;
use App\Http\Resources\UserResource;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
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
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new UserResource(User::with(['roles'])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreR $request)
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->handlePasswordInput($request);
        $user = User::create($request->all());
        $user->roles()->sync($request->input('role'));
        $this->image_store($user,$request);
        return (new UserResource($user))
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
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user= User::findOrFail($id);
        return new UserResource($user->load(['roles']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateR $request, $id)
    {
        $this->handlePasswordInput($request);
        $user = User::findOrFail($id);
        $user->update($request->all());
        $user->roles()->sync($request->input('role'));
        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    protected function handlePasswordInput(Request $request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }
    }
    public function image_store($user ,$request)
    {
                    if ($request->hasFile('p_image')) {

                        $file =$request->file('p_image');
                        $filename  = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $filename   = $filename;
                        //move image to public/img folder
                        $image_resize = $user->update(['p_image'=>$filename]);
                        $image_resize = Image::make($file->getRealPath());
                        $image_resize->resize(50, 50);
                        $path = public_path('img/Avatar/' . $filename);
                        $image_resize->save($path);
                    }
                    else {
                         $validated = $request->messages();
                        return response()->json(["message" => $validated]);
                    }

    }
}
