<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user('admin')->notifications()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark one notification as read and open what it points to.
     */
    public function read(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user('admin')->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('admin.dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user('admin')->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
