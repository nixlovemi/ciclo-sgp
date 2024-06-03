<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JobFile;
use App\Models\Job;
use App\Models\User;

class JobFileFactory extends Factory
{
    const FILES = [
        JobFile::TYPE_FILE => [
            '/img/demo/users/user-1.jpg',
            '/img/demo/users/user-2.jpg',
            '/img/demo/users/user-3.jpg',
        ],
        JobFile::TYPE_URL => [
            'https://www.africau.edu/images/default/sample.pdf',
            'https://file-examples.com/storage/fe235481fb64f1ca49a92b5/2017/10/file_example_JPG_100kB.jpg',
            'https://file-examples.com/storage/fe235481fb64f1ca49a92b5/2017/04/file_example_MP4_480_1_5MG.mp4',
        ],
    ];

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
                    ->inRandomOrder()
                    ->first();
            },
            'title' => $this->faker->text(60),
            'type' => $this->faker->randomElement(array_keys(JobFile::JOB_FILE_TYPES)),
            'job_section' => $this->faker->randomElement(array_merge([null], array_keys(JobFile::JOB_SECTIONS))),
            'create_user_id' => function(array $attributes) {
                $roleFilter = [User::ROLE_ADMIN, User::ROLE_MANAGER];

                switch ($attributes['job_section']) {
                    case JobFile::JOB_SECTION_BRIEFING_FINAL_REVIEW:
                        $roleFilter = array_merge($roleFilter, [User::ROLE_EDITOR]);
                        break;

                    case JobFile::JOB_SECTION_BRIEFING_FINALIZATION:
                        $roleFilter = array_merge($roleFilter, [User::ROLE_CREATIVE]);
                        break;
                    
                    default:
                        $roleFilter = array_merge($roleFilter, [User::ROLE_CREATIVE, User::ROLE_CUSTOMER]);
                        break;
                }

                return User::whereIn('role', $roleFilter)
                    ->where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'url' => function(array $attributes) {
                return $this->faker->randomElement(self::FILES[$attributes['type']]);
            },
        ];
    }
}
