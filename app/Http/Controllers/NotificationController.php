<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware('can:list-notifications')->only('index');
        $this->middleware('can:create-notification')->only('create', 'store');
        $this->middleware('can:edit-notification,notification')->only('edit', 'update');
        $this->middleware('can:delete-notification,notification')->only('destroy');
        $this->middleware('can:publish-notification')->only('publish');
    }

    public function index()
    {
        $notifications = Notification::with('sender')
            ->when(Auth::user()->hasRole('editor'), function($query) {
                return $query->where('sender_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function create()
    {
        // presenta valores de lang
        $recipientTypes = [
            'all' => __('site.All_users'),
            'role' =>  __('site.Users_role'),
            'specific' =>  __('site.Specific_users')
        ];
    
        // Obtener roles como array [id => nombre]
       $roles = \Spatie\Permission\Models\Role::all()->pluck('name', 'id');
        
        // Obtener todos los usuarios activos
       // $users = User::whereHas('roles')->get();
         // Obtener usuarios que NO tienen el rol 'invited'
        $users = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'invited');
        })->get();

    
        return view('notifications.create', [
            'recipientTypes' => $recipientTypes,
            'roles' => $roles,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,role,specific',
            'recipient_role' => 'nullable|required_if:recipient_type,role|exists:roles,name',
            'recipient_ids' => 'nullable|required_if:recipient_type,specific|array',
            'recipient_ids.*' => 'exists:users,id',
        ]);
    
        // Crear notificación
        $notification = Notification::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'sender_id' => Auth::id(),
            'recipient_type' => $validated['recipient_type'],
            'recipient_role' => $validated['recipient_type'] === 'role' ? $validated['recipient_role'] : null,
            'recipient_ids' => $validated['recipient_type'] === 'specific' ? $validated['recipient_ids'] : null,
            'is_published' => Auth::user()->can('notifications.publish'),
            'published_at' => Auth::user()->can('notifications.publish') ? now() : null,
        ]);
    
        // Asignar destinatarios
        $this->assignRecipients($notification);
    
        return redirect()->route('notifications.index')
            ->with('success', __('site.Notification_created'));
    }

    public function show(Notification $notification)
    {
        $this->authorize('view-notification', $notification);
        
        // Marcar como leída si es destinatario
        if (auth()->check() && $notification->recipients->contains(auth()->id())) {
            DB::table('notification_user')
                ->where('notification_id', $notification->id)
                ->where('user_id', auth()->id())
                ->update(['read' => true, 'read_at' => now()]);
        }

        return view('notifications.show', compact('notification'));
    }

    
    public function edit(Notification $notification)
    {
       
        $this->authorize('edit-notification', $notification);
        
        $recipientTypes = [
            'all' => 'Todos los usuarios',
            'role' => 'Por rol', 
            'specific' => 'Usuarios específicos'
        ];
        
        return view('notifications.edit', [
            'notification' => $notification,
            'recipientTypes' => $recipientTypes,
            'roles' => Role::all()->pluck('name', 'name'),
            'users' => User::whereDoesntHave('roles', fn($q) => $q->where('name', 'invited'))->get()
        ]);
    }
    
    public function update(Request $request, Notification $notification)
    {
        $this->authorize('update-notification', $notification);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,role,specific',
            'recipient_role' => 'nullable|required_if:recipient_type,role|exists:roles,name',
            'recipient_ids' => 'nullable|required_if:recipient_type,specific|array',
            'recipient_ids.*' => 'exists:users,id',
        ]);

        $notification->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'recipient_type' => $validated['recipient_type'],
            'recipient_role' => $validated['recipient_type'] === 'role' ? $validated['recipient_role'] : null,
            'recipient_ids' => $validated['recipient_type'] === 'specific' ? $validated['recipient_ids'] : null,
        ]);

        // Si cambian los destinatarios, actualizamos
        $notification->recipients()->detach();
        $this->assignRecipients($notification);

        return redirect()->route('notifications.index')
            ->with('success', __('site.Notification_updated'));
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete-notification', $notification);
        
        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', __('site.Notification_deleted'));
    }

    public function publish(Notification $notification)
    {
        $this->authorize('publish', $notification);

        $notification->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->sendNotifications($notification);

        return redirect()->route('notifications.index')
            ->with('success', __('site.Notification_published'));
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read' => true, 'read_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    protected function assignRecipients(Notification $notification)
    {
        $users = collect();

        switch ($notification->recipient_type) {
            case 'all':
                // Todos los usuarios excepto invitados (usando relación de roles)
                $users = User::whereDoesntHave('roles', function($query) {
                    $query->where('name', 'invited');
                })->get();
                break;
                
            case 'role':
                // Usuarios con el rol específico
                $users = User::role($notification->recipient_role)->get();
                break;
                
            case 'specific':
                // Usuarios específicos seleccionados
                $users = User::whereIn('id', $notification->recipient_ids)->get();
                break;
        }

        $notification->recipients()->sync($users->pluck('id'));
    }

    protected function sendNotifications(Notification $notification)
    {
        // Envío web (ya está asignado en la tabla notification_user)
        $notification->update(['web_sent' => true]);

        // Envío por email (implementación futura)
        // $this->sendEmailNotifications($notification);
        
        // Envío push (implementación futura)
        // $this->sendPushNotifications($notification);
    }
}