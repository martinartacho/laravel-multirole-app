<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
    <h3 class="text-lg font-medium text-gray-900">{{ __('Manager') }}</h3>
        
        
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-dashboard.card title="{{ __('Users') }}" link="{{ route('gestor.users.index') }}" color="blue">
                Total: {{ App\Models\User::count() }}
            </x-dashboard.card>

            <x-dashboard.card title="{{ __('Roles.') }}" color="green">
                {{ Spatie\Permission\Models\Role::count() }} {{ __('roles') }}
            </x-dashboard.card>

            <x-dashboard.card title="Setting" color="purple">
            {{ __('Full access to the system') }} {{ __('Manager.') }}
            </x-dashboard.card>
        </div>
    </div>
</div>