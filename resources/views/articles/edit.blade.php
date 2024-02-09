<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('articles.update', $article) }}" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <input type="text" name="title" placeholder="{{ __('Title') }}" class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" value="{{ $article->title }}">
            <textarea name="message" placeholder="{{ __('Message') }}" class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ $article->message }}</textarea>
            <input type="file" name="img" accept="image/*">
            <div class="form-group">
                <label class="form-text">Tags :</label><br>
                @foreach($tags as $tag)
                <label class="form-text"><input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ $article->tags->contains($tag) ? 'checked' : '' }}> {{ $tag->name }}</label><br>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <div class="mt-4 space-x-2">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('articles.index') }}"><x-primary-button>{{ __('Cancel') }}</x-primary-button></a>
            </div>
        </form>
    </div>
</x-app-layout>