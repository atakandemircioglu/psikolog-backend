<?php

class TherapistModel extends Model
{
    protected static $tableName = "Psikolog Başvuru Formu";
    protected $primaryKey = "id";
    protected $fillables = [
        'isim',
        'telefonNumarasi',
        'eposta',
        'unvani',
        'uzmanlikAlani',
        'musaitlikTarihi',
        'webSitesi',
        'cv',
        'fotograf',
        'appointmentForm'
    ];
}
