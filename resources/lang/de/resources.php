<?php

declare(strict_types=1);

return [
    'forms' => [
        'navigation_label' => 'FieldKit Formulare',
        'label' => 'Formular',
        'plural_label' => 'Formulare',
        'navigation_group' => 'System',

        'fields' => [
            'purpose_token' => [
                'label' => 'Zweck-Token',
                'helper' => 'Eindeutige Kennung für dieses Formular (z.B. customer_registration)',
                'placeholder' => 'customer_registration',
            ],
            'name' => [
                'label' => 'Formularname',
                'placeholder' => 'Kundenregistrierung',
            ],
            'description' => [
                'label' => 'Beschreibung',
                'placeholder' => 'Zusätzliche Felder für die Kundenregistrierung',
            ],
            'is_active' => [
                'label' => 'Aktiv',
            ],
            'owner_type' => [
                'label' => 'Besitzertyp',
                'placeholder' => 'App\\Models\\Shop',
            ],
            'owner_id' => [
                'label' => 'Besitzer-ID',
            ],
            'fields_count' => [
                'label' => 'Felder',
            ],
            'created_at' => [
                'label' => 'Erstellt',
            ],
        ],

        'sections' => [
            'basic_information' => 'Grundinformationen',
            'multi_tenancy' => 'Mandantenfähigkeit (Optional)',
        ],

        'filters' => [
            'is_active' => 'Aktiv-Status',
        ],
    ],

    'definitions' => [
        'navigation_label' => 'Felddefinitionen',
        'label' => 'Felddefinition',
        'plural_label' => 'Felddefinitionen',
        'title' => 'Felddefinitionen',

        'tabs' => [
            'definition' => 'Definition',
            'basic_settings' => 'Grundeinstellungen',
            'validation' => 'Validierung',
            'options' => 'Optionen',
            'external_mappings' => 'Externe Zuordnungen',
            'conditional_visibility' => 'Bedingte Sichtbarkeit',
        ],

        'sections' => [
            'form_context' => 'Formular & Kontext',
            'field_configuration' => 'Feldkonfiguration',
            'display_settings' => 'Anzeigeeinstellungen',
            'validation_rules' => 'Validierungsregeln',
            'field_options' => 'Feldoptionen',
            'system_integrations' => 'Systemintegrationen',
            'display_conditions' => 'Anzeigebedingungen',
        ],

        'fields' => [
            'form' => [
                'label' => 'Formular',
            ],
            'fieldkit_form_id' => [
                'label' => 'Formular',
            ],
            'field_key' => [
                'label' => 'Feldschlüssel',
                'helper' => 'Eindeutige Kennung für dieses Feld (z.B. customer_phone)',
                'placeholder' => 'customer_phone',
            ],
            'key' => [
                'label' => 'Feldschlüssel',
                'helper' => 'Eindeutige Kennung für dieses Feld (z.B. customer_phone)',
                'placeholder' => 'customer_phone',
            ],
            'type' => [
                'label' => 'Feldtyp',
                'helper' => 'Feldtyp auswählen',
                'helper_disabled' => 'Typ kann nicht geändert werden - Feld hat vorhandene Daten',
                'options' => [
                    'text' => 'Texteingabe',
                    'email' => 'E-Mail-Eingabe',
                    'number' => 'Zahleneingabe',
                    'textarea' => 'Textbereich',
                    'select' => 'Auswahlmenü',
                    'radio' => 'Optionsfelder',
                    'checkbox' => 'Kontrollkästchen',
                ],
            ],
            'label' => [
                'label' => 'Bezeichnung',
                'placeholder' => 'Telefonnummer',
                'display_label' => 'Anzeigebezeichnung',
            ],
            'description' => [
                'label' => 'Beschreibung',
                'placeholder' => 'Geben Sie Ihre Telefonnummer ein',
            ],
            'placeholder' => [
                'label' => 'Platzhalter',
                'placeholder' => '+49 (123) 456-7890',
            ],
            'sort_order' => [
                'label' => 'Sortierreihenfolge',
                'helper' => 'Felder werden in aufsteigender Reihenfolge angezeigt',
                'short' => '#',
            ],
            'is_active' => [
                'label' => 'Aktiv',
            ],
            'is_required' => [
                'label' => 'Pflichtfeld',
                'short' => 'Erforderlich',
            ],
            'validation_rules' => [
                'label' => 'Laravel-Validierungsregeln',
                'placeholder' => 'Validierungsregeln hinzufügen...',
                'helper' => 'Eingeben und Enter drücken. Parameter hinzufügen wie: min:3, max:255, size:10, between:1,10',
            ],
            'options' => [
                'label' => 'Optionen',
                'quick_label' => 'Optionen',
                'quick_helper' => 'Eine Option pro Zeile eingeben. Jede erstellt eine Option mit der Bezeichnung als Anzeige und Slug als Wert.',
                'description' => 'Optionen für Auswahl- und Optionsfelder hinzufügen. Benutzer sehen die Bezeichnung, aber der Wert wird gespeichert.',
                'fields' => [
                    'value' => [
                        'label' => 'Wert',
                        'placeholder' => 'ja',
                        'helper' => 'Der tatsächliche Wert, der gespeichert wird, wenn diese Option ausgewählt wird',
                    ],
                    'label' => [
                        'label' => 'Bezeichnung',
                        'placeholder' => 'Ja',
                        'helper' => 'Der Text, der den Benutzern gezeigt wird',
                    ],
                    'description' => [
                        'label' => 'Beschreibung',
                        'placeholder' => 'Optionale Beschreibung',
                        'helper' => 'Zusätzlicher Beschreibungstext',
                    ],
                    'icon' => [
                        'label' => 'Symbol',
                        'placeholder' => 'heroicon-o-check',
                        'helper' => 'Optionaler Heroicon-Name',
                    ],
                    'external_identifier' => [
                        'label' => 'Externe ID',
                        'placeholder' => 'ext_123',
                        'helper' => 'Kennung des externen Systems',
                    ],
                    'sort_order' => [
                        'label' => 'Sortierreihenfolge',
                        'helper' => 'Anzeigereihenfolge (niedrigere Zahlen zuerst)',
                    ],
                ],
            ],
            'options_count' => [
                'label' => 'Optionen',
            ],
            'created_at' => [
                'label' => 'Erstellt',
            ],
            'external_mappings' => [
                'label' => 'Externe Zuordnungen',
            ],
            'mappings' => [
                'label' => 'Externe Zuordnungen',
            ],
            'adapter_type' => [
                'label' => 'Adapter-Typ',
                'options' => [
                    'ameax_column' => 'Ameax-Datenbankspalte',
                    'mailchimp_api' => 'Mailchimp-API',
                    'custom_webhook' => 'Benutzerdefinierter Webhook',
                ],
            ],
            'adapter' => [
                'label' => 'Adapter-Typ',
                'options' => [
                    'ameax_column' => 'Ameax-Datenbankspalte',
                    'mailchimp_api' => 'Mailchimp-API',
                    'custom_webhook' => 'Benutzerdefinierter Webhook',
                ],
            ],
            'target' => [
                'label' => 'Ziel',
                'placeholder' => 'customer.phone_number',
                'helper' => 'Punkt-Notation-Pfad (z.B. customer.phone_number)',
            ],
            'config' => [
                'label' => 'Konfiguration',
                'key_label' => 'Schlüssel',
                'value_label' => 'Wert',
                'helper' => 'Zusätzliche Konfiguration für diese Zuordnung',
            ],
            'transformations' => [
                'label' => 'Transformationen',
                'key_label' => 'Schlüssel',
                'value_label' => 'Wert',
                'helper' => 'Zusätzliche Transformationen für diese Zuordnung',
            ],
            'conditions' => [
                'label' => 'Sichtbarkeitsbedingungen',
                'conditions_label' => 'Bedingungen',
                'key_label' => 'Schlüssel',
                'value_label' => 'Wert',
                'helper' => 'Bedingungen für diese Zuordnung',
            ],
            'field_type' => [
                'label' => 'Feldtyp',
                'options' => [
                    'native' => 'Natives Feld',
                    'fieldkit' => 'FieldKit-Feld',
                ],
            ],
            'operator' => [
                'label' => 'Operator',
                'options' => [
                    'in' => 'Wert ist in der Liste',
                    'not_in' => 'Wert ist nicht in der Liste',
                    'equals' => 'Wert entspricht',
                    'not_equals' => 'Wert entspricht nicht',
                ],
            ],
            'expected_values' => [
                'label' => 'Erwarteter Wert',
                'label_plural' => 'Erwartete Werte',
                'helper' => 'Einzelner Wert, der dieses Feld ein-/ausblendet',
                'helper_plural' => 'Werte, die dieses Feld ein-/ausblenden',
            ],
        ],

        'filters' => [
            'fieldkit_form_id' => 'Formular',
            'type' => 'Typ',
            'field_type' => 'Feldtyp',
            'is_required' => 'Erforderlich',
            'required_fields' => 'Pflichtfelder',
            'is_active' => 'Aktiv-Status',
            'active_fields' => 'Aktive Felder',
        ],

        'actions' => [
            'add_option' => 'Option hinzufügen',
            'add_mapping' => 'Zuordnung hinzufügen',
            'add_condition' => 'Bedingung hinzufügen',
            'add_field' => 'Feld hinzufügen',
        ],
    ],

    'options_relation_manager' => [
        'section' => [
            'option_details' => 'Optionsdetails',
        ],
        'fields' => [
            'value' => [
                'label' => 'Wert',
                'helper' => 'Gespeichert in fieldkit_data',
            ],
            'label' => [
                'label' => 'Bezeichnung',
                'helper' => 'Dem Benutzer angezeigt',
            ],
            'description' => [
                'label' => 'Beschreibung',
                'helper' => 'Optional - für Optionsfelder mit Beschreibungen',
            ],
            'external_identifier' => [
                'label' => 'Externe ID',
                'helper' => 'Optional - ID für externes System. Fallback: Wert',
                'placeholder' => '(verwendet Wert)',
            ],
            'sort_order' => [
                'label' => 'Sortierreihenfolge',
                'short_label' => 'Reihenfolge',
                'helper' => 'Optionen werden in aufsteigender Reihenfolge angezeigt',
            ],
        ],
    ],
];
