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

            // Make field live() if it's referenced as a dependency in other fields' conditions
            if ($this->isFieldDependency($name, $config)) {
                $component->live();
            }

            // Add conditional visibility if this field has conditions
            if (!empty($config['conditions'])) {
                $component->visible(function (\Filament\Schemas\Components\Utilities\Get $get) use ($config) {
                    return $this->evaluateConditions($config['conditions'], $get);
                });
            }

            if ($placeholder) {
                $component->placeholder($placeholder);
            }

            if ($description) {
                $component->helperText($description);
            }
        }

        return $component;
    }

    /**
     * Check if this field is referenced as a dependency in other fields' conditions
     */
    protected function isFieldDependency(string $fieldName, array $config): bool
    {
        // Check if this field is referenced in any condition's field_key
        $allFields = $config['all_fields'] ?? [];
        
        foreach ($allFields as $field) {
            $conditions = $field['conditions'] ?? [];
            foreach ($conditions as $condition) {
                if (($condition['field_key'] ?? null) === $fieldName) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Evaluate conditions for field visibility (mirrors FieldKitDefinition::shouldDisplay)
     */
    protected function evaluateConditions(array $conditions, \Filament\Schemas\Components\Utilities\Get $get): bool
    {
        if (empty($conditions)) {
            return true;
        }

        // ALL conditions must be met (AND)
        foreach ($conditions as $condition) {
            $fieldKey = $condition['field_key'] ?? null;
            $expectedValues = $condition['answer_values'] ?? [];
            $operator = $condition['operator'] ?? 'in';

            // Get value from form
            $actualValue = $get($fieldKey);

            // Dependent field not present
            if ($actualValue === null) {
                return false;
            }

            // Value normalization (bool → string)
            if (is_bool($actualValue)) {
                $actualValue = $actualValue ? 'true' : 'false';
            }

            switch ($operator) {
                case 'in':
                    if (!in_array($actualValue, $expectedValues, true)) {
                        return false;
                    }
                    break;

                case 'not_in':
                    if (in_array($actualValue, $expectedValues, true)) {
                        return false;
                    }
                    break;

                case 'equals':
                    $expectedValue = $expectedValues[0] ?? null;
                    if ($actualValue !== $expectedValue) {
                        return false;
                    }
                    break;

                case 'not_equals':
                    $expectedValue = $expectedValues[0] ?? null;
                    if ($actualValue === $expectedValue) {
                        return false;
                    }
                    break;

                default:
                    return false; // Unknown operator
            }
        }

        return true; // All conditions met
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