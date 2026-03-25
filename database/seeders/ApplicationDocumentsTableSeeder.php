<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationDocument;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ApplicationDocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create an applicant user
        $applicant = User::where('email', 'applicant@konstructo.com')->first();
        
        if (!$applicant) {
            $applicant = User::create([
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Gonzales',
                'suffix' => null,
                'email' => 'applicant@konstructo.com',
                'username' => 'juandela.cruz',
                'password' => Hash::make('password123'),
                'role' => 'applicant',
                'phone_number' => '09171234567',
                'address' => 'Brgy. San Jose',
                'zip_code' => '4500',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => null,
                'avatar' => null,
                'rejection_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Created new applicant: Juan Dela Cruz (ID: ' . $applicant->id . ')');
        } else {
            $this->command->info('✓ Found existing applicant: ' . $applicant->first_name . ' ' . $applicant->last_name . ' (ID: ' . $applicant->id . ')');
        }
        
        // Find or create a staff user for last_updated_by
        $staff = User::where('email', 'staff@konstructo.com')->first();
        
        if (!$staff) {
            $staff = User::create([
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'middle_name' => 'Reyes',
                'suffix' => null,
                'email' => 'staff@konstructo.com',
                'username' => 'mariasantos',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone_number' => '09177654321',
                'address' => 'Brgy. Centro',
                'zip_code' => '4500',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => null,
                'avatar' => null,
                'rejection_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Created new staff: Maria Santos (ID: ' . $staff->id . ')');
        } else {
            $this->command->info('✓ Found existing staff: ' . $staff->first_name . ' ' . $staff->last_name . ' (ID: ' . $staff->id . ')');
        }

        // Helper function to generate unique application numbers
        $generateAppNumber = function() {
            $year = date('Y');
            do {
                $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $appNumber = $year . $random;
            } while (ApplicationDocument::where('application_number', $appNumber)->exists());
            return $appNumber;
        };

        // Helper function to generate document links
        $generateDocumentLinks = function($appId) {
            return [
                'app_letter_link' => 'https://drive.google.com/file/d/sample-app-letter-' . $appId . '/view',
                'bp_forms_link' => 'https://drive.google.com/file/d/sample-bp-forms-' . $appId . '/view',
                'arch_plans_link' => 'https://drive.google.com/file/d/sample-arch-plans-' . $appId . '/view',
                'structural_plans_link' => 'https://drive.google.com/file/d/sample-structural-plans-' . $appId . '/view',
                'electrical_plans_link' => 'https://drive.google.com/file/d/sample-electrical-plans-' . $appId . '/view',
                'plumbing_plans_link' => 'https://drive.google.com/file/d/sample-plumbing-plans-' . $appId . '/view',
                'mechanical_plans_link' => 'https://drive.google.com/file/d/sample-mechanical-plans-' . $appId . '/view',
                'fencing_plans_link' => 'https://drive.google.com/file/d/sample-fencing-plans-' . $appId . '/view',
                'ownership_link' => 'https://drive.google.com/file/d/sample-ownership-' . $appId . '/view',
                'bom_link' => 'https://drive.google.com/file/d/sample-bom-' . $appId . '/view',
                'structural_analysis_link' => 'https://drive.google.com/file/d/sample-structural-analysis-' . $appId . '/view',
                'barangay_clearance_link' => 'https://drive.google.com/file/d/sample-barangay-clearance-' . $appId . '/view',
                'valid_id_link' => 'https://drive.google.com/file/d/sample-valid-id-' . $appId . '/view',
                'cshp_link' => 'https://drive.google.com/file/d/sample-cshp-' . $appId . '/view',
            ];
        };

        // Clear existing applications for this user (optional - uncomment if needed)
        // ApplicationDocument::where('user_id', $applicant->id)->delete();

        $this->command->info('');
        $this->command->info('Creating test applications...');
        $this->command->info('');

        // ============================================================
        // 5 APPLICATIONS WITH DIFFERENT AGING SCENARIOS
        // ============================================================

        // 1. NEW APPLICATION (1 day old) - Green badge
        $app1 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/new-application-' . date('Ymd'),
            'status' => 'pending',
            'admin_notes' => 'New application submitted. Initial review pending.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => false,
            'hard_copy_received_at' => null,
            'last_updated_by' => null,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(1),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);
        $this->command->info('  ✓ Created Application #1: ' . $app1->application_number . ' (1 day old - pending)');

        // 2. WARNING APPLICATION (4 days old) - Yellow badge
        $app2 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/warning-application-' . date('Ymd'),
            'status' => 'pending',
            'admin_notes' => 'Application pending for 4 days. Follow-up recommended.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => false,
            'hard_copy_received_at' => null,
            'last_updated_by' => null,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(2),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);
        $this->command->info('  ✓ Created Application #2: ' . $app2->application_number . ' (4 days old - pending)');

        // 3. CRITICAL APPLICATION (8 days old) - Orange badge
        $app3 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/critical-application-' . date('Ymd'),
            'status' => 'pending',
            'admin_notes' => 'CRITICAL: Application pending for 8 days. Multiple follow-ups sent. Urgent attention needed.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => false,
            'hard_copy_received_at' => null,
            'last_updated_by' => null,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(3),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ]);
        $this->command->info('  ✓ Created Application #3: ' . $app3->application_number . ' (8 days old - pending)');

        // 4. OVERDUE APPLICATION (15 days old) - Red badge with pulsing
        $app4 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/overdue-application-1-' . date('Ymd'),
            'status' => 'pending',
            'admin_notes' => 'OVERDUE: Application pending for 15 days. Final notice sent. Consider archiving if no response.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => false,
            'hard_copy_received_at' => null,
            'last_updated_by' => null,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(4),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(15),
        ]);
        $this->command->info('  ✓ Created Application #4: ' . $app4->application_number . ' (15 days old - pending)');

        // 5. UNDER REVIEW - OVERDUE (12 days old, under review status) - Red badge
        $app5 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/under-review-overdue-' . date('Ymd'),
            'status' => 'under-review',
            'admin_notes' => 'UNDER REVIEW for 12 days. Waiting for additional documentation. No response from applicant.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => true,
            'hard_copy_received_at' => Carbon::now()->subDays(10),
            'last_updated_by' => $staff->id,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(5),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(12),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        $this->command->info('  ✓ Created Application #5: ' . $app5->application_number . ' (12 days old - under review)');

        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✓ All 5 test applications created successfully!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('Applications created with different aging statuses:');
        $this->command->info('  ┌─────────────────────────────────────────────────────────────┐');
        $this->command->info('  │ 1. NEW (1 day old)        → Green badge / Green row border  │');
        $this->command->info('  │ 2. WARNING (4 days old)   → Yellow badge / Yellow row border│');
        $this->command->info('  │ 3. CRITICAL (8 days old)  → Orange badge / Orange row border│');
        $this->command->info('  │ 4. OVERDUE (15 days old)  → Red badge / Red row (pulsing)   │');
        $this->command->info('  │ 5. OVERDUE (12 days old)  → Red badge / Red row (pulsing)   │');
        $this->command->info('  └─────────────────────────────────────────────────────────────┘');
        $this->command->info('');
        $this->command->info('You can now view these applications at:');
        $this->command->info('  • Staff View:   /staff/applications');
        $this->command->info('  • Applicant View: /applicant/applications');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  • Applicant: applicant@konstructo.com / password123');
        $this->command->info('  • Staff:      staff@konstructo.com / password123');
    }
}