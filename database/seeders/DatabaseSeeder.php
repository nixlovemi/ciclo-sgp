<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Job;
use App\Models\JobBriefing;
use App\Models\JobFile;
use App\Models\JobInvoice;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\ServiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->UserSeed();
        $this->ClientSeed();
        $this->JobSeed();
        $this->ServiceItemSeed();
        $this->QuoteSeed();
    }

    private function UserSeed(): void
    {
        foreach(array_keys(User::USER_ROLES) as $role) {
            foreach([true, false] as $active) {
                User::factory(1)->create([
                    'role' => $role,
                    'active' => $active,
                ]);
            }
        }
    }

    private function ClientSeed(): void
    {
        Client::factory(10)->create();
    }

    private function JobSeed(): void
    {
        Job::factory(5)
            ->has(JobBriefing::factory()->count(1), 'briefing')
            ->has(JobFile::factory()->count(3), 'files')
            ->has(JobInvoice::factory()->count(1), 'invoice')
            ->create();
    }

    private function ServiceItemSeed(): void
    {
        ServiceItem::factory(10)->create();
    }

    private function QuoteSeed(): void
    {
        $quoteQty = 15;
        Quote::factory($quoteQty)->create();

        // create between 0 and 4 items each Quote
        for ($i=1; $i<=$quoteQty; $i++) {
            $Quote = Quote::find($i);
            if ($Quote) {
                QuoteItem::factory()->count(rand(0, 4))->create([
                    'quote_id' => $Quote->id
                ]);
            }
        }

        // makes 2 jobs to have a quote
        $Quotes = Quote::where('active', true)
            ->limit(2)
            ->get();
        foreach ($Quotes as $Quote) {
            $Job = Job::where('status', '<>', Job::STATUS_CANCEL)
                ->whereRaw('quote_id IS NULL')
                ->inRandomOrder()
                ->first();
            $Job->quote_id = $Quote->id;
            $Job->update();
        }
    }
}
