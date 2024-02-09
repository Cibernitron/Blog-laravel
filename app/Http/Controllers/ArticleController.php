<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {

        $articles = Article::with('user', 'tags')->latest()->get();
        $tags = Tag::all();

        return view('articles.index', [
            'articles' => $articles,
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'img' => 'required|image',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        $image = $request->file('img');

        $imageName = uniqid() . '_' . $image->getClientOriginalName();

        $image->storeAs('public/images', $imageName);

        $article = new Article();
        $article->title = $validated['title'];
        $article->message = $validated['message'];
        $article->img = $imageName;
        $article->user_id = $request->user()->id;
        $article->save();

        $article->tags()->attach($validated['tags']);

        return redirect(route('articles.index'));
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        $tags = Tag::all();

        return view('articles.edit', [
            'article' => $article,
            'tags' => $tags,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'img' => 'nullable|image',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/images', $imageName);
            $validated['img'] = $imageName;
        }

        $article->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'img' => $validated['img'] ?? $article->img,
        ]);

        $article->tags()->sync($validated['tags']);

        return redirect(route('articles.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect(route('articles.index'));
    }

    public function storeComment(Request $request, Article $article)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $article->comments()->create([
            'content' => $validatedData['content'],
            'user_id' => auth()->id(),
        ]);

        return back();
    }
}
