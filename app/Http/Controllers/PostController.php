<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailReseource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return PostDetailReseource::collection($posts->loadMissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content'])); //collection menampilkan data dari resource "PostResource" lebih dari pada satu & loadMissing berguna untuk magngil sesuai kebutuhan
    }

public function show($id)
{
    $post = Post::with('writer:id,username')->findOrFail($id); //  "with" berguna memanggil relasi yang terhubung pada Model Post menggunakan ralasi belongsTo, "id,username" itu hanya ditampilkan, 
    return new PostDetailReseource($post->loadMissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content'])); // new menampilakan data dari "PostDetailResource" berdasarkan id url atau hanya satu
}

public function store(Request $request)
{
    
    $validated =  $request->validate([
        'title' => 'required|max:255',
        'news_content' => 'required'
    ]);

   $image = null;
    if($request->file)
    {
        $filename = $this->generateRandomString(); 
        $extension = $request->file->extension();
        $image = $filename.'.'.$extension;
        Storage::putFileAs('image', $request->file, $image);
    }
  
    $request['image'] = $image;
    $request['author'] = Auth::user()->id;
    $post = Post::create($request->all());
    return new PostDetailReseource($post->loadMissing('writer:id,username'));  // new menampilakan data dari "PostDetailResource" berdasarkan id url atau hanya satu & loadMissing berguna untuk manggil sesuai kebutuhan

}

public function update(Request $request, $id)
{
    $validated =  $request->validate([
        'title' => 'required|max:255',
        'news_content' => 'required'
    ]);

    $post = Post::findOrFail($id);
    $post->update($request->all());
    return new PostDetailReseource($post->loadMissing('writer:id,username'));  // new menampilakan data dari "PostDetailResource" berdasarkan id url atau hanya satu & loadMissing berguna untuk manggil sesuai kebutuhan

}

public function destroy($id)
{
    $post = Post::findOrFail($id);
    $post->delete();
    return new PostDetailReseource($post->loadMissing('writer:id,username'));  // new menampilakan data dari "PostDetailResource" berdasarkan id url atau hanya satu & loadMissing berguna untuk manggil sesuai kebutuhan

}

//genarator string name image
function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



}
