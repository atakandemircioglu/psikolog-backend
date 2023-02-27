<?php

class ClientController
{
    public function getAllClients($filter = ['offset' => 0, 'limit' => 1000])
    {
        $modelFilter = $filter;
        unset($modelFilter['offset'], $modelFilter['limit'], $modelFilter['slug']);

        /* if ($_SESSION['role'] !== "ADMIN") {
            $modelFilter["atananPsikologGroup"] = $_SESSION["group"];
        } */

        $cModel = new ClientModel();
        $response = $cModel->getByFilter($modelFilter);

        if ($_SESSION['role'] !== 'ADMIN') {
            $response = array_filter($this->manageRelations($response), function ($client) {
                return $client["atananPsikologGroup"] === $_SESSION["group"];
            });
        }

        return array_slice($response, $filter['offset'], $filter['limit']);
    }

    private function manageRelations($clients) {
        foreach ($clients as &$eachClient) {
            $therapistID = str_replace(['{','}'], '', $eachClient['atananPsikolog']);
            $status = str_replace(['{','}'], '', $eachClient['durum']);
            $therapist = (new TherapistModel())->findByPrimaryKey($therapistID); // Cache is awesome in this case!
            if ($therapist) {
                $eachClient['atananPsikolog'] = $therapist->isim['first'] . ' ' . $therapist->isim['last'];
                $eachClient['atananPsikologGroup'] = $therapist->group;
                $eachClient['emailPsik'] = $therapist->eposta;
            }
            $eachClient['durum'] = $this->matchStatusId($status);
        }
        return $clients;
    }

    private function matchStatusId($id) {
        switch ($id) {
            case "35n0cxvtwbi":
                return "Onay Bekliyor";
            case "0o3gjpct8ats":
                return "Tedavisi Başladı";
            case "6yb36rvy42e":
                return "Tedavisi Bitti";
            case "ve0xdgmu5om":
                return "İptal Edildi";
        }
    }
}
