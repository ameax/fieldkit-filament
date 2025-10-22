<?php

declare(strict_types=1);

return [
    'forms' => [
        'navigation_label' => 'FieldKit Forms',
        'label' => 'Form',
        'plural_label' => 'Forms',
        'navigation_group' => 'System',

        'fields' => [
            'purpose_token' => [
                'label' => 'Purpose Token',
                'helper' => 'Unique identifier for this form (e.g., customer_registration)',
                'placeholder' => 'customer_registration',
            ],
            'name' => [
                'label' => 'Form Name',
                'placeholder' => 'Customer Registration',
            ],
            'description' => [
                'label' => 'Description',
                'placeholder' => 'Additional fields for customer registration',
            ],
            'is_active' => [
                'label' => 'Active',
            ],
            'owner_type' => [
                'label' => 'Owner Type',
                'placeholder' => 'App\\Models\\Shop',
            ],
            'owner_id' => [
                'label' => 'Owner ID',
            ],
            'fields_count' => [
                'label' => 'Fields',
            ],
            'created_at' => [
                'label' => 'Created',
            ],
        ],

        'sections' => [
            'basic_information' => 'Basic Information',
            'multi_tenancy' => 'Multi-Tenancy (Optional)',
        ],

        'filters' => [
            'is_active' => 'Active Status',
        ],
    ],

    'definitions' => [
        'navigation_label' => 'Field Definitions',
        'label' => 'Field Definition',
        'plural_label' => 'Field Definitions',
        'title' => 'Field Definitions',

        'tabs' => [
            'definition' => 'Definition',
            'basic_settings' => 'Basic Settings',
            'validation' => 'Validation',
            'options' => 'Options',
            'external_mappings' => 'External Mappings',
            'conditional_visibility' => 'Conditional Visibility',
        ],

        'sections' => [
            'form_context' => 'Form & Context',
            'field_configuration' => 'Field Configuration',
            'display_settings' => 'Display Settings',
            'validation_rules' => 'Validation Rules',
            'field_options' => 'Field Options',
            'system_integrations' => 'System Integrations',
            'display_conditions' => 'Display Conditions',
        ],

        'fields' => [
            'form' => [
                'label' => 'Form',
            ],
            'fieldkit_form_id' => [
                'label' => 'Form',
            ],
            'field_key' => [
                'label' => 'Field Key',
                'helper' => 'Unique identifier for this field (e.g., customer_phone)',
                'placeholder' => 'customer_phone',
            ],
            'key' => [
                'label' => 'Field Key',
                'helper' => 'Unique identifier for this field (e.g., customer_phone)',
                'placeholder' => 'customer_phone',
            ],
            'type' => [
                'label' => 'Field Type',
                'helper' => 'Select the field type',
                'helper_disabled' => 'Type cannot be changed - field has existing data',
                'options' => [
                    'text' => 'Text Input',
                    'email' => 'Email Input',
                    'number' => 'Number Input',
                    'textarea' => 'Textarea',
                    'select' => 'Select Dropdown',
                    'radio' => 'Radio Buttons',
                    'checkbox' => 'Checkbox',
                ],
            ],
            'label' => [
                'label' => 'Label',
                'placeholder' => 'Phone Number',
                'display_label' => 'Display Label',
            ],
            'description' => [
                'label' => 'Description',
                'placeholder' => 'Enter your phone number',
            ],
            'placeholder' => [
                'label' => 'Placeholder',
                'placeholder' => '+1 (555) 123-4567',
            ],
            'sort_order' => [
                'label' => 'Sort Order',
                'helper' => 'Fields are displayed in ascending order',
                'short' => '#',
            ],
            'is_active' => [
                'label' => 'Active',
            ],
            'is_required' => [
                'label' => 'Required Field',
                'short' => 'Required',
            ],
            'validation_rules' => [
                'label' => 'Laravel Validation Rules',
                'placeholder' => 'Add validation rules...',
                'helper' => 'Type and press Enter. Add parameters like: min:3, max:255, size:10, between:1,10',
            ],
            'options' => [
                'label' => 'Options',
                'quick_label' => 'Options',
                'quick_helper' => 'Enter one option per line. Each will create an option with the label as display and slug as value.',
                'description' => 'Add options for select and radio fields. Users will see the label but the value will be stored.',
                'fields' => [
                    'value' => [
                        'label' => 'Value',
                        'placeholder' => 'yes',
                        'helper' => 'The actual value stored when this option is selected',
                    ],
                    'label' => [
                        'label' => 'Label',
                        'placeholder' => 'Yes',
                        'helper' => 'The text shown to users',
                    ],
                    'description' => [
                        'label' => 'Description',
                        'placeholder' => 'Optional description',
                        'helper' => 'Additional description text',
                    ],
                    'icon' => [
                        'label' => 'Icon',
                        'placeholder' => 'heroicon-o-check',
                        'helper' => 'Optional Heroicon name',
                    ],
                    'external_identifier' => [
                        'label' => 'External ID',
                        'placeholder' => 'ext_123',
                        'helper' => 'External system identifier',
                    ],
                    'sort_order' => [
                        'label' => 'Sort Order',
                        'helper' => 'Display order (lower numbers first)',
                    ],
                ],
            ],
            'options_count' => [
                'label' => 'Options',
            ],
            'created_at' => [
                'label' => 'Created',
            ],
            'external_mappings' => [
                'label' => 'External Mappings',
            ],
            'mappings' => [
                'label' => 'External Mappings',
            ],
            'adapter_type' => [
                'label' => 'Adapter Type',
                'options' => [
                    'ameax_column' => 'Ameax Database Column',
                    'mailchimp_api' => 'Mailchimp API',
                    'custom_webhook' => 'Custom Webhook',
                ],
            ],
            'adapter' => [
                'label' => 'Adapter Type',
                'options' => [
                    'ameax_column' => 'Ameax Database Column',
                    'mailchimp_api' => 'Mailchimp API',
                    'custom_webhook' => 'Custom Webhook',
                ],
            ],
            'target' => [
                'label' => 'Target',
                'placeholder' => 'customer.phone_number',
                'helper' => 'Dot notation path (e.g., customer.phone_number)',
            ],
            'config' => [
                'label' => 'Configuration',
                'key_label' => 'Key',
                'value_label' => 'Value',
                'helper' => 'Additional configuration for this mapping',
            ],
            'transformations' => [
                'label' => 'Transformations',
                'key_label' => 'Key',
                'value_label' => 'Value',
                'helper' => 'Additional transformations for this mapping',
            ],
            'conditions' => [
                'label' => 'Visibility Conditions',
                'conditions_label' => 'Conditions',
                'key_label' => 'Key',
                'value_label' => 'Value',
                'helper' => 'Conditions for this mapping',
            ],
            'field_type' => [
                'label' => 'Field Type',
                'options' => [
                    'native' => 'Native Field',
                    'fieldkit' => 'FieldKit Field',
                ],
            ],
            'operator' => [
                'label' => 'Operator',
                'options' => [
                    'in' => 'Value is in list',
                    'not_in' => 'Value is not in list',
                    'equals' => 'Value equals',
                    'not_equals' => 'Value does not equal',
                ],
            ],
            'expected_values' => [
                'label' => 'Expected Value',
                'label_plural' => 'Expected Values',
                'helper' => 'Single value that will show/hide this field',
                'helper_plural' => 'Values that will show/hide this field',
            ],
        ],

        'filters' => [
            'fieldkit_form_id' => 'Form',
            'type' => 'Type',
            'field_type' => 'Field Type',
            'is_required' => 'Required',
            'required_fields' => 'Required Fields',
            'is_active' => 'Active Status',
            'active_fields' => 'Active Fields',
        ],

        'actions' => [
            'add_option' => 'Add Option',
            'add_mapping' => 'Add Mapping',
            'add_condition' => 'Add Condition',
            'add_field' => 'Add Field',
        ],
    ],

    'options_relation_manager' => [
        'section' => [
            'option_details' => 'Option Details',
        ],
        'fields' => [
            'value' => [
                'label' => 'Value',
                'helper' => 'Stored in fieldkit_data',
            ],
            'label' => [
                'label' => 'Label',
                'helper' => 'Displayed to user',
            ],
            'description' => [
                'label' => 'Description',
                'helper' => 'Optional - for radio buttons with descriptions',
            ],
            'external_identifier' => [
                'label' => 'External ID',
                'helper' => 'Optional - ID for external system. Fallback: value',
                'placeholder' => '(uses value)',
            ],
            'sort_order' => [
                'label' => 'Sort Order',
                'short_label' => 'Order',
                'helper' => 'Options are displayed in ascending order',
            ],
        ],
    ],
];
