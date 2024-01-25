<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Client;
use App\Models\Job;

class JobBriefingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'job_id' => function() {
                return Job::where('jobs.status', '<>', Job::STATUS_CANCEL)
                    ->leftJoin('jobs_briefing', 'jobs_briefing.job_id', '=', 'jobs.id')
                    ->whereRaw('jobs_briefing.id IS NULL')
                    ->inRandomOrder()
                    ->first();
            },
            'objective' => $this->faker->text(),
            'material' => $this->faker->text(),
            'technical' => $this->faker->text(),
            'content_info' => $this->faker->text(),
            'creative_details' => $this->faker->text(),
            'deliverables' => $this->faker->text(),
            'notes' => $this->faker->text(),
        ];
    }
}
