<x-app-layout>
    @unless ($article->created_at->eq($article->updated_at))
    <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
    @endunless
    <div class="article-detail">
        <div class="article-div">

            <div class="button-edit">
                @if ($article->user->is(auth()->user()))
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('articles.edit', $article)">
                            {{ __('Edit') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('articles.destroy', $article) }}">
                            @csrf
                            @method('delete')
                            <x-dropdown-link :href="route('articles.destroy', $article)" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Delete') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endif
            </div>
            <h2 class="title-article">{{ $article->title }}</h2>
            <img class="image-article" src="{{ asset('storage/images/' . $article->img) }}" alt="{{ $article->title }}">
            <p>{{ $article->message }}</p>
            <div><i class="fas fa-tags"></i> Tags:
                @foreach($article->tags as $tag)
                <span class="tag">{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="comment-section">
        <form class="comment-input" method="POST" action="{{ route('articles.comments.store', $article) }}">
            @csrf
            <textarea class="text-area" name="content" placeholder="Votre commentaire"></textarea>
            <button class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 " type="submit">Poster</button>
        </form>
        <ul class="comment-list">
            @foreach ($article->comments as $comment)
            <li class="comment">
                <div>
                    <p class="text-sm text-gray-500">Publié par : {{ $comment->user->name }}</p>
                    <p>{{ $comment->content }}</p>
                </div>
                @if ($comment->user->is(auth()->user()))
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg-comment" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('comments.edit', $comment)">
                            {{ __('Edit') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                            @csrf
                            @method('delete')
                            <x-dropdown-link :href="route('comments.destroy', $comment)" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Delete') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

</x-app-layout>