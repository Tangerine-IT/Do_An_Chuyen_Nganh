<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use Illuminate\Http\RedirectResponse;

class NewsletterSubscriptionController extends Controller
{
    /**
     * Validates the address and confirms with a toast. Subscriptions are not stored yet:
     * there is no subscriber table or mailing list integration in the schema.
     */
    public function __invoke(SubscribeNewsletterRequest $request): RedirectResponse
    {
        return redirect(route('home').'#newsletter')->with('success', __('ui.newsletter.success'));
    }
}
