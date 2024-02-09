<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form class="form" method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data">
            @csrf
            <h1 class="form-text">Créer un article</h1>
            <input type="text" name="title" placeholder="{{ __('title') }}" class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" value="{{ old('title') }}">
            <textarea name="message" placeholder="{{ __('message') }}" class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('message') }}</textarea>
            <div class="add-image">
                <p class="form-text">Ajouter une image : </p>
                <input type="file" name="img" enctype="multipart/form-data" accept="image/*" class="input-img">
            </div>
            <div class="form-group">
                <label class="form-text">Tags :</label><br>
                <div class="tag-list">
                    @foreach($tags as $tag)
                    <label class="form-text"><input type="checkbox" name="tags[]" value="{{ $tag->id }}"> {{ $tag->name }}</label><br>
                    @endforeach
                </div>
            </div>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <x-primary-button class="mt-4">{{ __('Poster') }}</x-primary-button>
        </form>

        <div class="article-list">
            @foreach ($articles as $article)
            <!-- <a href="{{ $article->page }}"> -->
            <div class="row mt-n5">
                @if ($article->user->is(auth()->user()))
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="button">
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
                <a href="{{ route('article.show', $article->id) }}">
                    <div class="article" data-wow-delay=".2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                        <div class="blog-grid">
                            <div class="blog-grid-img position-relative"><img class="image" src="{{ asset('storage/images/' . $article->img) }}" alt="Article Image"></div>
                            <div class="blog-grid-text p-4">
                                <h3 class="h5 mb-3t title "><b>{{ $article->title }}</b></h3>
                                <p class="display-30">{{ $article->message }}</p>
                                <div class="meta meta-style2">
                                    <div class="user-list">
                                        <div class="div-tag"><i class="fas fa-tags"></i>
                                            @foreach($article->tags as $tag)
                                            <span class="tag">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                        <div class="article-bot">
                                            <div><i class="fas fa-calendar-alt"></i> {{ $article->created_at->format('j M Y, g:i a') }}</div>
                                            <div><i class="fas fa-user"></i> By {{ $article->user->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- </a> -->
            @endforeach
        </div>
    </div>
</x-app-layout>