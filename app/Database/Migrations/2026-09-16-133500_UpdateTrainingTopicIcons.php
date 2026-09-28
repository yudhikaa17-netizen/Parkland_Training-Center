<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTrainingTopicIcons extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $iconMap = [
            'benang_jahit'   => 'bi bi-bezier2',
            'jarum_jahit'    => 'bi bi-pin-angle-fill',
            'no_sew'         => 'bi bi-fire',
            'assembly'       => 'bi bi-gear-wide-connected',
            'sewing_machine' => 'bi bi-tools',
            'cutting_proses' => 'bi bi-scissors',
            'cutting_tools'  => 'bi bi-hammer',
            'inhouse'        => 'bi bi-mortarboard-fill',
            'material_kulit' => 'bi bi-layers-fill',
            'skill_level'    => 'bi bi-award-fill',
        ];

        foreach ($iconMap as $key => $icon) {
            $db->table('training_topics')
                ->where('topic_key', $key)
                ->update(['icon' => $icon]);
        }

        $db->query("UPDATE training_topics SET icon = 'bi bi-file-earmark-text' WHERE icon LIKE '%?%' OR icon = '' OR icon IS NULL");
    }

    public function down()
    {
    }
}
