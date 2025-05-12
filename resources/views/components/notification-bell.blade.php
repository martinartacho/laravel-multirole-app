@auth
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <!-- Campana -->
    <button @click="open = !open" class="relative p-2 text-gray-700 hover:text-gray-900 focus:outline-none">
         <span class="inline-flex rounded-md">
            {{ __('Notifications') }}
        </span>
    <i class="bi bi-bell text-xl"></i> 

        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
        @if($unreadCount > 0)
            <span id="unread-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50"
         style="display: none;">

        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications->take(5) as $notification)
                <a href="{{ route('notifications.show', $notification) }}"
                   class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <div class="flex justify-between">
                        <span class="font-semibold">{{ $notification->title }}</span>
                        @if($notification->read_at === null)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ __('site.New') }}</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500">{{ __('site.No notifications') }}</div>
            @endforelse
        </div>

        <div class="border-t">
            <a href="{{ route('notifications.index') }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">{{ __('site.See_all') }}</a>
            <!-- <a href="#" class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-100 text-center mark-all-read">Marcar como leídas</a> -->
        </div>
    </div>
</div>
@endauth


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Marcar como leídas
    document.querySelector('.mark-all-read')?.addEventListener('click', function (e) {
        e.preventDefault();
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                document.getElementById('unread-count')?.remove();
                document.querySelectorAll('[data-new="true"]').forEach(el => el.remove());
            }
        });
    });

    // Actualizar cada 60s
    setInterval(() => {
        fetch('{{ route("notifications.unread-count") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            const counter = document.getElementById('unread-count');
            const bell = document.querySelector('button');
            if (data.count > 0) {
                if (!counter) {
                    const badge = document.createElement('span');
                    badge.id = 'unread-count';
                    badge.className = 'absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2';
                    badge.textContent = data.count;
                    bell.appendChild(badge);
                } else {
                    counter.textContent = data.count;
                }
            } else if (counter) {
                counter.remove();
            }
        });
    }, 60000);
});
</script>
@endpush
