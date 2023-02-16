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
        $therapistName = $rawData['isim']['first'] . ' ' . $rawData['isim']['last'];
        $appointmentForm = $this->createAppointmentForm(['properties' => [
            'title' => $therapistName . '_' . $therapistID
        ]]);
        $therapist = (new TherapistModel())->findByPrimaryKey($therapistID);
        $therapist->appointmentForm = $appointmentForm[0]['id'];
        file_put_contents(__DIR__ . '/log/test2.json', json_encode($therapist));
        $therapist->save();
        return $appointmentForm;
    }

    public function createAppointmentForm($opt = [])
    {
        $questions = $this->getAppointmentFormQuestionsDefault();
        $properties = $this->getAppointmentFormPropertiesDefault();
        $properties['title'] = 'Psikolog Randevu Formu';

        foreach ($opt as $var => $value) {
            if (in_array($var, ['properties', 'questions'])) {
                foreach ($value as $key => $value) {
                    ${$var}[$key] = $value;
                }
            }
        }

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
