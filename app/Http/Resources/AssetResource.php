<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'serial_number' => $this->serial_number,
            'department' => $this->department,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'purchase_cost' => (float) $this->purchase_cost,
            'purchase_cost_formatted' => $this->purchase_cost_formatted,
            'book_value' => $this->book_value,
            'book_value_formatted' => $this->book_value_formatted,
            'depreciation_method' => $this->depreciation_method,
            'depreciation_life_months' => $this->depreciation_life_months,
            'monthly_depreciation' => $this->monthly_depreciation,
            'warranty_expiry' => $this->warranty_expiry?->format('Y-m-d'),
            'warranty_status' => $this->warranty_status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            
            // Relationships
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                    'color' => $this->category->color,
                ];
            }),
            
            'location' => $this->whenLoaded('location', function () {
                return [
                    'id' => $this->location->id,
                    'name' => $this->location->name,
                    'slug' => $this->location->slug,
                    'full_address' => $this->location->full_address,
                ];
            }),
            
            'vendor' => $this->whenLoaded('vendor', function () {
                return [
                    'id' => $this->vendor->id,
                    'name' => $this->vendor->name,
                    'slug' => $this->vendor->slug,
                    'contact_person' => $this->vendor->contact_person,
                    'email' => $this->vendor->email,
                    'phone' => $this->vendor->phone,
                ];
            }),
            
            'assignee' => $this->whenLoaded('assignee', function () {
                return $this->assignee ? [
                    'id' => $this->assignee->id,
                    'name' => $this->assignee->name,
                    'email' => $this->assignee->email,
                ] : null;
            }),
            
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'color' => $tag->color,
                    ];
                });
            }),
            
            'attachments_count' => $this->whenCounted('attachments'),
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'filename' => $attachment->filename,
                        'original_filename' => $attachment->original_filename,
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->size,
                        'size_formatted' => $attachment->size_formatted,
                        'is_image' => $attachment->is_image,
                        'icon' => $attachment->icon,
                        'url' => $attachment->url,
                        'download_url' => $attachment->download_url,
                        'uploaded_at' => $attachment->created_at,
                    ];
                });
            }),
            
            // Links
            'links' => [
                'self' => route('api.v1.assets.show', $this->id),
                'web' => route('assets.show', $this->id),
                'qr_code' => route('assets.qr', $this->id),
            ],
        ];
    }
}