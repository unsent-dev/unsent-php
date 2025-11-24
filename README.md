# Unsent PHP SDK

Official PHP SDK for the [Unsent](https://unsent.dev) API - the best open-source platform for sending transactional emails.

## Prerequisites

- [Unsent API key](https://app.unsent.dev/dev-settings/api-keys)
- [Verified domain](https://app.unsent.dev/domains)
- PHP 7.4 or higher

## Installation

Install via Composer:

```bash
composer require souravsspace/unsent
```

## Usage

### Basic Setup

```php
<?php

require 'vendor/autoload.php';

use Souravsspace\Unsent\Unsent;

$client = new Unsent('us_12345');
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

### Error Handling

By default, the SDK raises exceptions on HTTP errors:

```php
<?php

use Souravsspace\Unsent\Unsent;
use Souravsspace\Unsent\UnsentHTTPError;

$client = new Unsent('us_12345');

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

$client = new Unsent('us_12345', null, false);

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
use Souravsspace\Unsent\Unsent;

$httpClient = new Client([
    'timeout' => 30,
    'verify' => false, // Not recommended for production!
]);

$client = new Unsent('us_12345', null, true, $httpClient);
```

## API Reference

### Client Methods

- `new Unsent($key, $url = null, $raiseOnError = true, $client = null)` - Initialize the client

### Email Methods

- `$client->emails->send($payload)` - Send an email (alias for `create`)
- `$client->emails->create($payload)` - Create and send an email
- `$client->emails->batch($emails)` - Send multiple emails in batch
- `$client->emails->get($emailId)` - Get email details
- `$client->emails->update($emailId, $payload)` - Update a scheduled email
- `$client->emails->cancel($emailId)` - Cancel a scheduled email

### Contact Methods

- `$client->contacts->create($bookId, $payload)` - Create a contact
- `$client->contacts->get($bookId, $contactId)` - Get contact details
- `$client->contacts->update($bookId, $contactId, $payload)` - Update a contact
- `$client->contacts->upsert($bookId, $contactId, $payload)` - Upsert a contact
- `$client->contacts->delete($bookId, $contactId)` - Delete a contact

### Campaign Methods

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

## Requirements

- PHP 7.4 or higher
- Guzzle HTTP client ^7.0

## License

MIT

## Support

- [Documentation](https://docs.unsent.dev)
- [GitHub Issues](https://github.com/souravsspace/unsent-php/issues)
- [Discord Community](https://discord.gg/unsent)
