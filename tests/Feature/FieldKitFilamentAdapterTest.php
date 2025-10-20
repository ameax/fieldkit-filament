<?php

declare(strict_types=1);

use Ameax\FieldkitFilament\Adapters\FieldKitFilamentAdapter;
use Ameax\FieldkitCore\Models\FieldKitDefinition;
use Ameax\FieldkitCore\Models\FieldKitOption;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;

beforeEach(function () {
    $this->adapter = new FieldKitFilamentAdapter();
});

it('creates text input component', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'phone',
        'type' => 'text',
        'label' => 'Phone Number',
        'placeholder' => '+1 (555) 123-4567',
        'is_required' => true,
    ]);
    
    $component = $this->adapter->createComponent($definition);
    
    expect($component)->toBeInstanceOf(TextInput::class);
    expect($component->getName())->toBe('phone');
    expect($component->getLabel())->toBe('Phone Number');
    expect($component->isRequired())->toBeTrue();
});

it('creates email input component', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'email',
        'type' => 'email',
        'label' => 'Email Address',
        'is_required' => true,
    ]);
    
    $component = $this->adapter->createComponent($definition);
    
    expect($component)->toBeInstanceOf(TextInput::class);
    expect($component->getName())->toBe('email');
    expect($component->getLabel())->toBe('Email Address');
    expect($component->isRequired())->toBeTrue();
});

it('creates select component with options', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'category',
        'type' => 'select',
        'label' => 'Category',
        'is_required' => true,
    ]);
    
    // Mock the options relationship
    $definition->setRelation('options', collect([
        new FieldKitOption([
            'value' => 'electronics',
            'label' => 'Electronics',
            'sort_order' => 1,
        ]),
        new FieldKitOption([
            'value' => 'clothing',
            'label' => 'Clothing',
            'sort_order' => 2,
        ]),
    ]));
    
    $component = $this->adapter->createComponent($definition);
    
    expect($component)->toBeInstanceOf(Select::class);
    expect($component->getName())->toBe('category');
    expect($component->getLabel())->toBe('Category');
    expect($component->isRequired())->toBeTrue();
});

it('creates checkbox component', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'newsletter',
        'type' => 'checkbox',
        'label' => 'Subscribe to Newsletter',
        'is_required' => false,
    ]);
    
    $component = $this->adapter->createComponent($definition);
    
    expect($component)->toBeInstanceOf(Checkbox::class);
    expect($component->getName())->toBe('newsletter');
    expect($component->getLabel())->toBe('Subscribe to Newsletter');
    expect($component->isRequired())->toBeFalse();
});

it('applies validation rules correctly', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'phone',
        'type' => 'text',
        'label' => 'Phone',
        'is_required' => true,
        'validation_rules' => 'max:20|regex:/^\+[1-9]\d{1,14}$/',
        'min_length' => 5,
        'max_length' => 20,
    ]);
    
    $component = $this->adapter->createComponent($definition);
    
    expect($component)->toBeInstanceOf(TextInput::class);
    expect($component->isRequired())->toBeTrue();
});

it('applies conditional visibility correctly', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'company_phone',
        'type' => 'text',
        'label' => 'Company Phone',
        'conditions' => [
            [
                'field_type' => 'native',
                'field_key' => 'customer_type',
                'operator' => 'equals',
                'expected_values' => ['business']
            ]
        ],
    ]);
    
    $component = $this->adapter->createComponent($definition);
    
    expect($component)->toBeInstanceOf(TextInput::class);
    expect($component->getName())->toBe('company_phone');
});

it('converts form schema correctly', function () {
    $definitions = collect([
        new FieldKitDefinition([
            'field_key' => 'email',
            'type' => 'email',
            'label' => 'Email',
            'is_required' => true,
            'sort_order' => 1,
        ]),
        new FieldKitDefinition([
            'field_key' => 'phone',
            'type' => 'text',
            'label' => 'Phone',
            'is_required' => false,
            'sort_order' => 2,
        ]),
    ]);
    
    $schema = $this->adapter->convertToSchema($definitions);
    
    expect($schema)->toBeArray();
    expect($schema)->toHaveCount(2);
    expect($schema[0])->toBeInstanceOf(TextInput::class);
    expect($schema[1])->toBeInstanceOf(TextInput::class);
    expect($schema[0]->getName())->toBe('email');
    expect($schema[1]->getName())->toBe('phone');
});

it('throws exception for unsupported input type', function () {
    $definition = new FieldKitDefinition([
        'field_key' => 'test',
        'type' => 'unsupported_type',
        'label' => 'Test',
    ]);
    
    $this->adapter->createComponent($definition);
})->throws(InvalidArgumentException::class, 'Unsupported input type: unsupported_type');