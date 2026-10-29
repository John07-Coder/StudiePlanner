<?php

return [
    'required' => 'Het veld :attribute is verplicht.',
    'string' => 'Het veld :attribute moet tekst bevatten.',
    'email' => 'Vul een geldig e-mailadres in.',
    'unique' => 'Dit :attribute is al in gebruik.',
    'confirmed' => 'De bevestiging van :attribute komt niet overeen.',
    'min' => ['string' => 'Het veld :attribute moet minimaal :min tekens bevatten.'],
    'max' => ['string' => 'Het veld :attribute mag maximaal :max tekens bevatten.'],
    'integer' => 'Het veld :attribute moet een heel getal zijn.',
    'in' => 'De gekozen waarde voor :attribute is ongeldig.',
    'exists' => 'Het gekozen :attribute is niet beschikbaar.',
    'date_format' => 'Vul bij :attribute een geldige datum in (jaar-maand-dag).',
    'after_or_equal' => 'Het veld :attribute moet op of na :date liggen.',
    'before_or_equal' => 'Het veld :attribute moet op of voor :date liggen.',
    'attributes' => [
        'name' => 'naam', 'email' => 'e-mailadres', 'password' => 'wachtwoord',
        'title' => 'titel', 'description' => 'omschrijving', 'subject_id' => 'vak',
        'type' => 'type', 'priority' => 'prioriteit', 'due_date' => 'deadline',
        'date' => 'datum', 'color' => 'kleur', 'status' => 'status',
    ],
];

