<?php

use App\Models\User;
use App\Models\AdvisoryHeader;
use App\Models\AdvisoryFocus;
use App\Models\AdvisoryScope;
use App\Models\AdvisoryScopeFeature;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('advisory services scope features icon can be uploaded and saved', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $header = AdvisoryHeader::firstOrCreate([], ['title1' => 'Default Title 1']);
    $focus = AdvisoryFocus::firstOrCreate([]);
    $scope = AdvisoryScope::firstOrCreate([], ['title1' => 'Default Title 1']);
    \App\Models\AdvisoryDeliverableCard::firstOrCreate([], ['title1' => 'Default Title 1']);
    \App\Models\AdvisoryService::firstOrCreate([], ['title1' => 'Default Title 1']);
    \App\Models\AdvisoryDiscussCard::firstOrCreate([], ['title1' => 'Default Title 1']);

    // Mock file upload
    Storage::fake('public');
    $file = UploadedFile::fake()->image('scope_icon.png', 100, 100);

    $response = $this
        ->actingAs($user)
        ->put("/admin/advisory-services/{$header->id}", [
            'header_title1' => 'Header Title 1',
            'scope_title1' => 'Scope Title 1',
            'deliverable_title1' => 'Deliverable Title 1',
            'service_title1' => 'Service Title 1',
            'discuss_title1' => 'Discuss Title 1',
            'injected_status' => '1',
            
            'scope_features' => [
                [
                    'title' => 'Feature Title 1',
                    'description' => 'Feature Desc 1',
                    'icon' => $file
                ]
            ]
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $scope->refresh();
    expect($scope->features()->count())->toBe(1);
    
    $feature = $scope->features()->first();
    expect($feature->title)->toBe('Feature Title 1');
    expect($feature->icon)->not->toBeNull();

    $filePath = public_path($feature->icon);
    expect(file_exists($filePath))->toBeTrue();

    if (file_exists($filePath)) {
        unlink($filePath);
    }
});

test('advisory services scope features icon validation allows svg', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $header = AdvisoryHeader::firstOrCreate([], ['title1' => 'Default Title 1']);
    $scope = AdvisoryScope::firstOrCreate([], ['title1' => 'Default Title 1']);
    \App\Models\AdvisoryDeliverableCard::firstOrCreate([], ['title1' => 'Default Title 1']);
    \App\Models\AdvisoryService::firstOrCreate([], ['title1' => 'Default Title 1']);
    \App\Models\AdvisoryDiscussCard::firstOrCreate([], ['title1' => 'Default Title 1']);

    Storage::fake('public');
    // Create a dummy SVG file content
    $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2z"/></svg>';
    $file = UploadedFile::fake()->create('scope_icon.svg', 100, 'image/svg+xml');
    
    // We mock file write since fake() might not write actual contents needed for some mime checks
    file_put_contents($file->getRealPath(), $svgContent);

    $response = $this
        ->actingAs($user)
        ->put("/admin/advisory-services/{$header->id}", [
            'header_title1' => 'Header Title 1',
            'scope_title1' => 'Scope Title 1',
            'deliverable_title1' => 'Deliverable Title 1',
            'service_title1' => 'Service Title 1',
            'discuss_title1' => 'Discuss Title 1',
            'injected_status' => '1',
            
            'scope_features' => [
                [
                    'title' => 'Feature Title SVG',
                    'description' => 'Feature Desc SVG',
                    'icon' => $file
                ]
            ]
        ]);

    $response->assertSessionHasNoErrors();
});
