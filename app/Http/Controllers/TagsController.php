<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Notice the mistake that you made, request doesn't have any relationship in, it's simply an http request.
        // Instead, to retrieve all tags, we check the authentication of the user, and from there we go to the relationship
        // between user and tags.
        $this->authorize('viewAny', Tag::class);

        $tags = auth()->user()->tags()->latest()->get();

        return view('tags', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        // Laravel injects the user model automatically in the create policy method.
        $this->authorize('create', Tag::class);

        auth()->user()->tags()->create($request->validated());

        return redirect()->route('tags.index')->with('success', 'tag created successfully');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTagRequest $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $tag->update($request->validated());

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);
        $tag->delete();

        return back()->with('success', 'Tag deleted.');
    }
}
