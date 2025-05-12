
 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">{{ __('site.welcome invited', ['name' => Auth::user()->name]) }}</h3>

        
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    
        {{ __('site.restricted functions') }}
        </div>
    </div>
</div>