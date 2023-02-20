<?php

class ClientModel extends Model
{
    protected static $tableID = "230433775830052";
    protected $primaryKey = "id";
    protected $fillables = [
        'isim',
        'telefonNumarasi',
        'eposta',
        'adres',
        'cinsiyet',
        'yas',
        'dahaOnceDestek',
        'dahaOnceTani',
        'destekAlmak',
        'musaitZamanlar',
        'kullandigiIlaclar',
        'eklemekIstediginiz',
        'eklemekIstedikleriniz',
        'atananPsikolog',
        'emailPsik',
        'durum'
    ];

    public function findByEposta($eposta) {
        return $this->where('eposta', $eposta)->get();
    }
}
