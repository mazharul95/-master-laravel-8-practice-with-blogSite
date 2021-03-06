<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Models\BlogPost;

// [
//     'show' => 'view',
//     'create' => 'create',
//     'store' => 'create',
//     'edit' => 'update',
//     'update' => 'update',
//     'destroy' => 'delete',
// ]
class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        // $posts = [
        //     1 => [
        //         'title' => 'Intro to Laravel',
        //         'content' => 'This is a short intro to Laravel',
        //         'is_new' => true,
        //         'has_comments' => true,
        //     ],
        //     2 => [
        //         'title' => 'Intro to PHP',
        //         'content' => 'This is a short intro to PHP',
        //         'is_new' => false,
        //     ],
        //     3 => [
        //         'title' => 'Intro to Golang',
        //         'content' => 'This is a short intro to Go',
        //         'is_new' => false,
        //     ],
        // ];

        return view('posts.index',
            ['posts' => BlogPost::withCount('comments')->get()]
        );
    }
    public function show($id)
    {
        //abort_if(!isset($this->posts[$id]), 404);

        return view('posts.show', [
            'post' => BlogPost::with('comments')->FindOrFail($id)]);
    }

    public function create()
    {
        //$this->authorize('posts.create');

        return view('posts.create');
    }

    public function store(StorePost $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;
        $post = BlogPost::create($validatedData);

        $request->session()->flash('status', 'the blog post was created');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

   
    public function edit($id)
    {
        $post = BlogPost::FindOrFail($id);
        $this->authorize($post);

        return view('posts.edit', ['post' => $post]);
    }

    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $this->authorize($post);

        $validatedData = $request->validated();
        $post->fill($validatedData);
        $post->save();

        $request->session()->flash('status', 'the blog post was Updated!');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You can't delete this blog post!");
        // }
        $this->authorize($post);

        $post->delete();

        session()->flash('status', 'the blog post was deleted! ');

        return redirect()->route('posts.index');
    }
}
