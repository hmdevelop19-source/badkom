<?php

namespace App\Enums;

enum UtdStatusEnum: string
{
    case Aktif = 'Aktif';
    case Dimutasi = 'Dimutasi';
    case Ditarik = 'Ditarik';
    case Selesai = 'Selesai';
}
