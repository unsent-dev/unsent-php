# Unsent PHP SDK

Official PHP SDK for the [Unsent](https://unsent.dev) API - the best open-source platform for sending transactional emails.

## Prerequisites

- [Unsent API key](https://app.unsent.dev/dev-settings/api-keys)
- [Verified domain](https://app.unsent.dev/domains)
- PHP 7.4 or higher

## Installation

Install via Composer:

```bash
composer require unsent-dev/unsent
```

## Usage

### Basic Setup

```php
<?php

require 'vendor/autoload.php';

use UnsentDev\Unsent\Unsent;

$client = new Unsent('un_xxxx');
```

### Environment Variables

You can also set your API key using environment variables:

```php
<?php
// Set UNSENT_API_KEY in your environment
// Then initialize without passing the key
$client = new Unsent();
```

### Sending Emails

#### Simple Email

```php
<?php

[$data, $error] = $client->emails->send([
    'to' => 'hello@acme.com',
    'from' => 'hello@company.com',
    'subject' => 'Unsent email',
    'html' => '<p>Unsent is the best email service provider to send emails</p>',
    'text' => 'Unsent is the best email service provider to send emails',
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Email sent! ID: " . $data['emailId'] . "\n";
}
```

#### Email with Attachments

```php
<?php

[$data, $error] = $client->emails->send([
    'to' => 'hello@acme.com',
    'from' => 'hello@company.com',
    'subject' => 'Email with attachment',
    'html' => '<p>Please find the attachment below</p>',
    'attachments' => [
        [
            'filename' => 'document.pdf',
            'content' => 'base64-encoded-content-here',
        ]
    ],
]);
```

#### Scheduled Email

```php
<?php

use DateTime;

// Schedule email for 1 hour from now
$scheduledTime = new DateTime('+1 hour');

[$data, $error] = $client->emails->send([
    'to' => 'hello@acme.com',
    'from' => 'hello@company.com',
    'subject' => 'Scheduled email',
    'html' => '<p>This email was scheduled</p>',
    'scheduledAt' => $scheduledTime,
]);
```

#### Batch Emails

```php
<?php

$emails = [
    [
        'to' => 'user1@example.com',
        'from' => 'hello@company.com',
        'subject' => 'Hello User 1',
        'html' => '<p>Welcome User 1</p>',
    ],
    [
        'to' => 'user2@example.com',
        'from' => 'hello@company.com',
        'subject' => 'Hello User 2',
        'html' => '<p>Welcome User 2</p>',
    ],
];

[$data, $error] = $client->emails->batch($emails);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Sent " . count($data['data']) . " emails\n";
}
```

#### Idempotent Retries

To prevent duplicate emails when retrying failed requests, you can provide an idempotency key.

```php
<?php

[$data, $error] = $client->emails->send([
    'to' => 'hello@acme.com',
    'from' => 'hello@company.com',
    'subject' => 'Unsent email',
    'html' => '<p>Unsent is the best email service provider to send emails</p>',
], [
    'idempotencyKey' => 'unique-key-123'
]);
```

### Managing Emails

#### Get Email Details

```php
<?php

[$data, $error] = $client->emails->get('email_id');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Email status: " . $data['latestStatus'] . "\n";
}
```

#### Update Email

```php
<?php

[$data, $error] = $client->emails->update('email_id', [
    'scheduledAt' => new DateTime('+2 hours'),
]);
```

#### Cancel Scheduled Email

```php
<?php

[$data, $error] = $client->emails->cancel('email_id');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Email cancelled successfully\n";
}
```

#### List Emails

```php
<?php

[$data, $error] = $client->emails->list([
    'page' => 1,
    'limit' => 50,
    'startDate' => '2024-01-01T00:00:00Z',
    'endDate' => '2024-12-31T23:59:59Z',
    'domainId' => 'domain_123'
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data['data'] as $email) {
        echo "Email ID: " . $email['id'] . ", Status: " . $email['status'] . "\n";
    }
}
```

#### Get Email Events

```php
<?php

[$data, $error] = $client->emails->getEvents('email_id', [
    'page' => 1,
    'limit' => 50
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $event) {
        echo "Event: " . $event['type'] . " at " . $event['timestamp'] . "\n";
    }
}
```

### Managing Contacts

#### Create Contact

```php
<?php

[$data, $error] = $client->contacts->create('contact_book_id', [
    'email' => 'user@example.com',
    'firstName' => 'John',
    'lastName' => 'Doe',
    'properties' => [
        'company' => 'Acme Inc',
        'role' => 'Developer'
    ]
]);
```

#### Get Contact

```php
<?php

[$data, $error] = $client->contacts->get('contact_book_id', 'contact_id');
```

#### Update Contact

```php
<?php

[$data, $error] = $client->contacts->update('contact_book_id', 'contact_id', [
    'firstName' => 'Jane',
    'properties' => [
        'role' => 'Senior Developer'
    ]
]);
```

#### Upsert Contact

```php
<?php

// Creates if doesn't exist, updates if exists
[$data, $error] = $client->contacts->upsert('contact_book_id', 'contact_id', [
    'email' => 'user@example.com',
    'firstName' => 'John',
    'lastName' => 'Doe',
]);
```

#### Delete Contact

```php
<?php

[$data, $error] = $client->contacts->delete('contact_book_id', 'contact_id');
```

### Managing Campaigns

#### Create Campaign

```php
<?php

[$data, $error] = $client->campaigns->create([
    'name' => 'Welcome Series',
    'subject' => 'Welcome to our service!',
    'html' => '<p>Thanks for joining us!</p>',
    'from' => 'welcome@example.com',
    'contactBookId' => 'cb_1234567890',
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Campaign created! ID: " . $data['id'] . "\n";
}
```

#### Schedule Campaign

```php
<?php

[$data, $error] = $client->campaigns->schedule('campaign_id', [
    'scheduledAt' => '2024-12-01T10:00:00Z',
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Campaign scheduled successfully!\n";
}
```

#### Pause/Resume Campaigns

```php
<?php

// Pause a campaign
[$data, $error] = $client->campaigns->pause('campaign_123');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Campaign paused successfully!\n";
}

// Resume a campaign
[$data, $error] = $client->campaigns->resume('campaign_123');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Campaign resumed successfully!\n";
}
```

#### Get Campaign Details

```php
<?php

[$data, $error] = $client->campaigns->get('campaign_id');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Campaign status: " . $data['status'] . "\n";
    echo "Recipients: " . $data['total'] . "\n";
    echo "Sent: " . $data['sent'] . "\n";
}
```

### Managing Domains

#### List Domains

```php
<?php

[$data, $error] = $client->domains->list();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $domain) {
        echo "Domain: " . $domain['name'] . ", Status: " . $domain['status'] . "\n";
    }
}
```

#### Create Domain

```php
<?php

[$data, $error] = $client->domains->create([
    'name' => 'example.com',
    'region' => 'us-east-1',
]);
```

#### Verify Domain

```php
<?php

[$data, $error] = $client->domains->verify(123);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Verification initiated\n";
}
```

#### Get Domain

```php
<?php

[$data, $error] = $client->domains->get(123);
```

#### Delete Domain

```php
<?php

[$data, $error] = $client->domains->delete(123);
```

#### Get Domain Analytics

```php
<?php

[$data, $error] = $client->domains->getAnalytics('domain_id', [
    'days' => 30
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Domain analytics retrieved successfully\n";
}
```

#### Get Domain Stats

```php
<?php

[$data, $error] = $client->domains->getStats('domain_id');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Domain stats retrieved successfully\n";
}
```

### Managing Contact Books

Contact Books are containers for organizing your contacts.

#### List Contact Books

```php
<?php

[$data, $error] = $client->contactBooks->list();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $book) {
        echo "Book: " . $book['name'] . "\n";
    }
}
```

#### Create Contact Book

```php
<?php

[$data, $error] = $client->contactBooks->create([
    'name' => 'Newsletter Subscribers',
    'emoji' => '📧'
]);
```

#### Get Contact Book

```php
<?php

[$data, $error] = $client->contactBooks->get('book_id');
```

#### Update Contact Book

```php
<?php

[$data, $error] = $client->contactBooks->update('book_id', [
    'name' => 'Updated Name',
    'emoji' => '📬'
]);
```

#### Delete Contact Book

```php
<?php

[$data, $error] = $client->contactBooks->delete('book_id');
```

### Managing Templates

Templates allow you to create reusable email templates.

#### List Templates

```php
<?php

[$data, $error] = $client->templates->list();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $template) {
        echo "Template: " . $template['name'] . "\n";
    }
}
```

#### Create Template

```php
<?php

[$data, $error] = $client->templates->create([
    'name' => 'Welcome Email',
    'subject' => 'Welcome to {{company}}!',
    'html' => '<h1>Welcome, {{firstName}}!</h1><p>We're glad to have you.</p>',
    'text' => 'Welcome, {{firstName}}! We're glad to have you.'
]);
```

#### Get Template

```php
<?php

[$data, $error] = $client->templates->get('template_id');
```

#### Update Template

```php
<?php

[$data, $error] = $client->templates->update('template_id', [
    'subject' => 'Welcome to our platform!',
    'html' => '<h1>Updated content</h1>'
]);
```

#### Delete Template

```php
<?php

[$data, $error] = $client->templates->delete('template_id');
```

### Analytics

Get insights into your email performance.

#### Get Analytics Overview

```php
<?php

[$data, $error] = $client->analytics->get();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Sent: " . $data['sent'] . "\n";
    echo "Delivered: " . $data['delivered'] . "\n";
    echo "Bounced: " . $data['bounced'] . "\n";
}
```

#### Get Time Series Data

```php
<?php

[$data, $error] = $client->analytics->getTimeSeries(['days' => 30]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $point) {
        echo "Date: " . $point['date'] . ", Sent: " . $point['sent'] . "\n";
    }
}
```

#### Get Reputation Score

```php
<?php

[$data, $error] = $client->analytics->getReputation();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Reputation Score: " . $data['score'] . "\n";
    echo "Bounce Rate: " . $data['bounceRate'] . "%\n";
}
```

### Managing API Keys

Create and manage API keys for your application.

#### List API Keys

```php
<?php

[$data, $error] = $client->apiKeys->list();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $key) {
        echo "Key: " . $key['name'] . " (" . $key['permission'] . ")\n";
    }
}
```

#### Create API Key

```php
<?php

[$data, $error] = $client->apiKeys->create([
    'name' => 'Production Key',
    'permission' => 'SENDING'
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "New API Key: " . $data['token'] . "\n";
    // Important: Save this token securely, it won't be shown again
}
```

#### Delete API Key

```php
<?php

[$data, $error] = $client->apiKeys->delete('key_id');
```

### Settings

Get your account settings.

#### Get Settings

```php
<?php

[$data, $error] = $client->settings->get();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Account: " . $data['accountName'] . "\n";
    echo "Email Limit: " . $data['emailLimit'] . "\n";
}
```

### Managing Suppressions

Manage your suppression list (bounced, complained, or unsubscribed emails).

#### List Suppressions

```php
<?php

[$data, $error] = $client->suppressions->list([
    'page' => 1,
    'limit' => 50
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data['data'] as $suppression) {
        echo "Email: " . $suppression['email'] . ", Reason: " . $suppression['reason'] . "\n";
    }
}
```

#### Add Suppression

```php
<?php

[$data, $error] = $client->suppressions->add([
    'email' => 'user@example.com',
    'reason' => 'MANUAL'
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Email added to suppression list\n";
}
```

#### Delete Suppression

```php
<?php

[$data, $error] = $client->suppressions->delete('user@example.com');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Email removed from suppression list\n";
}
```

### Managing Webhooks

> **Note**: Webhooks are included for reference as a future implementation. The webhook functionality is not yet fully operational in the API.

#### List Webhooks

```php
<?php

[$data, $error] = $client->webhooks->list();
```

#### Create Webhook

```php
<?php

[$data, $error] = $client->webhooks->create([
    'url' => 'https://your-app.com/webhook',
    'events' => ['email.sent', 'email.delivered', 'email.bounced']
]);
```

#### Update Webhook

```php
<?php

[$data, $error] = $client->webhooks->update('webhook_id', [
    'url' => 'https://your-app.com/updated-webhook',
    'events' => ['email.sent', 'email.delivered']
]);
```

#### Delete Webhook

```php
<?php

[$data, $error] = $client->webhooks->delete('webhook_id');
```

### System Operations

Check API health and version information.

#### Health Check

```php
<?php

[$data, $error] = $client->system->health();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "API Status: " . $data['status'] . "\n";
    echo "Uptime: " . $data['uptime'] . " seconds\n";
}
```

#### Get Version Information

```php
<?php

[$data, $error] = $client->system->version();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "API Version: " . $data['version'] . "\n";
    echo "Environment: " . $data['environment'] . "\n";
    echo "Node Version: " . $data['nodeVersion'] . "\n";
}
```

### Activity Feed

Retrieve activity feed with email events and details.

#### Get Activity

```php
<?php

[$data, $error] = $client->activity->get([
    'page' => 1,
    'limit' => 50
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Activity feed retrieved successfully\n";
}
```

### Events

Manage and retrieve email events.

#### List All Events

```php
<?php

[$data, $error] = $client->events->list([
    'page' => 1,
    'limit' => 50,
    'status' => 'DELIVERED',
    'startDate' => '2024-01-01T00:00:00Z'
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Events retrieved successfully\n";
}
```

### Teams

Manage team information and members.

#### List Teams

```php
<?php

[$data, $error] = $client->teams->list();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    foreach ($data as $team) {
        echo "Team: " . $team['name'] . "\n";
    }
}
```

#### Get Team Details

```php
<?php

[$data, $error] = $client->teams->get('team_id');

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Team Name: " . $data['name'] . "\n";
    echo "Plan: " . $data['plan'] . "\n";
}
```

### Metrics

Retrieve metrics data for your account.

#### Get Metrics

```php
<?php

[$data, $error] = $client->metrics->get();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Metrics retrieved successfully\n";
}
```

### Stats

Retrieve statistical data.

#### Get Stats

```php
<?php

[$data, $error] = $client->stats->get();

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Stats retrieved successfully\n";
}
```

### Error Handling

By default, the SDK raises exceptions on HTTP errors:

```php
<?php

use UnsentDev\Unsent\Unsent;
use UnsentDev\Unsent\UnsentHTTPError;

$client = new Unsent('un_xxxx');

try {
    [$data, $error] = $client->emails->send([
        'to' => 'invalid-email',
        'from' => 'hello@company.com',
        'subject' => 'Test',
        'html' => '<p>Test</p>',
    ]);
} catch (UnsentHTTPError $e) {
    echo "HTTP " . $e->statusCode . ": " . $e->error['message'] . "\n";
}
```

To disable automatic error raising:

```php
<?php

$client = new Unsent('un_xxxx', null, false);

[$data, $error] = $client->emails->send([
    'to' => 'hello@acme.com',
    'from' => 'hello@company.com',
    'subject' => 'Test',
    'html' => '<p>Test</p>',
]);

if ($error) {
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "Success!\n";
}
```

### Custom HTTP Client

For advanced use cases, you can provide your own Guzzle client instance:

```php
<?php

use GuzzleHttp\Client;
use UnsentDev\Unsent\Unsent;

$httpClient = new Client([
    'timeout' => 30,
    'verify' => false, // Not recommended for production!
]);

$client = new Unsent('un_xxxx', null, true, $httpClient);
```

## API Reference

### Client Methods

- `new Unsent($key, $url = null, $raiseOnError = true, $client = null)` - Initialize the client

### Email Methods

- `$client->emails->send($payload, $options = [])` - Send an email (alias for `create`)
- `$client->emails->create($payload, $options = [])` - Create and send an email
- `$client->emails->batch($emails, $options = [])` - Send multiple emails in batch
- `$client->emails->list($options = [])` - List emails with optional filters
- `$client->emails->get($emailId)` - Get email details
- `$client->emails->update($emailId, $payload)` - Update a scheduled email
- `$client->emails->cancel($emailId)` - Cancel a scheduled email
- `$client->emails->getComplaints($options = [])` - Get email complaints
- `$client->emails->getBounces($options = [])` - Get email bounces
- `$client->emails->getUnsubscribes($options = [])` - Get email unsubscribes
- `$client->emails->getEvents($emailId, $options = [])` - Get events for a specific email

### Contact Book Methods

- `$client->contactBooks->list()` -List all contact books
- `$client->contactBooks->create($payload)` - Create a contact book
- `$client->contactBooks->get($bookId)` - Get contact book details
- `$client->contactBooks->update($bookId, $payload)` - Update a contact book
- `$client->contactBooks->delete($bookId)` - Delete a contact book

### Contact Methods

- `$client->contacts->list($bookId, $options = [])` - List contacts in a book
- `$client->contacts->create($bookId, $payload)` - Create a contact
- `$client->contacts->get($bookId, $contactId)` - Get contact details
- `$client->contacts->update($bookId, $contactId, $payload)` - Update a contact
- `$client->contacts->upsert($bookId, $contactId, $payload)` - Upsert a contact
- `$client->contacts->delete($bookId, $contactId)` - Delete a contact

### Template Methods

- `$client->templates->list()` - List all templates
- `$client->templates->create($payload)` - Create a template
- `$client->templates->get($templateId)` - Get template details
- `$client->templates->update($templateId, $payload)` - Update a template
- `$client->templates->delete($templateId)` - Delete a template

### Campaign Methods

- `$client->campaigns->list()` - List all campaigns
- `$client->campaigns->create($payload)` - Create a campaign
- `$client->campaigns->get($campaignId)` - Get campaign details
- `$client->campaigns->schedule($campaignId, $payload)` - Schedule a campaign
- `$client->campaigns->pause($campaignId)` - Pause a campaign
- `$client->campaigns->resume($campaignId)` - Resume a campaign

### Domain Methods

- `$client->domains->list()` - List all domains
- `$client->domains->create($payload)` - Create a domain
- `$client->domains->verify($domainId)` - Verify a domain
- `$client->domains->get($domainId)` - Get domain details
- `$client->domains->delete($domainId)` - Delete a domain
- `$client->domains->getAnalytics($domainId, $options = [])` - Get domain analytics
- `$client->domains->getStats($domainId, $options = [])` - Get domain stats

### Analytics Methods

- `$client->analytics->get()` - Get analytics overview
- `$client->analytics->getTimeSeries($options = [])` - Get time series analytics
- `$client->analytics->getReputation()` - Get reputation score

### API Key Methods

- `$client->apiKeys->list()` - List all API keys
- `$client->apiKeys->create($payload)` - Create an API key
- `$client->apiKeys->delete($keyId)` - Delete an API key

### Settings Methods

- `$client->settings->get()` - Get account settings

### Suppression Methods

- `$client->suppressions->list($options = [])` - List suppressions
- `$client->suppressions->add($payload)` - Add an email to suppression list
- `$client->suppressions->delete($email)` - Remove an email from suppression list

### Webhook Methods

> **Note**: Webhooks are a future feature and not yet fully operational.

- `$client->webhooks->list()` - List all webhooks
- `$client->webhooks->create($payload)` - Create a webhook
- `$client->webhooks->update($webhookId, $payload)` - Update a webhook
- `$client->webhooks->delete($webhookId)` - Delete a webhook

### System Methods

- `$client->system->health()` - Check API health status
- `$client->system->version()` - Get API version information

### Activity Methods

- `$client->activity->get($options = [])` - Get activity feed

### Events Methods

- `$client->events->list($options = [])` - List all email events

### Teams Methods

- `$client->teams->list()` - List all teams
- `$client->teams->get($teamId)` - Get team details

### Metrics Methods

- `$client->metrics->get($options = [])` - Get metrics data

### Stats Methods

- `$client->stats->get($options = [])` - Get statistics

## Requirements

- PHP 7.4 or higher
- Guzzle HTTP client ^7.0

## License

MIT

## Support

- [Documentation](https://docs.unsent.dev)
- [GitHub Issues](https://github.com/unsent-dev/unsent-php/issues)
- [Discord Community](https://discord.gg/unsent)
