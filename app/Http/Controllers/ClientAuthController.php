<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\MagicLinkService;
use Illuminate\Http\Request;

class ClientAuthController extends Controller
{
    protected MagicLinkService $magicLinkService;

    public function __construct(MagicLinkService $magicLinkService)
    {
        $this->magicLinkService = $magicLinkService;
    }

    /**
     * Show the client login form.
     */
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    /**
     * Send magic link to client.
     */
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'method' => 'required|in:email,whatsapp',
        ]);

        $identifier = $request->identifier;
        $method = $request->method;

        // Find client by email or WhatsApp number
        $client = $method === 'email'
            ? Client::where('email', $identifier)->where('is_active', true)->first()
            : Client::where('whatsapp_number', $identifier)->where('is_active', true)->first();

        if (!$client) {
            return back()->with('error', 'No active client found with the provided information.');
        }

        try {
            if ($method === 'email') {
                $this->magicLinkService->sendMagicLinkViaEmail($client);
                $message = 'Magic link sent to your email address. Please check your inbox.';
            } else {
                $this->magicLinkService->sendMagicLinkViaWhatsApp($client);
                $message = 'Magic link sent to your WhatsApp. Please check your messages.';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send magic link. Please try again.');
        }
    }

    /**
     * Authenticate client via magic link.
     */
    public function authenticate(Request $request, string $token)
    {
        $accessToken = $this->magicLinkService->verifyToken($token);

        if (!$accessToken) {
            return redirect()->route('client.login')
                           ->with('error', 'Invalid or expired link. Please request a new one.');
        }

        $client = $this->magicLinkService->authenticateClient($accessToken);

        return redirect()->route('client.dashboard')
                       ->with('success', "Welcome back, {$client->name}!");
    }

    /**
     * Logout client.
     */
    public function logout()
    {
        session()->forget(['client_authenticated', 'client_id', 'tenant_id']);
        
        return redirect()->route('client.login')
                       ->with('success', 'You have been logged out successfully.');
    }
}
