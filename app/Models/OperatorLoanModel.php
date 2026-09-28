<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorLoanModel extends Model
{
    protected $table            = 'operator_loans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'trainee_id',
        'factory',
        'borrowing_department',
        'person_in_charge',
        'line_name',
        'start_date',
        'end_date',
        'duration_days',
        'status',
        'purpose',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLoansWithTrainee($date = null)
    {
        $builder = $this->select('operator_loans.*, trainees.name as trainee_name, trainees.nik, trainees.track, trainees.stage')
            ->join('trainees', 'trainees.id = operator_loans.trainee_id')
            ->orderBy('operator_loans.id', 'DESC');

        if (!empty($date)) {
            $builder->groupStart()
                ->where('operator_loans.start_date <=', $date)
                ->groupStart()
                    ->where('operator_loans.end_date >=', $date)
                    ->orWhere('operator_loans.end_date IS NULL')
                    ->orWhere('operator_loans.end_date', '')
                    ->orWhere('operator_loans.end_date', '0000-00-00')
                ->groupEnd()
            ->orWhere('operator_loans.start_date', $date)
            ->groupEnd();
        }

        return $builder->findAll();
    }

    public function getFactoryStats($date = null, array $customFactories = [])
    {
        if (empty($customFactories)) {
            $settingModel = new \App\Models\SiteSettingModel();
            $customFactories = $settingModel->getSetting('factory_list', [
                'Factory 1 (F1)',
                'Factory 2 (F2)',
                'Factory 3 (F3)',
                'Factory 4 (F4)',
                'Factory 5 (F5)',
                'Bottom 1',
                'Bottom 2',
                'Inhouse',
            ]);
        }

        if (!is_array($customFactories) || empty($customFactories)) {
            $customFactories = [
                'Factory 1 (F1)',
                'Factory 2 (F2)',
                'Factory 3 (F3)',
                'Factory 4 (F4)',
                'Factory 5 (F5)',
                'Bottom 1',
                'Bottom 2',
                'Inhouse',
            ];
        }

        $builder = $this;
        if (!empty($date)) {
            $builder = $this->groupStart()
                ->where('start_date <=', $date)
                ->groupStart()
                    ->where('end_date >=', $date)
                    ->orWhere('end_date IS NULL')
                    ->orWhere('end_date', '')
                    ->orWhere('end_date', '0000-00-00')
                ->groupEnd()
            ->orWhere('start_date', $date)
            ->groupEnd();
        }

        $loans = $builder->findAll();

        $colorPalette = [
            'var(--blue)',
            'var(--blue)',
            '#c084fc',
            '#c084fc',
            '#c084fc',
            'var(--teal)',
            'var(--teal)',
            'var(--violet)',
            'var(--amber)',
            '#ec4899',
            '#06b6d4',
            '#f59e0b',
            '#8b5cf6',
            '#10b981',
            '#6366f1',
        ];

        $factoryCounts = [];
        $fIdx = 0;
        foreach ($customFactories as $facName) {
            $trimmedName = trim($facName);
            if (empty($trimmedName)) continue;
            
            $factoryCounts[$trimmedName] = [
                'name'        => $trimmedName,
                'active'      => 0,
                'completed'   => 0,
                'color'       => $colorPalette[$fIdx % count($colorPalette)],
                'description' => 'Dipinjam di ' . $trimmedName,
            ];
            $fIdx++;
        }

        $stats = [
            'f1_active'       => 0,
            'f2_active'       => 0,
            'f3_active'       => 0,
            'f4_active'       => 0,
            'f5_active'       => 0,
            'b1_active'       => 0,
            'b2_active'       => 0,
            'inhouse_active'  => 0,
            'total_active'    => 0,
            'total_completed' => 0,
            'factory_counts'  => $factoryCounts,
        ];

        foreach ($loans as $l) {
            $loanFactory = trim($l['factory'] ?? '');
            $fUpper = strtoupper($loanFactory);
            $lineUpper = strtoupper($l['line_name'] ?? '');
            $isActive = ($l['status'] === 'Dipinjam' || $l['status'] === 'Disetujui');
            $isCompleted = ($l['status'] === 'Selesai');

            if ($isActive) {
                $stats['total_active']++;
            } elseif ($isCompleted) {
                $stats['total_completed']++;
            }

            // Match against configured factories
            $matchedKey = null;
            foreach ($stats['factory_counts'] as $facKey => $facData) {
                $facUpper = strtoupper($facKey);
                preg_match('/\((.*?)\)/', $facKey, $matches);
                $shortCode = isset($matches[1]) ? strtoupper($matches[1]) : '';

                if (
                    $fUpper === $facUpper ||
                    ($shortCode !== '' && (str_contains($fUpper, $shortCode) || str_contains($lineUpper, $shortCode))) ||
                    str_contains($fUpper, $facUpper) ||
                    str_contains($facUpper, $fUpper)
                ) {
                    $matchedKey = $facKey;
                    break;
                }
            }

            if ($matchedKey !== null) {
                if ($isActive) {
                    $stats['factory_counts'][$matchedKey]['active']++;
                } elseif ($isCompleted) {
                    $stats['factory_counts'][$matchedKey]['completed']++;
                }
            } else {
                if (!empty($loanFactory)) {
                    if (!isset($stats['factory_counts'][$loanFactory])) {
                        $idx = count($stats['factory_counts']);
                        $stats['factory_counts'][$loanFactory] = [
                            'name'        => $loanFactory,
                            'active'      => 0,
                            'completed'   => 0,
                            'color'       => $colorPalette[$idx % count($colorPalette)],
                            'description' => 'Dipinjam di ' . $loanFactory,
                        ];
                    }
                    if ($isActive) {
                        $stats['factory_counts'][$loanFactory]['active']++;
                    } elseif ($isCompleted) {
                        $stats['factory_counts'][$loanFactory]['completed']++;
                    }
                }
            }

            // Maintain legacy keys for backwards-compatibility
            $isF1 = str_contains($fUpper, 'F1') || str_contains($fUpper, 'FACTORY 1') || str_contains($lineUpper, 'F1');
            $isF2 = str_contains($fUpper, 'F2') || str_contains($fUpper, 'FACTORY 2') || str_contains($lineUpper, 'F2');
            $isF3 = str_contains($fUpper, 'F3') || str_contains($fUpper, 'FACTORY 3') || str_contains($lineUpper, 'F3');
            $isF4 = str_contains($fUpper, 'F4') || str_contains($fUpper, 'FACTORY 4') || str_contains($lineUpper, 'F4');
            $isF5 = str_contains($fUpper, 'F5') || str_contains($fUpper, 'FACTORY 5') || str_contains($lineUpper, 'F5');
            $isB1 = str_contains($fUpper, 'BOTTOM 1') || str_contains($lineUpper, 'BOTTOM 1') || str_contains($fUpper, 'B1');
            $isB2 = str_contains($fUpper, 'BOTTOM 2') || str_contains($lineUpper, 'BOTTOM 2') || str_contains($fUpper, 'B2');
            $isInhouse = str_contains($fUpper, 'INHOUSE') || str_contains($lineUpper, 'INHOUSE');

            if ($isActive) {
                if ($isF1) $stats['f1_active']++;
                elseif ($isF2) $stats['f2_active']++;
                elseif ($isF3) $stats['f3_active']++;
                elseif ($isF4) $stats['f4_active']++;
                elseif ($isF5) $stats['f5_active']++;
                elseif ($isB1) $stats['b1_active']++;
                elseif ($isB2) $stats['b2_active']++;
                elseif ($isInhouse) $stats['inhouse_active']++;
                else $stats['f1_active']++;
            }
        }

        return $stats;
    }
}
