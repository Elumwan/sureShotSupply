<div class="space-y-4">
    @forelse ($mediaItems as $media)
        @php
            $previewUrl = $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl();
        @endphp

        <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-white/10">
            <div class="flex min-w-0 items-center gap-4">
                <img
                    src="{{ $previewUrl }}"
                    alt="{{ $media->name ?: $media->file_name }}"
                    class="h-20 w-20 rounded-lg object-cover"
                />

                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-gray-950 dark:text-white">
                        {{ $media->name ?: $media->file_name }}
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }}
                        ·
                        {{ number_format($media->size / 1024, 1) }} KB
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                {{ $action->getModalAction('deleteMedia' . $media->getKey()) }}
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">
            No images have been uploaded yet.
        </p>
    @endforelse
</div>
