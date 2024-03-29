<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JobFile;
use App\Models\Job;

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
            'url' => function(array $attributes) {
                return $this->faker->randomElement(self::FILES[$attributes['type']]);
            },
        ];
    }
}
