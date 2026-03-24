<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('==========================================');
        $this->command->info('Starting Conversation Seeder');
        $this->command->info('==========================================');
        
        // Use existing support team (ID: 1)
        $support = User::find(1);
        
        // Use existing staff members as admin and engineer
        // Let's use staff with ID 2 as "Admin" and ID 3 as "Engineer"
        $admin = User::find(2); // John Santos
        $engineer = User::find(3); // Maria Reyes
        
        if (!$support) {
            $this->command->error('Support team not found!');
            return;
        }
        
        $this->command->info("\nStaff Users:");
        $this->command->info("- Support Team: {$support->full_name} (ID: {$support->id})");
        if ($admin) $this->command->info("- Admin (John Santos): {$admin->full_name} (ID: {$admin->id})");
        if ($engineer) $this->command->info("- Engineer (Maria Reyes): {$engineer->full_name} (ID: {$engineer->id})");
        
        // Get all applicants
        $applicants = User::where('role', 'applicant')
            ->where('approval_status', 'approved')
            ->get();
        
        if ($applicants->isEmpty()) {
            $this->command->error('No applicants found!');
            return;
        }
        
        $this->command->info("\nFound " . $applicants->count() . " applicant(s):");
        foreach ($applicants as $applicant) {
            $this->command->info("- {$applicant->full_name} (ID: {$applicant->id})");
        }
        
        // Optional: Clear existing conversations
        $clearExisting = $this->command->confirm('Do you want to clear existing conversations?', false);
        
        if ($clearExisting) {
            $this->command->warn('Clearing existing conversations...');
            DB::table('conversation_participants')->delete();
            DB::table('messages')->delete();
            DB::table('conversations')->delete();
            $this->command->info('Existing conversations cleared!');
        }
        
        $conversationsCreated = 0;
        
        foreach ($applicants as $applicant) {
            $this->command->info("\n----------------------------------------");
            $this->command->info("Processing applicant: {$applicant->full_name}");
            
            // Create conversation with Support Team
            $existingSupportConv = Conversation::whereHas('participants', function ($q) use ($applicant) {
                $q->where('user_id', $applicant->id);
            })->whereHas('participants', function ($q) use ($support) {
                $q->where('user_id', $support->id);
            })->where('type', 'private')->first();
            
            if (!$existingSupportConv) {
                $supportConv = Conversation::create(['type' => 'private']);
                $supportConv->participants()->attach([$applicant->id, $support->id]);
                
                Message::create([
                    'conversation_id' => $supportConv->id,
                    'user_id' => $support->id,
                    'content' => 'Hello! Welcome to Konstructo Support. How can we help you today?',
                    'type' => 'text'
                ]);
                
                Message::create([
                    'conversation_id' => $supportConv->id,
                    'user_id' => $applicant->id,
                    'content' => 'Hi! I have a question about my application status.',
                    'type' => 'text'
                ]);
                
                $conversationsCreated++;
                $this->command->info("  ✓ Created conversation with Support Team");
            } else {
                $this->command->info("  • Conversation with Support Team already exists");
            }
            
            // Create conversation with Admin (John Santos)
            if ($admin) {
                $existingAdminConv = Conversation::whereHas('participants', function ($q) use ($applicant) {
                    $q->where('user_id', $applicant->id);
                })->whereHas('participants', function ($q) use ($admin) {
                    $q->where('user_id', $admin->id);
                })->where('type', 'private')->first();
                
                if (!$existingAdminConv) {
                    $adminConv = Conversation::create(['type' => 'private']);
                    $adminConv->participants()->attach([$applicant->id, $admin->id]);
                    
                    Message::create([
                        'conversation_id' => $adminConv->id,
                        'user_id' => $admin->id,
                        'content' => 'Your application is currently being processed. Please wait for further updates.',
                        'type' => 'text'
                    ]);
                    
                    $conversationsCreated++;
                    $this->command->info("  ✓ Created conversation with Admin (John Santos)");
                } else {
                    $this->command->info("  • Conversation with Admin already exists");
                }
            }
            
            // Create conversation with Engineer (Maria Reyes)
            if ($engineer) {
                $existingEngineerConv = Conversation::whereHas('participants', function ($q) use ($applicant) {
                    $q->where('user_id', $applicant->id);
                })->whereHas('participants', function ($q) use ($engineer) {
                    $q->where('user_id', $engineer->id);
                })->where('type', 'private')->first();
                
                if (!$existingEngineerConv) {
                    $engineerConv = Conversation::create(['type' => 'private']);
                    $engineerConv->participants()->attach([$applicant->id, $engineer->id]);
                    
                    Message::create([
                        'conversation_id' => $engineerConv->id,
                        'user_id' => $engineer->id,
                        'content' => 'Technical review in progress for your application. We will notify you once completed.',
                        'type' => 'text'
                    ]);
                    
                    $conversationsCreated++;
                    $this->command->info("  ✓ Created conversation with Engineer (Maria Reyes)");
                } else {
                    $this->command->info("  • Conversation with Engineer already exists");
                }
            }
        }
        
        $this->command->info("\n==========================================");
        $this->command->info("Seeder Completed Successfully!");
        $this->command->info("==========================================");
        $this->command->info("Total new conversations created: {$conversationsCreated}");
        $this->command->info("Total applicants processed: " . $applicants->count());
        $this->command->info("\nYour chat system is now ready!");
        $this->command->info("You can now start messaging between applicants and staff.");
    }
}