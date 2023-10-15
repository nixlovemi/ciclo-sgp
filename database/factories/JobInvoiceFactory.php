<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobInvoiceFactory extends Factory
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
                    ->inRandomOrder()
                    ->first();
            },
            'invoice_number' => function() {
                return str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            },
            'invoice_date' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
            'due_date' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
            'total' => function() {
                return (float) rand(1, 1000) . '.' . rand(0, 99);
            },
            'invoice_path' => $this->faker->randomElement([
                'https://www.invoicesimple.com/wp-content/uploads/2022/12/InvoiceSimple-PDF-Template.pdf',
                'https://slicedinvoices.com/pdf/wordpress-pdf-invoice-plugin-sample.pdf',
                'https://wise.com/imaginary-v2/images/cff3e88f2d560c0df4cc43cb814d120f-invoice-template-PDF-3.pdf'
            ]),
        ];
    }
}
