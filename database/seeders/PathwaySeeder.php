<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PathwayResult;
use App\Models\PathwayQuestion;
use App\Models\PathwayOption;

class PathwaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clean existing records to avoid duplicates
        PathwayOption::query()->delete();
        PathwayQuestion::query()->delete();
        PathwayResult::query()->delete();

        // 2. Create Pathway Results
        $resultAccreditation = PathwayResult::create([
            'title' => 'Apply for GIHQS Accreditation',
            'description' => 'Proceed directly to the GIHQS accreditation application route for recognised education, training, or certification offerings.',
            'badges' => ['Apply', 'Accreditation', 'Institutional'],
            'info_box_text' => 'This route is appropriate when your need is clear and you are ready to move from exploration to formal application.',
            'primary_button_text' => 'Apply for Accreditation',
            'primary_button_url' => '/accreditation/apply',
            'secondary_button_text' => 'View Accreditation',
            'secondary_button_url' => '/accreditation/overview',
            'status' => 1,
        ]);

        $resultOverview = PathwayResult::create([
            'title' => 'View Accreditation Overview',
            'description' => 'Explore the different types of GIHQS accreditations available for healthcare institutions and training providers.',
            'badges' => ['Explore', 'Overview', 'Institutional'],
            'info_box_text' => 'This route is ideal if you want to learn more about the requirements and benefits before applying.',
            'primary_button_text' => 'View Overview',
            'primary_button_url' => '/accreditation/overview',
            'secondary_button_text' => 'Download Brochure',
            'secondary_button_url' => '/brochure.pdf',
            'status' => 1,
        ]);

        $resultLearning = PathwayResult::create([
            'title' => 'Learning & Development Offerings',
            'description' => 'Access our comprehensive learning catalogue, featuring professional development courses, workshops, and certifications.',
            'badges' => ['Learning', 'Catalogue', 'Individual'],
            'info_box_text' => 'Best for individual professionals looking to upgrade their healthcare leadership skills.',
            'primary_button_text' => 'Explore Catalogue',
            'primary_button_url' => '/learning-catalogue',
            'secondary_button_text' => 'Contact Support',
            'secondary_button_url' => '/contact',
            'status' => 1,
        ]);

        $resultCertifications = PathwayResult::create([
            'title' => 'GIHQS Certification Pathways',
            'description' => 'Gain professional certification as a healthcare leader. Learn about eligibility, exams, and preparation materials.',
            'badges' => ['Certification', 'Professional', 'Individual'],
            'info_box_text' => 'Recommended if you are an individual aiming for personal certification in healthcare quality.',
            'primary_button_text' => 'Start Certification',
            'primary_button_url' => '/certifications/start',
            'secondary_button_text' => 'Preparation Guide',
            'secondary_button_url' => '/certifications/guide',
            'status' => 1,
        ]);

        // 3. Create Pathway Questions
        // Step 1 Question
        $qStep1 = PathwayQuestion::create([
            'step_number' => 1,
            'question_text' => 'Which best reflects your role or organisation?',
            'status' => 1,
        ]);

        // Step 2 Questions
        $qStep2Individual = PathwayQuestion::create([
            'step_number' => 2,
            'question_text' => 'What are you primarily looking for?',
            'status' => 1,
        ]);

        $qStep2Institution = PathwayQuestion::create([
            'step_number' => 2,
            'question_text' => 'How would you like to proceed?',
            'status' => 1,
        ]);

        // Step 3 Questions
        $qStep3CertLevel = PathwayQuestion::create([
            'step_number' => 3,
            'question_text' => 'What level of certification do you seek?',
            'status' => 1,
        ]);

        $qStep3LearningMode = PathwayQuestion::create([
            'step_number' => 3,
            'question_text' => 'Are you interested in online or classroom sessions?',
            'status' => 1,
        ]);

        // 4. Link Options with Questions and Next Targets
        
        // Options for Step 1 Question
        PathwayOption::create([
            'question_id' => $qStep1->id,
            'option_text' => 'Individual professional',
            'next_question_id' => $qStep2Individual->id,
            'result_id' => null,
            'order' => 1,
            'status' => 1,
        ]);

        PathwayOption::create([
            'question_id' => $qStep1->id,
            'option_text' => 'Training provider, university, or institution',
            'next_question_id' => $qStep2Institution->id,
            'result_id' => null,
            'order' => 2,
            'status' => 1,
        ]);

        PathwayOption::create([
            'question_id' => $qStep1->id,
            'option_text' => 'Organisation seeking advisory or capability-building support',
            'next_question_id' => $qStep2Institution->id,
            'result_id' => null,
            'order' => 3,
            'status' => 1,
        ]);

        // Options for Step 2 Question (Individual)
        PathwayOption::create([
            'question_id' => $qStep2Individual->id,
            'option_text' => 'Accreditation',
            'next_question_id' => $qStep3CertLevel->id,
            'result_id' => null,
            'order' => 1,
            'status' => 1,
        ]);

        PathwayOption::create([
            'question_id' => $qStep2Individual->id,
            'option_text' => 'Learning & Development offerings',
            'next_question_id' => $qStep3LearningMode->id,
            'result_id' => null,
            'order' => 2,
            'status' => 1,
        ]);

        // Options for Step 2 Question (Institution)
        PathwayOption::create([
            'question_id' => $qStep2Institution->id,
            'option_text' => 'View accreditation overview',
            'next_question_id' => null,
            'result_id' => $resultOverview->id,
            'order' => 1,
            'status' => 1,
        ]);

        PathwayOption::create([
            'question_id' => $qStep2Institution->id,
            'option_text' => 'Go directly to apply for accreditation',
            'next_question_id' => null,
            'result_id' => $resultAccreditation->id,
            'order' => 2,
            'status' => 1,
        ]);

        // Options for Step 3 Question (Certification Level)
        PathwayOption::create([
            'question_id' => $qStep3CertLevel->id,
            'option_text' => 'Executive Healthcare Leadership',
            'next_question_id' => null,
            'result_id' => $resultCertifications->id,
            'order' => 1,
            'status' => 1,
        ]);

        PathwayOption::create([
            'question_id' => $qStep3CertLevel->id,
            'option_text' => 'General Quality Management',
            'next_question_id' => null,
            'result_id' => $resultCertifications->id,
            'order' => 2,
            'status' => 1,
        ]);

        // Options for Step 3 Question (Learning Mode)
        PathwayOption::create([
            'question_id' => $qStep3LearningMode->id,
            'option_text' => 'Online Self-paced Courses',
            'next_question_id' => null,
            'result_id' => $resultLearning->id,
            'order' => 1,
            'status' => 1,
        ]);

        PathwayOption::create([
            'question_id' => $qStep3LearningMode->id,
            'option_text' => 'In-person Workshops',
            'next_question_id' => null,
            'result_id' => $resultLearning->id,
            'order' => 2,
            'status' => 1,
        ]);
    }
}
