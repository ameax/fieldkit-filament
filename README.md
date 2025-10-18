# FieldKit Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ameax/fieldkit-filament.svg?style=flat-square)](https://packagist.org/packages/ameax/fieldkit-filament)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ameax/fieldkit-filament/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ameax/fieldkit-filament/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ameax/fieldkit-filament/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ameax/fieldkit-filament/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ameax/fieldkit-filament.svg?style=flat-square)](https://packagist.org/packages/ameax/fieldkit-filament)

Filament admin panel integration for FieldKit - provides GUI for managing dynamic form fields and renders Filament form components.

## Features

- **Admin GUI** - Manage form fields through Filament interface
- **Form Adapter** - Converts FieldKit definitions to Filament components
- **Resource Management** - CRUD interface for forms, fields, options, and mappings
- **Visual Field Builder** - Create and edit fields with live preview
- **Mapping Configuration** - Configure external system mappings through GUI
- **Auto-Component Generation** - Automatically renders appropriate Filament components

## Requirements

- PHP 8.3+
- Laravel 11.0 or 12.0
- Filament 3.x
- [ameax/fieldkit-core](https://github.com/ameax/fieldkit-core)

## Installation

```bash
composer require ameax/fieldkit-core
composer require ameax/fieldkit-filament
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="fieldkit-core-migrations"
php artisan vendor:publish --tag="fieldkit-filament-migrations"
php artisan migrate
```

Publish config (optional):

```bash
php artisan vendor:publish --tag="fieldkit-core-config"
php artisan vendor:publish --tag="fieldkit-filament-config"
```

## Basic Usage

### 1. Access Admin Panel

Navigate to `/admin/fieldkit-forms` to manage your dynamic fields.

### 2. Create a Form

1. Click "New Form"
2. Set purpose token (e.g., `customer_registration`)
3. Add fields (text, email, select, etc.)
4. Configure mappings for external systems
5. Save

### 3. Use in Livewire Components

```php
use Ameax\FieldkitCore\Services\FieldKitService;
use Ameax\FieldkitFilament\Adapters\FieldKitFilamentAdapter;

class AccountRegistration extends Component
{
    public array $data = [];

    protected function customerSchema(): array
    {
        $service = app(FieldKitService::class);
        $adapter = app(FieldKitFilamentAdapter::class);

        return array_merge(
            $this->baseFields(),
            $service->renderFormComponents(
                purposeToken: 'customer_registration',
                adapter: $adapter
            )
        );
    }
}
```

### 4. Render in Blade

```blade
<form wire:submit="register">
    {{ $this->form }}

    <button type="submit">Register</button>
</form>
```

## Supported Field Types

- Text Input
- Email Input
- Number Input
- Textarea
- Checkbox
- Select (with options)
- Radio (with descriptions)

## External System Mappings

Configure mappings through the admin panel:

1. Edit a field
2. Go to "Mappings" tab
3. Add mapping:
   - Adapter: `ameax_column` (or custom)
   - Target Table: `customer`
   - Target Column: `xcu_newsletter`
   - Transformer: `boolean`
4. Save

The handler system (configured in `config/fieldkit-forms.php`) will automatically process these mappings.

## Documentation

For full documentation, see:

- [FieldKit Core Documentation](https://github.com/ameax/fieldkit-core/tree/main/docs)
- [Filament Integration Guide](https://github.com/ameax/fieldkit-core/blob/main/docs/filament/installation.md)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Schmidt](https://github.com/ms-aranes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
