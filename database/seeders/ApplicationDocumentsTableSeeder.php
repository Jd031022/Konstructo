<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationDocument;
use App\Models\User;
use Carbon\Carbon;

class ApplicationDocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an existing applicant user (Juan Dela Cruz - ID 56)
        $applicant = User::find(56);
        
        if (!$applicant) {
            $this->command->error('Applicant user (ID 56) not found!');
            return;
        }
        
        // Get a staff user for last_updated_by (John Santos - ID 48)
        $staff = User::find(48);
        
        if (!$staff) {
            $this->command->error('Staff user (ID 48) not found!');
            return;
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

        // Clear existing applications for this user (optional)
        // ApplicationDocument::where('user_id', $applicant->id)->delete();

        // ============================================================
        // 5 APPLICATIONS WITH DIFFERENT AGING SCENARIOS
        // ============================================================

        // 1. NEW APPLICATION (1 day old) - Green badge
        ApplicationDocument::create([
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
            'document_links' => null,
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // 2. WARNING APPLICATION (4 days old) - Yellow badge
        ApplicationDocument::create([
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
            'document_links' => null,
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);

        // 3. CRITICAL APPLICATION (8 days old) - Orange badge
        ApplicationDocument::create([
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
            'document_links' => null,
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        // 4. OVERDUE APPLICATION (15 days old) - Red badge with pulsing
        ApplicationDocument::create([
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
            'document_links' => null,
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(15),
        ]);

        // 5. UNDER REVIEW - OVERDUE (12 days old, under review status) - Red badge
        ApplicationDocument::create([
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
            'document_links' => null,
            'basic_requirement_id' => null,
            'created_at' => Carbon::now()->subDays(12),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        $this->command->info('✓ 5 test applications created for user: ' . $applicant->first_name . ' ' . $applicant->last_name);
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
        $this->command->info('You can now view these applications at: /staff/applications');
        $this->command->info('The aging colors and badges will be visible in the table.');
    }
}