<?php

class ClientController
{
    public function getAllClients($filter = ['offset' => 0, 'limit' => 1000])
    {
        $modelFilter = $filter;
        unset($modelFilter['offset'], $modelFilter['limit'], $modelFilter['slug']);
        $cModel = new ClientModel();
        $response = $cModel->getByFilter($modelFilter);
        return array_slice($response, $filter['offset'], $filter['limit']);
    }
}
