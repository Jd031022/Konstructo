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
        // Use the same applicant user from the first seeder
        $applicant = User::where('email', 'applicant@konstructo.com')->first();
        
        if (!$applicant) {
            // Create the user if it doesn't exist (fallback)
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
            $this->command->info('✓ Using existing applicant: ' . $applicant->first_name . ' ' . $applicant->last_name . ' (ID: ' . $applicant->id . ')');
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
            $this->command->info('✓ Using existing staff: ' . $staff->first_name . ' ' . $staff->last_name . ' (ID: ' . $staff->id . ')');
        }

        // Find or create an admin user for admin actions
        $admin = User::where('email', 'admin@konstructo.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'middle_name' => null,
                'suffix' => null,
                'email' => 'admin@konstructo.com',
                'username' => 'adminuser',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone_number' => '09178888888',
                'address' => 'Admin Office',
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
            $this->command->info('✓ Created new admin: Admin User (ID: ' . $admin->id . ')');
        } else {
            $this->command->info('✓ Found existing admin: ' . $admin->first_name . ' ' . $admin->last_name . ' (ID: ' . $admin->id . ')');
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

        // Helper function to generate document links with different file types
        $generateDocumentLinks = function($appId, $type = 'standard') {
            $basePath = 'https://drive.google.com/file/d/';
            
            if ($type === 'incomplete') {
                return [
                    'app_letter_link' => $basePath . 'sample-app-letter-' . $appId . '/view',
                    'bp_forms_link' => null,
                    'arch_plans_link' => null,
                    'structural_plans_link' => null,
                    'electrical_plans_link' => null,
                    'plumbing_plans_link' => null,
                    'mechanical_plans_link' => null,
                    'fencing_plans_link' => null,
                    'ownership_link' => $basePath . 'sample-ownership-' . $appId . '/view',
                    'bom_link' => null,
                    'structural_analysis_link' => null,
                    'barangay_clearance_link' => $basePath . 'sample-barangay-clearance-' . $appId . '/view',
                    'valid_id_link' => $basePath . 'sample-valid-id-' . $appId . '/view',
                    'cshp_link' => null,
                ];
            } elseif ($type === 'complete') {
                return [
                    'app_letter_link' => $basePath . 'complete-app-letter-' . $appId . '/view',
                    'bp_forms_link' => $basePath . 'complete-bp-forms-' . $appId . '/view',
                    'arch_plans_link' => $basePath . 'complete-arch-plans-' . $appId . '/view',
                    'structural_plans_link' => $basePath . 'complete-structural-plans-' . $appId . '/view',
                    'electrical_plans_link' => $basePath . 'complete-electrical-plans-' . $appId . '/view',
                    'plumbing_plans_link' => $basePath . 'complete-plumbing-plans-' . $appId . '/view',
                    'mechanical_plans_link' => $basePath . 'complete-mechanical-plans-' . $appId . '/view',
                    'fencing_plans_link' => $basePath . 'complete-fencing-plans-' . $appId . '/view',
                    'ownership_link' => $basePath . 'complete-ownership-' . $appId . '/view',
                    'bom_link' => $basePath . 'complete-bom-' . $appId . '/view',
                    'structural_analysis_link' => $basePath . 'complete-structural-analysis-' . $appId . '/view',
                    'barangay_clearance_link' => $basePath . 'complete-barangay-clearance-' . $appId . '/view',
                    'valid_id_link' => $basePath . 'complete-valid-id-' . $appId . '/view',
                    'cshp_link' => $basePath . 'complete-cshp-' . $appId . '/view',
                ];
            } else {
                return [
                    'app_letter_link' => $basePath . 'app-letter-' . $appId . '/view',
                    'bp_forms_link' => $basePath . 'bp-forms-' . $appId . '/view',
                    'arch_plans_link' => $basePath . 'arch-plans-' . $appId . '/view',
                    'structural_plans_link' => $basePath . 'structural-plans-' . $appId . '/view',
                    'electrical_plans_link' => $basePath . 'electrical-plans-' . $appId . '/view',
                    'plumbing_plans_link' => $basePath . 'plumbing-plans-' . $appId . '/view',
                    'mechanical_plans_link' => $basePath . 'mechanical-plans-' . $appId . '/view',
                    'fencing_plans_link' => $basePath . 'fencing-plans-' . $appId . '/view',
                    'ownership_link' => $basePath . 'ownership-' . $appId . '/view',
                    'bom_link' => $basePath . 'bom-' . $appId . '/view',
                    'structural_analysis_link' => $basePath . 'structural-analysis-' . $appId . '/view',
                    'barangay_clearance_link' => $basePath . 'barangay-clearance-' . $appId . '/view',
                    'valid_id_link' => $basePath . 'valid-id-' . $appId . '/view',
                    'cshp_link' => $basePath . 'cshp-' . $appId . '/view',
                ];
            }
        };

        $this->command->info('');
        $this->command->info('Creating additional test applications for the same user...');
        $this->command->info('');

        // ============================================================
        // 5 MORE APPLICATIONS WITH DIFFERENT STATUSES
        // ============================================================

        // 6. APPROVED APPLICATION (3 days old, approved)
        $app6 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/approved-application-' . date('Ymd'),
            'status' => 'approved',
            'admin_notes' => 'Application approved. All requirements are complete. Building permit issued.',
            'rejection_reason' => null,
            'verified_at' => Carbon::now()->subDays(2),
            'verified_by' => $staff->id,
            'hard_copy_received' => true,
            'hard_copy_received_at' => Carbon::now()->subDays(2),
            'last_updated_by' => $staff->id,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(6, 'complete'),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        $this->command->info('  ✓ Created Application #6: ' . $app6->application_number . ' (Approved)');

        // 7. REJECTED APPLICATION (7 days old, rejected)
        $app7 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/rejected-application-' . date('Ymd'),
            'status' => 'rejected',
            'admin_notes' => 'Application rejected due to incomplete requirements and non-compliance with building codes.',
            'rejection_reason' => 'Incomplete documents: Missing structural analysis and electrical plans. Property ownership documents not properly notarized.',
            'verified_at' => Carbon::now()->subDays(3),
            'verified_by' => $admin->id,
            'hard_copy_received' => true,
            'hard_copy_received_at' => Carbon::now()->subDays(5),
            'last_updated_by' => $admin->id,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(7, 'incomplete'),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(3),
        ]);
        $this->command->info('  ✓ Created Application #7: ' . $app7->application_number . ' (Rejected)');

        // 8. ARCHIVED APPLICATION (30 days old, archived)
        $app8 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/archived-application-' . date('Ymd'),
            'status' => 'pending',
            'admin_notes' => 'Application archived due to 30 days of inactivity. No response from applicant after multiple follow-ups.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => false,
            'hard_copy_received_at' => null,
            'last_updated_by' => $admin->id,
            'is_archived' => true,
            'archived_at' => Carbon::now()->subDays(5),
            'archived_by' => $admin->id,
            'archive_reason' => 'No response from applicant for 30 days. Multiple follow-ups sent without reply.',
            'document_links' => $generateDocumentLinks(8),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(35),
            'updated_at' => Carbon::now()->subDays(5),
        ]);
        $this->command->info('  ✓ Created Application #8: ' . $app8->application_number . ' (Archived)');

        // 9. UNDER REVIEW - RECENTLY UPDATED (2 days old, under review)
        $app9 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/under-review-recent-' . date('Ymd'),
            'status' => 'under-review',
            'admin_notes' => 'Under review. Staff assigned for evaluation. Additional documents requested for clarification.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => true,
            'hard_copy_received_at' => Carbon::now()->subDays(1),
            'last_updated_by' => $staff->id,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(9, 'standard'),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subHours(6),
        ]);
        $this->command->info('  ✓ Created Application #9: ' . $app9->application_number . ' (Under Review - Recently Updated)');

        // 10. RESUBMITTED APPLICATION (after rejection, pending review)
        $app10 = ApplicationDocument::create([
            'user_id' => $applicant->id,
            'application_number' => $generateAppNumber(),
            'google_drive_link' => 'https://drive.google.com/drive/folders/resubmitted-application-' . date('Ymd'),
            'status' => 'pending',
            'admin_notes' => 'Resubmitted application after previous rejection. All requirements have been addressed. Waiting for re-evaluation.',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
            'hard_copy_received' => true,
            'hard_copy_received_at' => Carbon::now()->subDays(2),
            'last_updated_by' => $applicant->id,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'document_links' => $generateDocumentLinks(10, 'complete'),
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        $this->command->info('  ✓ Created Application #10: ' . $app10->application_number . ' (Resubmitted - Pending)');

        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✓ Additional 5 test applications created successfully for the same user!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('New applications created with different statuses:');
        $this->command->info('  ┌─────────────────────────────────────────────────────────────┐');
        $this->command->info('  │ 6. APPROVED (5 days old)        → Approved status badge    │');
        $this->command->info('  │ 7. REJECTED (10 days old)       → Rejected status badge    │');
        $this->command->info('  │ 8. ARCHIVED (35 days old)       → Archived status badge    │');
        $this->command->info('  │ 9. UNDER REVIEW (3 days old)    → Under review status      │');
        $this->command->info('  │ 10. RESUBMITTED (4 days old)    → Pending status (renewed) │');
        $this->command->info('  └─────────────────────────────────────────────────────────────┘');
        $this->command->info('');
        $this->command->info('Total applications for ' . $applicant->first_name . ' ' . $applicant->last_name . ': 10');
        $this->command->info('');
        $this->command->info('You can now view these applications at:');
        $this->command->info('  • Staff View:   /staff/applications');
        $this->command->info('  • Applicant View: /applicant/applications');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  • Applicant: applicant@konstructo.com / password123');
        $this->command->info('  • Staff:      staff@konstructo.com / password123');
        $this->command->info('  • Admin:      admin@konstructo.com / password123');
    }
}