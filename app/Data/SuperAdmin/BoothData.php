<?php

declare(strict_types=1);

namespace App\Data\SuperAdmin;

use App\Enums\BoothStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class BoothData
{
    public function __construct(
        public string $name,
        public string $inisial,
        public string $nomor_loket,
        public ?string $description,
        public ?UploadedFile $logo,
        public bool $is_open,
        public ?BoothStatus $status = null
    ) {}

    /**
     * Pabrikasi DTO dari objek Form Request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        $is_open = $request->has('is_open') ? (bool) $request->input('is_open') : true;

        $statusInput = $request->input('status');
        $statusEnum = null;
        if ($statusInput) {
            $statusEnum = $statusInput instanceof BoothStatus ? $statusInput : BoothStatus::tryFrom((string) $statusInput);
        }

        return new self(
            name: (string) $request->input('name'),
            inisial: (string) $request->input('inisial'),
            nomor_loket: (string) $request->input('nomor_loket'),
            description: $request->input('description') ? (string) $request->input('description') : null,
            logo: $request->file('logo'),
            is_open: $is_open,
            status: $statusEnum
        );
    }

    /**
     * Konversi properti DTO menjadi array asosiatif untuk model Department.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'inisial' => $this->inisial,
            'nomor_loket' => $this->nomor_loket,
            'description' => $this->description,
            'is_open' => $this->is_open,
        ];
    }
}
