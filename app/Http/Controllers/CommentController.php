<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment->update($validatedData);

        return redirect()->route('article.show', $comment->article_id);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back();
    }

    public function edit(Comment $comment)
    {
        // Vérifiez si l'utilisateur est autorisé à modifier le commentaire
        $this->authorize('update', $comment);

        // Affichez la vue de modification du commentaire avec les données du commentaire
        return view('comments.edit', compact('comment'));
    }
}
