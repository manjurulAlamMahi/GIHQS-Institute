<?php

use App\Models\MembershipPackage;

test('membership packages endpoint returns packages sorted by id ascending', function () {
    // Create packages.
    // They will be auto-assigned IDs sequentially (e.g. 1, 2, 3)
    $package1 = MembershipPackage::create([
        'name' => 'Package A',
        'title' => 'Package A Title',
        'price' => 10.00,
        'discount_percentage' => 0.00,
        'exam_attempt_limit' => 1,
        'status' => 1,
    ]);

    $package2 = MembershipPackage::create([
        'name' => 'Package B',
        'title' => 'Package B Title',
        'price' => 20.00,
        'discount_percentage' => 0.00,
        'exam_attempt_limit' => 1,
        'status' => 1,
    ]);

    $package3 = MembershipPackage::create([
        'name' => 'Package C',
        'title' => 'Package C Title',
        'price' => 30.00,
        'discount_percentage' => 0.00,
        'exam_attempt_limit' => 1,
        'status' => 1,
    ]);

    $response = $this->getJson('/api/membership-packages');

    $response->assertStatus(200);

    $packages = $response->json('data.membership_packages');

    expect($packages)->toHaveCount(3);
    expect($packages[0]['id'])->toBe($package1->id);
    expect($packages[1]['id'])->toBe($package2->id);
    expect($packages[2]['id'])->toBe($package3->id);
});
