<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return new PostResource(true, 'List Data Posts', 200, $posts);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
            'author'    => 'required',
            'post_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');

        if ($image) {
            $image->storeAs('public/posts', $image->hashName());
            $post = Post::create([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'post_date' => $request->post_date,
            ]);
        } else {
            $post = Post::create([
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'post_date' => $request->post_date,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', 200, $post);
    }

    /**
     * show
     *
     * @param  mixed $post
     * @return void
     */
    public function show(Post $post)
    {
        //return single post as a resource
        return new PostResource(true, 'Data Post Ditemukan!', 200, $post);
    }
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Post $post)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
            'author'    => 'required',
            'post_date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            Storage::delete('public/posts/' . $post->image);

            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'post_date' => $request->post_date,
            ]);
        } else {
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'post_date' => $request->post_date,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah!', 200, $post);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Post $post)
    {
        Storage::delete('public/posts/' . $post->image);

        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus!', 200, null);
    }
}
