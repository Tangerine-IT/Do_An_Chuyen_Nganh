<x-app-layout :title="'Blog - '.config('petshop.name')" :description="'Kiến thức chăm sóc thú cưng hữu ích từ PetShop.'">
    <div class="container-page py-10 sm:py-14">
        <div class="max-w-2xl">
            <p class="font-semibold text-accent-700">PetShop Journal</p>
            <h1 class="mt-2 text-4xl">{{ __('ui.blog.title') }}</h1>
            <p class="mt-3 text-ink-muted">{{ __('ui.blog.subtitle') }}</p>
        </div>

        <form method="GET" class="mt-8 flex flex-col gap-3 sm:flex-row" role="search">
            <input class="form-control sm:max-w-sm" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.blog.search') }}">
            <select class="form-control sm:max-w-xs" name="category">
                <option value="">{{ __('ui.blog.all_categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <x-button type="submit">{{ __('ui.search.submit') }}</x-button>
        </form>

        @if ($posts->isEmpty())
            <x-empty-state class="mt-10" :title="__('ui.blog.empty')" />
        @else
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
</x-app-layout>
