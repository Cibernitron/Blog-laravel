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
        $tags = Tag::all(); // Récupérer tous les tags depuis la base de données

        return view('articles.index', [
            'articles' => $articles,
            'tags' => $tags, // Passer les tags à la vue
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
        // Valider les données de la requête
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'img' => 'required|image', // Ajoutez une validation pour vous assurer que le fichier téléchargé est une image
            'tags' => 'array', // Les tags doivent être un tableau
            'tags.*' => 'exists:tags,id', // Vérifiez que chaque tag existe dans la table des tags
        ]);

        // Récupérez le fichier téléchargé à partir de la requête
        $image = $request->file('img');

        // Générez un nom de fichier unique pour éviter les conflits de noms
        $imageName = uniqid() . '_' . $image->getClientOriginalName();

        // Déplacez le fichier vers le dossier de stockage public
        $image->storeAs('public/images', $imageName);

        // Créez un nouvel article et enregistrez-le dans la base de données
        $article = new Article();
        $article->title = $validated['title'];
        $article->message = $validated['message'];
        $article->img = $imageName; // Assignez le nom de fichier à la propriété img
        $article->user_id = $request->user()->id; // Assurez-vous d'attribuer l'ID de l'utilisateur approprié
        $article->save();

        // Associez les tags sélectionnés à l'article
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

        // Récupérer tous les tags depuis la base de données
        $tags = Tag::all();

        return view('articles.edit', [
            'article' => $article,
            'tags' => $tags, // Passer les tags à la vue
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        // Valider les données de la requête, y compris les tags sélectionnés
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'img' => 'nullable|image', // L'image est facultative lors de la mise à jour
            'tags' => 'array', // Les tags doivent être un tableau
            'tags.*' => 'exists:tags,id', // Vérifiez que chaque tag existe dans la table des tags
        ]);

        // Si une nouvelle image est téléchargée, traitez-la comme dans la méthode store
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/images', $imageName);
            $validated['img'] = $imageName;
        }

        // Mettez à jour les autres champs de l'article
        $article->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'img' => $validated['img'] ?? $article->img, // Utilisez l'image existante si aucune nouvelle image n'est téléchargée
        ]);

        // Mettez à jour les tags associés à l'article en utilisant la méthode sync
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
            'user_id' => auth()->id(), // ou tout autre logique pour attribuer l'utilisateur
        ]);

        return back(); // Rediriger l'utilisateur vers la page de l'article après avoir ajouté le commentaire
    }
}
