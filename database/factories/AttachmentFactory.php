<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        $extensions = ['jpg', 'png', 'pdf', 'docx', 'xlsx'];
        $extension = $this->faker->randomElement($extensions);
        $filename = $this->faker->uuid() . '.' . $extension;
        $originalFilename = $this->faker->words(3, true) . '.' . $extension;
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return [
            'attachable_type' => Asset::class,
            'attachable_id' => Asset::factory(),
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => 'assets/' . $filename,
            'mime_type' => $mimeTypes[$extension],
            'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'uploaded_by' => User::factory(),
        ];
    }

    public function image(): static
    {
        $extension = $this->faker->randomElement(['jpg', 'png']);
        $filename = $this->faker->uuid() . '.' . $extension;
        $mimeType = $extension === 'jpg' ? 'image/jpeg' : 'image/png';

        return $this->state(fn () => [
            'filename' => $filename,
            'original_filename' => $this->faker->words(2, true) . '.' . $extension,
            'path' => 'assets/' . $filename,
            'mime_type' => $mimeType,
            'size' => $this->faker->numberBetween(51200, 5242880), // 50KB to 5MB
        ]);
    }

    public function document(): static
    {
        $extension = $this->faker->randomElement(['pdf', 'docx']);
        $filename = $this->faker->uuid() . '.' . $extension;
        $mimeType = $extension === 'pdf' 
            ? 'application/pdf' 
            : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        return $this->state(fn () => [
            'filename' => $filename,
            'original_filename' => $this->faker->words(3, true) . '.' . $extension,
            'path' => 'assets/' . $filename,
            'mime_type' => $mimeType,
            'size' => $this->faker->numberBetween(10240, 2097152), // 10KB to 2MB
        ]);
    }
}