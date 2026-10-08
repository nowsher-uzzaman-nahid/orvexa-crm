<?php

use App\Models\Company;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Services\LeadConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('lead can be converted to customer', function () {
    $convertedStatus = LeadStatus::factory()->create([
        'name' => 'Converted',
    ]);

    $lead = Lead::factory()->create();

    $customer = app(LeadConversionService::class)->convert($lead);

    expect($customer)->toBeInstanceOf(Customer::class);
    expect(Customer::count())->toBe(1);

    expect($customer->contact)->not->toBeNull();
    expect($customer->contact->company)->not->toBeNull();

    $lead->refresh();

    expect($lead->converted_at)->not->toBeNull();
    expect($lead->lead_status_id)->toBe($convertedStatus->id);

    expect($lead->customer_id)->toBe($customer->id);
    expect($lead->customer->is($customer))->toBeTrue();
    expect($customer->lead->is($lead))->toBeTrue();
});

test('lead without company can be converted to customer', function () {
    $convertedStatus = LeadStatus::factory()->create([
        'name' => 'Converted',
    ]);

    $lead = Lead::factory()->create([
        'company_name' => null,
    ]);

    $customer = app(LeadConversionService::class)->convert($lead);

    expect($customer)->toBeInstanceOf(Customer::class);
    expect($customer->contact)->not->toBeNull();
    expect($customer->contact->company_id)->toBeNull();

    expect(Company::count())->toBe(0);

    $lead->refresh();

    expect($lead->converted_at)->not->toBeNull();
    expect($lead->lead_status_id)->toBe($convertedStatus->id);
});

test('lead conversion reuses an existing company', function () {
    $convertedStatus = LeadStatus::factory()->create([
        'name' => 'Converted',
    ]);

    $company = Company::factory()->create([
        'name' => 'Acme Corporation',
    ]);

    $lead = Lead::factory()->create([
        'company_name' => '  acme corporation  ',
    ]);

    $customer = app(LeadConversionService::class)->convert($lead);

    expect(Company::count())->toBe(1);

    expect($customer->contact->company_id)
        ->toBe($company->id);

    $lead->refresh();

    expect($lead->converted_at)->not->toBeNull();
    expect($lead->lead_status_id)->toBe($convertedStatus->id);
});

test('lead conversion reuses an existing contact', function () {
    $convertedStatus = LeadStatus::factory()->create([
        'name' => 'Converted',
    ]);

    $company = Company::factory()->create([
        'name' => 'Acme Corporation',
    ]);

    $contact = Contact::factory()->create([
        'company_id' => $company->id,
        'email' => 'john@example.com',
    ]);

    $lead = Lead::factory()->create([
        'company_name' => 'Acme Corporation',
        'email' => '  JOHN@example.com  ',
    ]);

    expect($contact->email)
        ->toBe('john@example.com');

    expect(
        Contact::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', ['john@example.com'])
            ->exists()
    )->toBeTrue();

    $customer = app(LeadConversionService::class)->convert($lead);

    expect(Contact::count())->toBe(1);

    expect($customer->contact_id)
        ->toBe($contact->id);

    expect($customer->contact->email)
        ->toBe($contact->email);

    $lead->refresh();

    expect($lead->converted_at)
        ->not->toBeNull();

    expect($lead->lead_status_id)
        ->toBe($convertedStatus->id);
});

test('already converted lead cannot be converted again', function () {
    $convertedStatus = LeadStatus::factory()->create([
        'name' => 'Converted',
    ]);

    $lead = Lead::factory()->create([
        'lead_status_id' => $convertedStatus->id,
        'converted_at' => now(),
    ]);

    expect(fn () => app(LeadConversionService::class)->convert($lead))
        ->toThrow(
            \RuntimeException::class,
            'This lead has already been converted.'
        );

    expect(Customer::count())->toBe(0);
});

test('lead conversion rolls back when converted status is missing', function () {
    $lead = Lead::factory()->create([
        'company_name' => 'Rollback Test Company',
    ]);

    expect(fn () => app(LeadConversionService::class)->convert($lead))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect(Company::count())->toBe(0);
    expect(Contact::count())->toBe(0);
    expect(Customer::count())->toBe(0);

    $lead->refresh();

    expect($lead->converted_at)->toBeNull();
});

test('converted customer receives a generated customer code', function () {
    LeadStatus::factory()->create([
        'name' => 'Converted',
    ]);

    $lead = Lead::factory()->create();

    $customer = app(LeadConversionService::class)->convert($lead);

    expect($customer->customer_code)
        ->toStartWith('CUS-')
        ->and($customer->customer_code)
        ->toMatch('/^CUS-\d{6}$/');
});
