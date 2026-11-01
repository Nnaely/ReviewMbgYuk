<?php

namespace Database\Seeders;

use App\Models\Distribution;
use App\Models\Report;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reporter = User::where('email', 'pelapor@mbg.test')->firstOrFail();

        $examples = [
            [
                'school' => 'SDN 035 Soka',
                'description' => 'Contoh simulasi: catatan pemeriksaan menu untuk demonstrasi, bukan laporan kejadian nyata.',
                'status' => Report::STATUS_BARU,
            ],
            [
                'school' => 'SMP Negeri 2 Bandung',
                'description' => 'Contoh simulasi: laporan pemeriksaan distribusi untuk demonstrasi, bukan laporan kejadian nyata.',
                'status' => Report::STATUS_DIPROSES,
            ],
            [
                'school' => 'SMAN 3 Bandung',
                'description' => 'Contoh simulasi: catatan tindak lanjut distribusi untuk demonstrasi, bukan laporan kejadian nyata.',
                'status' => Report::STATUS_SELESAI,
            ],
        ];

        foreach ($examples as $example) {
            $school = School::where('name', $example['school'])->firstOrFail();
            $distribution = Distribution::query()
                ->where('school_id', $school->id)
                ->orderBy('distribution_date')
                ->firstOrFail();

            $report = Report::firstOrCreate(
                [
                    'reporter_id' => $reporter->id,
                    'description' => $example['description'],
                ],
                [
                    'school_id' => $school->id,
                    'distribution_id' => $distribution->id,
                ]
            );

            if (! $report->statusHistories()->exists()) {
                $report->statusHistories()->create([
                    'updated_by' => $reporter->id,
                    'status' => $report->status ?: Report::STATUS_BARU,
                    'note' => 'Laporan dibuat oleh pelapor.',
                ]);
            }

            if ($example['status'] === Report::STATUS_DIPROSES && $report->status === Report::STATUS_BARU) {
                $report->update([
                    'status' => Report::STATUS_DIPROSES,
                    'follow_up_note' => 'Contoh simulasi: laporan sedang diperiksa.',
                ]);
            }

            if ($example['status'] === Report::STATUS_SELESAI) {
                if ($report->status === Report::STATUS_BARU) {
                    $report->update([
                        'status' => Report::STATUS_DIPROSES,
                        'follow_up_note' => 'Contoh simulasi: laporan sedang diperiksa.',
                    ]);
                }

                if ($report->status === Report::STATUS_DIPROSES) {
                    $report->update([
                        'status' => Report::STATUS_SELESAI,
                        'follow_up_note' => 'Contoh simulasi: tindak lanjut telah dicatat.',
                    ]);
                }
            }
        }
    }
}
