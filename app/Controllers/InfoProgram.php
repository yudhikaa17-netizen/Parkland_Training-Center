<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiteSettingModel;

class InfoProgram extends BaseController
{
    public function index()
    {
        $settingModel = new SiteSettingModel();

        $defaultLocations = [
            [
                'title' => 'Factory 1 – 5',
                'dot_color' => 'var(--blue)',
                'items' => [
                    ['label' => 'Cell', 'value' => '1 – 22'],
                    ['label' => 'Proses per cell', 'value' => 'Cutting, Sewing, Assembly'],
                ]
            ],
            [
                'title' => 'Inhouse',
                'dot_color' => 'var(--amber)',
                'items' => [
                    ['label' => 'Nosew Line', 'value' => '1 – 60'],
                    ['label' => 'Screen Table Line', 'value' => '1 – 15'],
                    ['label' => 'Cutting', 'value' => '1 – 30'],
                    ['label' => 'HF Welding', 'value' => '1 – 45'],
                ]
            ],
            [
                'title' => 'Bottom 1 & Bottom 2',
                'dot_color' => 'var(--green)',
                'items' => [
                    ['label' => 'Press', 'value' => '1 – 30'],
                    ['label' => 'Stockfit', 'value' => '1 – 5'],
                    ['label' => 'Degreasing', 'value' => '1 – 2'],
                    ['label' => 'UV', 'value' => '1 – 2'],
                ]
            ]
        ];

        $locations = $settingModel->getSetting('production_locations', $defaultLocations);

        $data = [
            'title'              => 'Info Program Training',
            'current_role'       => session()->get('role'),
            'location_structure' => $locations,
        ];

        return view('info_program/index', $data);
    }
}
