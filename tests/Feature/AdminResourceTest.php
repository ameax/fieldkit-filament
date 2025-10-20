<?php

declare(strict_types=1);

use Ameax\FieldkitFilament\Resources\FieldKitFormResource;
use Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource;
use Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\ListFieldKitForms;
use Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\ListFieldKitDefinitions;
use Ameax\FieldkitCore\Models\FieldKitForm;
use Ameax\FieldkitCore\Models\FieldKitDefinition;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
    Filament::setCurrentPanel('app');
});

it('can list fieldkit forms', function () {
    $form1 = FieldKitForm::factory()->create([
        'purpose_token' => 'customer_registration',
        'name' => 'Customer Registration',
        'is_active' => true,
    ]);
    
    $form2 = FieldKitForm::factory()->create([
        'purpose_token' => 'checkout_additional',
        'name' => 'Checkout Additional Fields',
        'is_active' => false,
    ]);
    
    \Livewire\Livewire::test(ListFieldKitForms::class)
        ->assertCanSeeTableRecords([$form1, $form2])
        ->assertTableColumnExists('purpose_token')
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('is_active');
});

it('can create fieldkit form', function () {
    \Livewire\Livewire::test(\Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\CreateFieldKitForm::class)
        ->fillForm([
            'purpose_token' => 'test_form',
            'name' => 'Test Form',
            'description' => 'A test form',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();
    
    $this->assertDatabaseHas('fieldkit_forms', [
        'purpose_token' => 'test_form',
        'name' => 'Test Form',
        'description' => 'A test form',
        'is_active' => true,
    ]);
});

it('can edit fieldkit form', function () {
    $form = FieldKitForm::factory()->create([
        'purpose_token' => 'original_token',
        'name' => 'Original Name',
    ]);
    
    \Livewire\Livewire::test(\Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\EditFieldKitForm::class, [
        'record' => $form->getRouteKey(),
    ])
        ->fillForm([
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ])
        ->call('save')
        ->assertHasNoFormErrors();
    
    $form->refresh();
    
    expect($form->name)->toBe('Updated Name');
    expect($form->description)->toBe('Updated description');
});

it('can list fieldkit definitions', function () {
    $form = FieldKitForm::factory()->create();
    
    $definition1 = FieldKitDefinition::factory()->create([
        'fieldkit_form_id' => $form->id,
        'field_key' => 'email',
        'type' => 'email',
        'label' => 'Email Address',
        'is_active' => true,
    ]);
    
    $definition2 = FieldKitDefinition::factory()->create([
        'fieldkit_form_id' => $form->id,
        'field_key' => 'phone',
        'type' => 'text',
        'label' => 'Phone Number',
        'is_active' => false,
    ]);
    
    \Livewire\Livewire::test(ListFieldKitDefinitions::class)
        ->assertCanSeeTableRecords([$definition1, $definition2])
        ->assertTableColumnExists('field_key')
        ->assertTableColumnExists('type')
        ->assertTableColumnExists('label')
        ->assertTableColumnExists('is_active');
});

it('can create fieldkit definition', function () {
    $form = FieldKitForm::factory()->create();
    
    \Livewire\Livewire::test(\Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\CreateFieldKitDefinition::class)
        ->fillForm([
            'fieldkit_form_id' => $form->id,
            'field_key' => 'test_field',
            'type' => 'text',
            'label' => 'Test Field',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors();
    
    $this->assertDatabaseHas('fieldkit_definitions', [
        'fieldkit_form_id' => $form->id,
        'field_key' => 'test_field',
        'type' => 'text',
        'label' => 'Test Field',
        'is_required' => true,
        'is_active' => true,
    ]);
});

it('can filter definitions by form', function () {
    $form1 = FieldKitForm::factory()->create(['name' => 'Form 1']);
    $form2 = FieldKitForm::factory()->create(['name' => 'Form 2']);
    
    $definition1 = FieldKitDefinition::factory()->create([
        'fieldkit_form_id' => $form1->id,
        'field_key' => 'field1',
    ]);
    
    $definition2 = FieldKitDefinition::factory()->create([
        'fieldkit_form_id' => $form2->id,
        'field_key' => 'field2',
    ]);
    
    \Livewire\Livewire::test(ListFieldKitDefinitions::class)
        ->filterTable('fieldkit_form_id', $form1->id)
        ->assertCanSeeTableRecords([$definition1])
        ->assertCanNotSeeTableRecords([$definition2]);
});

it('can filter definitions by type', function () {
    $form = FieldKitForm::factory()->create();
    
    $textDefinition = FieldKitDefinition::factory()->create([
        'fieldkit_form_id' => $form->id,
        'type' => 'text',
    ]);
    
    $emailDefinition = FieldKitDefinition::factory()->create([
        'fieldkit_form_id' => $form->id,
        'type' => 'email',
    ]);
    
    \Livewire\Livewire::test(ListFieldKitDefinitions::class)
        ->filterTable('type', 'text')
        ->assertCanSeeTableRecords([$textDefinition])
        ->assertCanNotSeeTableRecords([$emailDefinition]);
});