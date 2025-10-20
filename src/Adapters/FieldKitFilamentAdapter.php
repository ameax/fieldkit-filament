<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Adapters;

use Ameax\FieldkitCore\Contracts\FieldKitAdapterInterface;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FieldKitFilamentAdapter implements FieldKitAdapterInterface
{
    public function getName(): string
    {
        return 'filament';
    }

    public function supports(string $type): bool
    {
        return in_array($type, [
            'text',
            'email', 
            'number',
            'textarea',
            'checkbox',
            'select',
            'radio',
        ]);
    }

    public function createComponent(string $type, array $config): mixed
    {
        $name = $config['name'] ?? 'field';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $placeholder = $config['placeholder'] ?? null;
        $description = $config['description'] ?? null;

        $component = match($type) {
            'text' => $this->createTextInput($name, $config),
            'email' => $this->createEmailInput($name, $config),
            'number' => $this->createNumberInput($name, $config),
            'textarea' => $this->createTextareaInput($name, $config),
            'checkbox' => $this->createCheckboxInput($name, $config),
            'select' => $this->createSelectInput($name, $config),
            'radio' => $this->createRadioInput($name, $config),
            default => null,
        };

        if ($component) {
            $component
                ->label($label)
                ->required($required);

            if ($placeholder) {
                $component->placeholder($placeholder);
            }

            if ($description) {
                $component->helperText($description);
            }
        }

        return $component;
    }

    protected function createTextInput(string $name, array $config): TextInput
    {
        $component = TextInput::make($name);

        if (isset($config['max_length'])) {
            $component->maxLength($config['max_length']);
        }

        return $component;
    }

    protected function createEmailInput(string $name, array $config): TextInput
    {
        return TextInput::make($name)
            ->email()
            ->autocomplete('email');
    }

    protected function createNumberInput(string $name, array $config): TextInput
    {
        $component = TextInput::make($name)
            ->numeric();

        if (isset($config['min'])) {
            $component->minValue($config['min']);
        }

        if (isset($config['max'])) {
            $component->maxValue($config['max']);
        }

        if (isset($config['step'])) {
            $component->step($config['step']);
        }

        return $component;
    }

    protected function createTextareaInput(string $name, array $config): Textarea
    {
        $component = Textarea::make($name);

        if (isset($config['rows'])) {
            $component->rows($config['rows']);
        }

        if (isset($config['max_length'])) {
            $component->maxLength($config['max_length']);
        }

        return $component;
    }

    protected function createCheckboxInput(string $name, array $config): Checkbox
    {
        $component = Checkbox::make($name);

        // Support inline layout
        if ($config['inline'] ?? false) {
            $component->inline();
        }

        return $component;
    }

    protected function createSelectInput(string $name, array $config): Select
    {
        $component = Select::make($name);

        if (isset($config['options'])) {
            $component->options($config['options']);
        }

        if ($config['searchable'] ?? false) {
            $component->searchable();
        }

        return $component;
    }

    protected function createRadioInput(string $name, array $config): Radio
    {
        $component = Radio::make($name);

        if (isset($config['options'])) {
            $component->options($config['options']);
        }

        // Support inline layout
        if ($config['inline'] ?? false) {
            $component->inline();
        }

        // Support descriptions (if provided in options)
        if ($config['show_descriptions'] ?? false) {
            // This would need custom styling or extended Radio component
            // For now, descriptions are handled via the main description field
        }

        return $component;
    }
}