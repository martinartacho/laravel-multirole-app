<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <i class="bi bi-people-fill mr-2"></i>
                {{ __('site.User Management') }}
            </h2>
           
           
            @can('create-notification')
            <a href="{{ route('notifications.create') }}">
                <x-primary-button>
                    <i class="bi bi-plus-lg mr-1"></i>{{ __('site.Create Notification') }} 
                </x-primary-button>
            </a>
            @endcan
           
        </div>
    </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Contenido de la vista index -->
                    <table class="table">
                        <thead>
                            <tr>

                                <th class="text-left px-2 py-2 w-1/3">{{ __('site.Title') }} </th>
                                <th class="text-left  px-2 py-2 w-2/20">{{ __('site.Sender') }}</th>
                                <th class="text-left  px-2 py-2 w-2/20">{{ __('site.Recipient') }}</th>
                                <th class="text-left  px-2 py-2 w-2/20">{{ __('site.Published') }}</th>
                                <th class="text-left  px-2 py-2 w-1/5">{{ __('site.registration_date') }}</th>
                                <th class="text-left  px-2 py-2 w-2/20">{{ __('site.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                            <tr>
                                <td>{{ $notification->title }}</td>
                                <td>{{ $notification->sender->name }}</td>
                                @can('view-notification', $notification)
                                <td>
                                    @if($notification->recipient_type === 'all')
                                        {{ __('site.All_users') }}
                                    @elseif($notification->recipient_type === 'role')
                                        {{ __('site.Roles') }}: {{ $notification->recipient_role }}
                                    @else 
                                        {{ count($notification->recipient_ids) }}  {{ __('site.Users') }}
                                    @endif
                                </td>
                                <td>
                                    @if($notification->is_published)
                                        <span class="badge bg-success">Publicada {{ __('site.Published') }}</span>
                                    @else
                                        <span class="badge bg-warning">Pendiente {{ __('site.Not_published') }}</span>
                                    @endif
                                </td>
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-2 py-2 w-2/20">
                                    <div class="flex gap-x-1 items-center">
                                        @can('view-notification', $notification)
                                            <a href="{{ route('notifications.show', $notification) }}" 
                                                class="btn btn-sm btn-info"
                                                title="{{ __('site.View_notification') }}">
                                                <i class="bi bi-eye-slash"></i>
                                            </a>
                                        @endcan

                                        @can('edit-notification', $notification)
                                            <a href="{{ route('notifications.edit', $notification) }}" 
                                                class="btn btn-sm btn-primary" 
                                                title="{{ __('site.Edit_notification') }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan

                                        @can('delete-notification', $notification)
                                            <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Estas seguro?')"
                                                    class="btn btn-sm btn-danger" 
                                                    title="{{ __('site.Delete_notification') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>