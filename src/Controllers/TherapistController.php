<?php

class TherapistController
{
    public function getAllTherapists($filter = ['offset' => 0, 'limit' => 1000])
    {
        $modelFilter = $filter;
        unset($modelFilter['offset'], $modelFilter['limit'], $modelFilter['slug']);
        $therapistModel = new TherapistModel();
        $allTherapists = $therapistModel->getByFilter($modelFilter);
        return array_slice($allTherapists, $filter['offset'], $filter['limit']);
    }

    public function onTherapistRegister($request)
    {
        $therapistID = $request['submissionID'];
        $rawData = json_decode($request['rawRequest'], true);
        $therapistName = $rawData['q3_isim']['first'] . ' ' . $rawData['q3_isim']['last'];
        $questions = $this->getAppointmentFormQuestionsDefault();

        $properties = $this->getAppointmentFormPropertiesDefault();
        $properties['title'] = $therapistName . '_' . $therapistID;
        $questions["questions"][1]['text'] = $therapistName . ' Randevu Formu';

        $appointmentForm = $this->createAppointmentForm($properties, $questions);
        $therapist = (new TherapistModel())->findByPrimaryKey($therapistID);
        $therapist->appointmentForm = 'https://www.jotform.com/' . $appointmentForm[0]['id'];
        $therapist->save();
        return $appointmentForm;
    }

    public function createAppointmentForm($properties = [], $questions = [])
    {
        $form = [
            'properties' => $properties
        ];

        $form = SheetDB::api()->createForm($form);
        $questionedForm = SheetDB::api()->createFormQuestions($form['id'], json_encode($questions));
        return [$form, $questionedForm];
    }

    public function getAppointmentFormPropertiesDefault()
    {
        return json_decode(file_get_contents('appointment-form-properties.json', true), true);
    }

    public function getAppointmentFormQuestionsDefault()
    {
        return json_decode(file_get_contents('appointment-form-questions.json', true), true);
    }
}
