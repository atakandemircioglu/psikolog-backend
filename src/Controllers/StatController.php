<?php

class StatController
{
    public function getStats()
    {
        //$therapistController = new TherapistController();
        $clientController = new ClientController();

        echo "<pre>";
        var_dump($clientController->getAllClients());
        die;

        // $content = [
        //     'toplam_danisan' => 0,
        //     'tedavisi_baslayan' => 0,
        //     'tedavi_bekleyen' => 0,
        //     'teavisi_tamamlanan' => 0,
        //     'onay_bekleyen' => 0,
        //     'onaylanan' => 0,
        //     'toplam_psikolog' => count($therapistController->getAllTherapists()),
        //     'gunluk_tamamlanan_randevu' => 0
        // ];


    }
}
