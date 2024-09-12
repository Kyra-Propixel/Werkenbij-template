<?php

namespace PHPageBuilder\Repositories;

use PHPageBuilder\Repositories\BaseRepository;
use PHPageBuilder\Contracts\HeaderSettingRepositoryContract;

class HeaderSettingRepository extends BaseRepository implements HeaderSettingRepositoryContract
{
    protected $table = 'headers';

    public function updateSettings(array $data)
    {
        // Clear all previous settings
        $this->destroyAll();

        // Save logo if provided
        if (isset($data['header_logo'])) {
            $this->create([
                'setting' => 'header_logo',
                'value' => $data['header_logo'],
                'is_array' => 0 // Explicitly use 0 for non-array values
            ]);
        }

        // Save background color if provided
        if (isset($data['header_background'])) {
            $this->create([
                'setting' => 'header_background',
                'value' => $data['header_background'],
                'is_array' => 0 // Explicitly use 0 for non-array values
            ]);
        }

        // Process and save header items (buttons) if provided
        if (isset($data['header_items']) && is_array($data['header_items'])) {
            foreach ($data['header_items'] as $item) {
                $this->create([
                    'setting' => 'header_item',
                    'value' => json_encode($item),  // Encode button data as JSON
                    'is_array' => 1 // Explicitly use 1 for array values (JSON-encoded data)
                ]);
            }
        }

        return true;
    }
}