<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('membership_packages')->count() === 0) {
            DB::table('membership_packages')->insert([
                [
                    'id' => 1,
                    'name' => 'Standard',
                    'title' => 'Standard Member',
                    'short_description' => 'Open to all. Join the GIHQS community and begin your professional journey.',
                    'price' => 0.00,
                    'discount_percentage' => 0.00,
                    'validity_days' => null,
                    'exam_attempt_limit' => 1,
                    'status' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'Premium',
                    'title' => 'Premium Member',
                    'short_description' => 'Premium members save on certification exams, renewal fees, and CE courses while gaining access to exclusive learning resources and member-only tools.',
                    'price' => 95.00,
                    'discount_percentage' => 15.00,
                    'validity_days' => 365,
                    'exam_attempt_limit' => 3,
                    'status' => 1,
                ]
            ]);

            DB::table('membership_package_features')->insert([
                [
                    'id' => 1,
                    'membership_package_id' => 1,
                    'description' => 'Open Enrollment',
                    'badge' => null,
                    'note' => 'No certification required to join',
                ],
                [
                    'id' => 2,
                    'membership_package_id' => 1,
                    'description' => 'Member Dashboard Access',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 3,
                    'membership_package_id' => 1,
                    'description' => 'Sample Exam Questions',
                    'badge' => null,
                    'note' => 'Limited access',
                ],
                [
                    'id' => 4,
                    'membership_package_id' => 1,
                    'description' => 'Exclusive Resource Library',
                    'badge' => null,
                    'note' => 'Selected resources',
                ],
                [
                    'id' => 5,
                    'membership_package_id' => 1,
                    'description' => 'Board / Advisory Opportunities',
                    'badge' => null,
                    'note' => 'General eligibility',
                ],
                [
                    'id' => 6,
                    'membership_package_id' => 1,
                    'description' => 'Premium Member Badge',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 7,
                    'membership_package_id' => 1,
                    'description' => 'Member-Only Courses',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 8,
                    'membership_package_id' => 1,
                    'description' => 'Certification Exam Discount',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 9,
                    'membership_package_id' => 1,
                    'description' => 'Renewal / Recertification Discount',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 10,
                    'membership_package_id' => 1,
                    'description' => 'CE Course Discount',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 11,
                    'membership_package_id' => 1,
                    'description' => 'Downloadable Templates / Toolkits / Checklists',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 12,
                    'membership_package_id' => 1,
                    'description' => 'Priority Support',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 13,
                    'membership_package_id' => 1,
                    'description' => 'Bonus CE Opportunities',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 14,
                    'membership_package_id' => 1,
                    'description' => 'Premium Reports / Insights',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 15,
                    'membership_package_id' => 2,
                    'description' => 'Open Enrollment',
                    'badge' => null,
                    'note' => 'No certification required to join',
                ],
                [
                    'id' => 16,
                    'membership_package_id' => 2,
                    'description' => 'Member Dashboard Access',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 17,
                    'membership_package_id' => 2,
                    'description' => 'Sample Exam Questions',
                    'badge' => 'Expanded Access',
                    'note' => null,
                ],
                [
                    'id' => 18,
                    'membership_package_id' => 2,
                    'description' => 'Exclusive Resource Library',
                    'badge' => 'Full Access',
                    'note' => null,
                ],
                [
                    'id' => 19,
                    'membership_package_id' => 2,
                    'description' => 'Board / Advisory Opportunities',
                    'badge' => null,
                    'note' => 'Preferred Consideration',
                ],
                [
                    'id' => 20,
                    'membership_package_id' => 2,
                    'description' => 'Premium Member Badge',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 21,
                    'membership_package_id' => 2,
                    'description' => 'Member-Only Courses',
                    'badge' => '15% Off',
                    'note' => null,
                ],
                [
                    'id' => 22,
                    'membership_package_id' => 2,
                    'description' => 'Certification Exam Discount',
                    'badge' => '15% Off',
                    'note' => null,
                ],
                [
                    'id' => 23,
                    'membership_package_id' => 2,
                    'description' => 'Renewal / Recertification Discount',
                    'badge' => '15% Off',
                    'note' => null,
                ],
                [
                    'id' => 24,
                    'membership_package_id' => 2,
                    'description' => 'CE Course Discount',
                    'badge' => '25% Off',
                    'note' => null,
                ],
                [
                    'id' => 25,
                    'membership_package_id' => 2,
                    'description' => 'Downloadable Templates / Toolkits / Checklists',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 26,
                    'membership_package_id' => 2,
                    'description' => 'Priority Support',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 27,
                    'membership_package_id' => 2,
                    'description' => 'Bonus CE Opportunities',
                    'badge' => null,
                    'note' => null,
                ],
                [
                    'id' => 28,
                    'membership_package_id' => 2,
                    'description' => 'Premium Reports / Insights',
                    'badge' => null,
                    'note' => null,
                ]
            ]);
        }
    }
}
