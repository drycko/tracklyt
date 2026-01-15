# Client Portal - Setup & Usage Guide

## Overview
The Client Portal provides secure, passwordless access for clients to view their quotes, projects, and invoices using magic link authentication via email or WhatsApp.

## Features
- 🔐 **Magic Link Authentication** - Passwordless login via email or WhatsApp
- 📊 **Dashboard** - Overview of quotes, projects, and invoices
- 📄 **Quotes** - View and download quote PDFs
- 💼 **Projects** - Access project details, repositories, links, and mobile apps
- 🧾 **Invoices** - View and download invoice PDFs
- ⏱️ **Secure Tokens** - Time-limited (24h), one-time use tokens

## Installation

### 1. Run Migrations
```bash
php artisan migrate
```

This will create the `client_access_tokens` table and add `whatsapp_number` to the `clients` table.

### 2. Install Twilio SDK (for WhatsApp)
```bash
composer require twilio/sdk
```

### 3. Configure Environment Variables

Add the following to your `.env` file:

```env
# Twilio Configuration (for WhatsApp magic links)
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

**To get Twilio credentials:**
1. Sign up at https://www.twilio.com/
2. Get your Account SID and Auth Token from the dashboard
3. Enable WhatsApp messaging in Twilio Console
4. Get your WhatsApp-enabled phone number

### 4. Configure Mail Settings

Make sure your email is configured in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@tracklyt.com"
MAIL_FROM_NAME="Tracklyt"
```

## Usage

### Client Login Flow

1. **Client visits**: `/client/login`
2. **Client selects method**: Email or WhatsApp
3. **Client enters**: Email address or WhatsApp number
4. **System sends**: Magic link via selected method
5. **Client clicks**: Magic link in email/WhatsApp
6. **System authenticates**: Client and redirects to dashboard

### Routes

#### Public Routes
- `GET /client/login` - Login form
- `POST /client/send-magic-link` - Send magic link
- `GET /client/auth/{token}` - Authenticate via magic link

#### Protected Routes (require client.auth middleware)
- `GET /client/dashboard` - Client dashboard
- `GET /client/quotes` - List quotes
- `GET /client/quotes/{quote}` - View quote
- `GET /client/quotes/{quote}/pdf` - Download quote PDF
- `GET /client/projects` - List projects
- `GET /client/projects/{project}` - View project
- `GET /client/invoices` - List invoices
- `GET /client/invoices/{invoice}` - View invoice
- `GET /client/invoices/{invoice}/pdf` - Download invoice PDF
- `GET /client/logout` - Logout

### Security

#### Token Expiration
- Tokens expire after **24 hours**
- Tokens are **single-use** (marked as used after authentication)
- Expired or used tokens cannot be reused

#### Client Isolation
- Clients can only access their own data
- Authorization checks in `ClientPortalController`
- Session-based authentication

#### Session Management
Session data:
```php
[
    'client_authenticated' => true,
    'client_id' => 1,
    'tenant_id' => 1,
]
```

## Code Structure

### Models
- `ClientAccessToken` - Token management and validation
- `Client` - Extended with `accessTokens()` relationship and `whatsapp_number` field

### Services
- `MagicLinkService` - Generate and verify magic links, send via email/WhatsApp
- `TwilioService` - WhatsApp message delivery via Twilio API

### Controllers
- `ClientAuthController` - Login, magic link sending, authentication, logout
- `ClientPortalController` - Dashboard, quotes, projects, invoices

### Middleware
- `ClientAuthenticated` - Protect client portal routes

### Views
- `client/auth/login.blade.php` - Login form with email/WhatsApp toggle
- `client/dashboard.blade.php` - Client dashboard
- `client/quotes/index.blade.php` - Quotes list
- `client/quotes/show.blade.php` - Quote details
- `client/projects/index.blade.php` - Projects list
- `client/projects/show.blade.php` - Project details
- `client/invoices/index.blade.php` - Invoices list
- `client/invoices/show.blade.php` - Invoice details
- `layouts/client.blade.php` - Client portal layout
- `emails/client-magic-link.blade.php` - Magic link email template

## Adding WhatsApp Number to Client

Update a client's WhatsApp number:

```php
$client = Client::find(1);
$client->whatsapp_number = '+1234567890'; // Include country code
$client->save();
```

Or via the Clients CRUD interface (if you add the field to forms).

## Testing

### Test Email Magic Link
```php
php artisan tinker

$client = Client::first();
$service = app(\App\Services\MagicLinkService::class);
$token = $service->sendMagicLinkViaEmail($client);
```

### Test WhatsApp Magic Link
```php
php artisan tinker

$client = Client::first();
$client->whatsapp_number = '+1234567890';
$client->save();

$service = app(\App\Services\MagicLinkService::class);
$token = $service->sendMagicLinkViaWhatsApp($client);
```

## Customization

### Change Token Expiry
Edit `MagicLinkService::createToken()`:

```php
'expires_at' => now()->addHours(24), // Change to desired duration
```

### Customize Email Template
Edit `resources/views/emails/client-magic-link.blade.php`

### Customize WhatsApp Message
Edit `MagicLinkService::sendMagicLinkViaWhatsApp()`:

```php
$twilioService->sendWhatsAppMessage(
    $client->whatsapp_number,
    "Your custom message here: {$magicLink}"
);
```

## Troubleshooting

### Tokens not expiring
- Check server time is correct
- Verify `expires_at` timestamp is being set properly

### WhatsApp not sending
- Verify Twilio credentials in `.env`
- Check WhatsApp number format: `whatsapp:+1234567890`
- Ensure WhatsApp Sandbox is approved (for testing)
- Check Twilio logs in dashboard

### Email not sending
- Verify mail configuration in `.env`
- Check mail logs
- Test with Mailtrap for development

### Client can't access data
- Verify client is active: `$client->is_active = true`
- Check client_id in session matches client
- Verify tenant_id isolation

## Production Checklist

- [ ] Configure production mail driver (not `log`)
- [ ] Set up Twilio production credentials
- [ ] Enable HTTPS for secure token transmission
- [ ] Set `APP_ENV=production`
- [ ] Configure proper session driver (redis/database, not file)
- [ ] Set up monitoring for failed authentications
- [ ] Add rate limiting to magic link requests
- [ ] Configure proper error pages
- [ ] Test token expiration cleanup job (optional)

## Future Enhancements

- [ ] SMS fallback for WhatsApp failures
- [ ] Client profile management
- [ ] Notification preferences
- [ ] Multi-language support
- [ ] Mobile app integration
- [ ] Two-factor authentication option
- [ ] Cleanup expired tokens (scheduled job)

---

**Need Help?** Contact the development team or refer to the main Tracklyt documentation.
