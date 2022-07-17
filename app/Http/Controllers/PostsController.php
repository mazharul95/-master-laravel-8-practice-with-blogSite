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
        return view('posts.index',
            ['posts' => BlogPost::withCount('comments')->get()]
        );
        //query builder
        //return view('posts.index', ['posts' => BlogPost::orderBy('created_at', 'desc')->take(5)->get()]);
    }

    public function create()
    {
        //$this->authorize('posts.create');

        return view('posts.create');
    }

    public function store(StorePost $request)
    {
        $validated = $request->validated();
        $post = BlogPost::create($validated);

        $request->session()->flash('status', 'the blog post was created');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    public function show($id)
    {
        //abort_if(!isset($this->posts[$id]), 404);

        return view('posts.show', [
            'post' => BlogPost::with('comments')->FindOrFail($id)]);
    }

    public function edit($id)
    {
        $post = BlogPost::FindOrFail($id);

        $this->authorize($post);

        return view('posts.edit', ['post' => BlogPost::findOrfail($id)]);
    }

    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $this->authorize($post);

        $validated = $request->validated();
        $post->fill($validated);
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
