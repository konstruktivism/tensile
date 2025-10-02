<?php

use App\Models\Organisation;
use App\Models\Project;

it('can create an organisation', function () {
    $organisation = Organisation::factory()->create([
        'name' => 'Test Organisation',
        'email' => 'test@organisation.com',
    ]);

    expect($organisation->name)->toBe('Test Organisation');
    expect($organisation->email)->toBe('test@organisation.com');
    expect($organisation->exists)->toBeTrue();
});

it('has many projects', function () {
    $organisation = Organisation::factory()->create();
    $projects = Project::factory()->count(3)->create(['organisation_id' => $organisation->id]);

    expect($organisation->projects)->toHaveCount(3);
    expect($organisation->projects()->count())->toBe(3);
});

it('can have projects with different types', function () {
    $organisation = Organisation::factory()->create();

    $fixedProject = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'is_fixed' => true,
    ]);

    $hourlyProject = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'is_fixed' => false,
    ]);

    expect($organisation->projects)->toHaveCount(2);
    expect($organisation->projects->where('is_fixed', true)->count())->toBe(1);
    expect($organisation->projects->where('is_fixed', false)->count())->toBe(1);
});

it('has fillable attributes', function () {
    $organisation = new Organisation();
    $fillable = $organisation->getFillable();

    expect($fillable)->toContain('name', 'email', 'address', 'phone', 'website');
});

it('can have nullable optional fields', function () {
    $organisation = Organisation::factory()->create([
        'address' => null,
        'phone' => null,
        'website' => null,
    ]);

    expect($organisation->address)->toBeNull();
    expect($organisation->phone)->toBeNull();
    expect($organisation->website)->toBeNull();
});

it('can have all contact information', function () {
    $organisation = Organisation::factory()->create([
        'name' => 'Full Organisation',
        'email' => 'contact@fullorg.com',
        'address' => '123 Main St, City, Country',
        'phone' => '+1-555-123-4567',
        'website' => 'https://fullorg.com',
    ]);

    expect($organisation->name)->toBe('Full Organisation');
    expect($organisation->email)->toBe('contact@fullorg.com');
    expect($organisation->address)->toBe('123 Main St, City, Country');
    expect($organisation->phone)->toBe('+1-555-123-4567');
    expect($organisation->website)->toBe('https://fullorg.com');
});
