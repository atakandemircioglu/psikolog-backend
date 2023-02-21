<?php

class StatController
{
    public function getStats()
    {
        $content = [
            'toplam_danisan' => 1,
            'tedavisi_baslayan' => 2,
            'tedavi_bekleyen' => 1,
            'teavisi_tamamlanan' => 54,
            'onay_bekleyen' => 12,
            'onaylanan' => 123,
            'toplam_psikolog' => 1,
            'gunluk_tamamlanan_randevu' => 12
        ];

        $therapistController = new TherapistController();
        $all = $therapistController->getAllTherapists();

        echo "<pre>";
        var_dump($all);
        die;
    }
}
