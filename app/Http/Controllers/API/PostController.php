<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\http\Requests\CreatePost;
use App\http\Requests\UpdatePost;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if (Gate::allows('isSuperAdmin')) {
                $post = Post::with('user')->get();
            }
            if (Gate::allows('isUser')) {
                $post = Post::where('user_id', $user->id)->get();
            }
            return $this->success(Config::get('constants.POST.POST_LIST'), 200, $post);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(CreatePost $request)
    {
        try{
            if ($request->image) {
                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
            }
            $input_array = $request->all();
            $input_array['image'] = $imageName;
            $input_array['user_id'] = Auth::id();

            $post = Post::create($input_array);
            return $this->success(Config::get('constants.POST.POST_ADD'), 200, $post);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * edit the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $post = Post::find($id);
            return $this->success(Config::get('constants.POST.POST_DETAIL'), 200, $post);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdatePost $request)
    {
        try {
            $input_array = $request->all();
            if ($request->image) {
                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $input_array['image'] = $imageName;
            }
            $post = Post::find($id);
            $post->update($input_array);
            return $this->success(Config::get('constants.POST.POST_EDIT'), 200, $post);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $post = Post::find($id);
            $post->delete();
            return $this->success(Config::get('constants.POST.POST_DELETE'), 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }
}
