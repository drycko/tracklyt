<?php

namespace App\Services;

use App\Models\TwilioUsage;
use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;
    protected string $fromNumber;

    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $this->fromNumber = config('services.twilio.whatsapp_from');

        if (!$accountSid || !$authToken || !$this->fromNumber) {
            throw new \Exception('Twilio credentials are not configured.');
        }

        $this->client = new Client($accountSid, $authToken);
    }

    /**
     * Send a WhatsApp message via Twilio with usage tracking.
     *
     * @param string $to The recipient's phone number (format: +1234567890)
     * @param string $message The message content
     * @param int|null $tenantId The tenant ID for usage tracking
     * @param float $cost Optional cost per message for tracking
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    public function sendWhatsAppMessage(string $to, string $message, ?int $tenantId = null, float $cost = 0)
    {
        // Check usage limit if tenant provided
        if ($tenantId) {
            $planLimitService = app(PlanLimitService::class);
            $tenant = \App\Models\Tenant::find($tenantId);
            
            if ($tenant && !$planLimitService->canSendTwilioMessage($tenant)) {
                throw new \Exception('WhatsApp message limit reached for this billing period. Please upgrade your plan.');
            }
        }

        // Ensure numbers are in the correct WhatsApp format
        $from = $this->formatWhatsAppNumber($this->fromNumber);
        $to = $this->formatWhatsAppNumber($to);

        $result = $this->client->messages->create(
            $to,
            [
                'from' => $from,
                'body' => $message,
            ]
        );

        // Track usage if tenant provided
        if ($tenantId) {
            $this->trackWhatsAppUsage($tenantId, $cost);
        }

        return $result;
    }

    /**
     * Send SMS with usage tracking.
     *
     * @param string $to The recipient's phone number
     * @param string $message The message content
     * @param int|null $tenantId The tenant ID for usage tracking
     * @param float $cost Optional cost per message for tracking
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    public function sendSMS(string $to, string $message, ?int $tenantId = null, float $cost = 0)
    {
        // Check usage limit if tenant provided
        if ($tenantId) {
            $planLimitService = app(PlanLimitService::class);
            $tenant = \App\Models\Tenant::find($tenantId);
            
            if ($tenant && !$planLimitService->canSendTwilioMessage($tenant)) {
                throw new \Exception('SMS limit reached for this billing period. Please upgrade your plan.');
            }
        }

        $from = str_replace('whatsapp:', '', $this->fromNumber);
        
        $result = $this->client->messages->create(
            $to,
            [
                'from' => $from,
                'body' => $message,
            ]
        );

        // Track usage if tenant provided
        if ($tenantId) {
            $this->trackSMSUsage($tenantId, $cost);
        }

        return $result;
    }

    /**
     * Track WhatsApp usage for tenant.
     */
    protected function trackWhatsAppUsage(int $tenantId, float $cost = 0): void
    {
        $usage = TwilioUsage::getOrCreateForCurrentMonth($tenantId);
        $usage->incrementWhatsApp($cost);
    }

    /**
     * Track SMS usage for tenant.
     */
    protected function trackSMSUsage(int $tenantId, float $cost = 0): void
    {
        $usage = TwilioUsage::getOrCreateForCurrentMonth($tenantId);
        $usage->incrementSMS($cost);
    }
}
