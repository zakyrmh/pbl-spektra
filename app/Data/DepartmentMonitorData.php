<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Department;

class DepartmentMonitorData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $inisial,
        public ?string $description,
        public int $waitingCount,
        public int $servingCount,
        public string $density,
        public string $densityClass,
        public string $densityDot
    ) {}

    /**
     * Factory method to construct the DTO from a Department model and daily counts.
     */
    public static function fromModel(Department $department, int $waitingCount, int $servingCount): self
    {
        $density = 'Kosong';
        $densityClass = 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200/50 dark:border-white/5';
        $densityDot = 'bg-gray-400 dark:bg-gray-500';

        if ($waitingCount > 5) {
            $density = 'Padat';
            $densityClass = 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-900/50';
            $densityDot = 'bg-red-500';
        } elseif ($waitingCount > 0) {
            $density = 'Lancar';
            $densityClass = 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 border border-green-200/50 dark:border-green-900/50';
            $densityDot = 'bg-green-500';
        }

        return new self(
            id: $department->id,
            name: $department->name,
            inisial: $department->inisial,
            description: $department->description,
            waitingCount: $waitingCount,
            servingCount: $servingCount,
            density: $density,
            densityClass: $densityClass,
            densityDot: $densityDot
        );
    }

    /**
     * Convert the DTO to an array for JSON responses.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'inisial' => $this->inisial,
            'description' => $this->description,
            'waiting_count' => $this->waitingCount,
            'serving_count' => $this->servingCount,
            'density' => $this->density,
            'density_class' => $this->densityClass,
            'density_dot' => $this->densityDot,
        ];
    }
}
