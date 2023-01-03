<?php

namespace App\Imports;

use App\Models\Voter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotersImport implements ToModel, WithHeadingRow, WithChunkReading
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Voter([
            'student_number' => $row['student_number'],
            'password' => $row['password'],
            'email' => $row['email'],
            'mobile' => $row['mobile'],
            'college' => $row['college'],
            'has_password_request' => true,
            'slug' => $row['student_number'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
