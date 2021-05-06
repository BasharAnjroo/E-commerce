<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['message' => 'NOT FOUND'], 404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'NOT FOUND'], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('profail_show_customer'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->id == $id) {
                $user = User::findOrFail($user->id);
                return new UserResource($user);
            }
            return response()->json(['message' => 'You do not have permission to access'], 404);
        } else {
            return response()->json(['message' => 'Please Login'], 404);
        }
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
        abort_if(Gate::denies('profail_update_customer'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->id == $id) {
                $profial = request()->validate([
                    'name' => 'required',
                    'gender' => 'required|string',
                    'birthday' => '',
                    'city' => '|string',
                    'address' => '',
                    'phone' => '|string',
                ]);
                $user = User::findOrFail($id);
                $user->update($profial);
                return (new UserResource($user))->response()->setStatusCode(Response::HTTP_ACCEPTED);
            } else {
                return response()->json(['message' => 'You do not have permission to access'], 404);
            }
        } else {
            return response()->json(['message' => 'Please Login'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['message' => 'NOT FOUND'], 404);
    }


    public function image_store($request)
    {
        if (Auth::check()) {
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
                    $user = Auth::user();
                    $image = $user->p_image;
                    if (!empty($image)) {
                        Storage::disk('avatar')->delete($image);
                    }
                    Storage::disk('avatar')->put($imageName, $dataUrl);
                    $user = User::findOrFail($user->id);
                    $image = $user->update(
                        ['p_image' => $imageName]
                    );
                }
            }
            return response()->json(['message' => 'not file']);
        } else {
            return response()->json(['message' => 'Please Login'], 404);
        }
    }
}
