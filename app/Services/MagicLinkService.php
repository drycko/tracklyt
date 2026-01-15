<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientAccessToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MagicLinkService
{
    /**
     * Generate a magic link for a client via email.
     */
    public function sendMagicLinkViaEmail(Client $client): ClientAccessToken
    {
        $token = $this->createToken($client, 'email');
        $magicLink = $this->generateMagicLink($token->token);

        Mail::send('emails.client-magic-link', [
            'client' => $client,
            'magicLink' => $magicLink,
        ], function ($message) use ($client) {
            $message->to($client->email)
                    ->subject('Access Your Tracklyt Portal');
        });

        return $token;
    }

    /**
     * Generate a magic link for a client via WhatsApp.
     */
    public function sendMagicLinkViaWhatsApp(Client $client): ClientAccessToken
    {
        if (!$client->whatsapp_number) {
            throw new \Exception('Client does not have a WhatsApp number.');
        }

        $token = $this->createToken($client, 'whatsapp');
        $magicLink = $this->generateMagicLink($token->token);

        $twilioService = app(TwilioService::class);
        $twilioService->sendWhatsAppMessage(
            $client->whatsapp_number,
            "Hello {$client->name},\n\nClick here to access your Tracklyt portal:\n{$magicLink}\n\nThis link expires in 24 hours.",
            $client->tenant_id // Pass tenant_id for usage tracking
        );

        return $token;
    }

    /**
     * Create a new access token for the client.
     */
    protected function createToken(Client $client, string $createdVia): ClientAccessToken
    {
        // Invalidate any existing valid tokens
        $client->accessTokens()
              ->valid()
              ->update(['used_at' => now()]);

        return ClientAccessToken::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'token' => ClientAccessToken::generateToken(),
            'created_via' => $createdVia,
            'expires_at' => now()->addHours(24),
        ]);
    }

    /**
     * Generate the magic link URL.
     */
    protected function generateMagicLink(string $token): string
    {
        return URL::route('client.auth', ['token' => $token]);
    }

    /**
     * Verify a magic link token.
     */
    public function verifyToken(string $token): ?ClientAccessToken
    {
        $accessToken = ClientAccessToken::where('token', $token)
                                       ->valid()
                                       ->first();

        if (!$accessToken) {
            return null;
        }

        return $accessToken;
    }

    /**
     * Authenticate a client using a valid token.
     */
    public function authenticateClient(ClientAccessToken $accessToken): Client
    {
        $accessToken->markAsUsed();
        
        $client = $accessToken->client;
        
        // Store client info in session
        session([
            'client_authenticated' => true,
            'client_id' => $client->id,
            'tenant_id' => $client->tenant_id,
        ]);

        return $client;
    }
}
