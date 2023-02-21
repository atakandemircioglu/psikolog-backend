<?php

class StatController
{
    public function getStats()
    {
        $therapistController = new TherapistController();
        $clientController = new ClientController();

        $clients = $clientController->getAllClients();

        $content = [
            'toplam_danisan' => count($clients),
            'tedavi_baslayan' => 0,
            'tedavi_bekleyen' => 0,
            'tedavi_tamamlanan' => 0,
            'tedavi_iptal' => 0,
            'onay_bekleyen' => 0,
            'toplam_psikolog' => count($therapistController->getAllTherapists())
        ];

        foreach ($clients as $eachClient) {
            switch ($eachClient['durum']) {
                case 'Onay Bekliyor':
                    $content['tedavi_bekleyen']++;
                    break;
                case 'Tedavisi Bitti':
                    $content['tedavi_tamamlanan']++;
                    break;
                case 'Tedavisi Başladı':
                    $content['tedavi_baslayan']++;
                    break;
                case 'İptal Edildi':
                    $content['tedavi_iptal']++;
                    break;
                default:
                    $content['tedavi_bekleyen']++;
                    break;
            }
        }

        return $content;
    }
}
