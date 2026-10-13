<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\AdminAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('frontend.contact', ['customer' => auth('customer')->user()]);
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $message = ContactMessage::create($request->safe()->except('website') + [
            'customer_id' => auth('customer')->id(),
            'ip_address' => $request->ip(),
        ]);

        rescue(fn () => Notification::send(User::withPermission('support'), new AdminAlertNotification(
            "New message from {$message->name}",
            route('admin.messages.show', $message),
            'bi-envelope',
            $message->subject,
        )));

        return back()->with('success', 'Thank you! Your message has been sent. We will get back to you soon.');
    }
}
