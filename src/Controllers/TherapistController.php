<?php

class TherapistController
{
    public function getAllTherapists($filter = ['offset' => 0, 'limit' => 1000])
    {
        $modelFilter = $filter;
        unset($modelFilter['offset'], $modelFilter['limit'], $modelFilter['slug']);

        if ($_SESSION['role'] !== "ADMIN") {
            $modelFilter["group"] = $_SESSION["group"];
        }

        $therapistModel = new TherapistModel();
        $allTherapists = $therapistModel->getByFilter($modelFilter);
        return array_slice($allTherapists, $filter['offset'], $filter['limit']);
    }

    public function getTherapistById($id) {
        $therapist = (new TherapistModel())->findByPrimaryKeyOrFail($id);
        if ($_SESSION['role'] !== 'ADMIN' && $_SESSION["group"] !== $therapist->group) {
            return "Unauthorized";
        }

        return $therapist->toArray();
    }

    public function onTherapistRegister($request)
    {
        $therapistID = $request['submissionID'];
        $rawData = json_decode($request['rawRequest'], true);
        $therapistName = $rawData['q3_isim']['first'] . ' ' . $rawData['q3_isim']['last'];
        $questions = $this->getAppointmentFormQuestionsDefault();

        $properties = $this->getAppointmentFormPropertiesDefault();
        $properties['title'] = $therapistName . " Randevu Formu";
        $questions["questions"][1]['text'] = $therapistName . ' Randevu Formu';
        $appointmentForm = $this->createAppointmentForm($properties, $questions);
        $therapist = (new TherapistModel())->findByPrimaryKey($therapistID);
        $additionalProperties = [ // this properties are buggy.
            "properties" => [
                "emails" => [
                    [
                        "body" => "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n                \n                \n                <meta name=\"viewport\" content=\"width=device-width\">\n                <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n                <meta name=\"x-apple-disable-message-reformatting\">\n                <style>\n                    table {\n                        border-spacing: 0 !important;\n                        border-collapse: collapse !important;\n                        margin: 0 auto !important;\n                    }\n                    table, td {\n                        border-spacing: 0 !important;\n                        border-collapse:collapse !important;\n                    }\n                    @media screen and (max-device-width: 700px), screen and (max-width: 700px) {\n                        .email-container {\n                            width: 100% !important;\n                            margin: auto !important;\n                        }\n                        .mobile-padding-fix {\n                            padding-left: 15px !important;\n                            padding-right: 15px !important;\n                        }\n                    }\n                    @media screen and (max-device-width: 480px), screen and (max-width: 480px) {\n                        td.questionColumn, td.valueColumn, td.footerCol1, td.footerCol2 {\n                            display: block !important;\n                            width: 100% !important;\n                        }\n                        td.questionColumn {\n                            padding: 0 !important;\n                        }\n                        td.valueColumn {\n                            padding: 4px 0 16px !important;\n                        }\n                        td.footerCol1, td.footerCol2 {\n                            text-align: center !important;\n                        }\n                        td.footerCol1 {\n                            padding-bottom: 16px\n                        }\n                    }\n                </style>\n            \n            </head><body width=\"100%\" style=\"width: 100%; margin: 0 auto !important; padding: 0 !important; mso-line-height-rule: exactly; background-color: #F3F3FE;\">\n                <table border=\"0\" width=\"720\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F3F3FE\" style=\"width: 720px; text-align: center;\" class=\"email-container body-bg\">\n                <tbody>\n                    <tr>\n                        <td height=\"36\">&#xA0;</td>\n                    </tr>\n                    <tr>\n                        <td align=\"center\" class=\"mobile-padding-fix\" style=\"padding: 0 40px;\">\n                            <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F3F3FE\" style=\"width: 100%;\">\n                    <tbody>\n                        <tr>\n                            <td height=\"40\" align=\"center\">\n                                <img height=\"40\" style=\"display: block; height: 40px;\" src=\"https://cdn.jotfor.ms/assets/img/logo2021/jotform-logo.png\" alt=\"Jotform Logo\">\n                            </td>\n                        </tr>\n                        <tr>\n                            <td height=\"28\">&#xA0;</td>\n                        </tr>\n                        <tr>\n                            <td height=\"8\" bgcolor=\"#FF6100\" style=\"line-height: 0; font-size: 0; border-radius: 4px 4px 0 0;\">&#xA0;</td>\n                        </tr>\n                        <tr>\n                            <td><table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\" style=\"width: 100%;\">\n            <tr>\n                <td colspan=\"3\" height=\"16\" style=\"line-height: 0; font-size: 0;\">&#xA0;</td>\n            </tr>\n            <tr>\n                <td width=\"36\">&#xA0;</td>\n                <td valign=\"middle\" align=\"left\">\n                    <h3 style=\"display: inline-block; vertical-align: middle; margin: 0; font-size: 18px; font-weight: 700; color: #0A1551; font-family: Helvetica, sans-serif;\">{form_title}</h3>\n                </td>\n                <td width=\"36\">&#xA0;</td>\n            </tr>\n            <tr>\n                <td width=\"36\">&#xA0;</td>\n                <td height=\"16\" style=\"border-bottom: 1px solid #ECEDF2; line-height: 0; font-size: 0;\">&#xA0;</td>\n                <td width=\"36\">&#xA0;</td>\n            </tr>\n        </table></td>\n                        </tr>\n                    </tbody>\n                </table>\n                            <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\" style=\"width: 100%; border-radius: 0 0 4px 4px;\">\n                                <tbody>\n                                    <tr>\n                                      <td colspan=\"3\" height=\"24\">&#xA0;</td>\n                                    </tr>\n                                    <tr>\n                                        <td width=\"36\">&#xA0;</td>\n                                        <td align=\"center\">\n                                            <table id=\"emailFieldsTable\" class=\"mceNonEditable\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" style=\"font-size: 14px; font-family:Helvetica, sans-serif;\">\n<tbody id=\"emailFieldsTableBody\"><tr id=\"row_4\" class=\"questionRow\"><td id=\"question_4\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6F76A7;\" valign=\"top\" width=\"30%\">&#x130;sim</td><td id=\"value_4\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0A1551; font-weight: 500;\" width=\"70%\">{isim}</td></tr><tr id=\"row_8\" class=\"questionRow\"><td id=\"question_8\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6F76A7;\" valign=\"top\" width=\"30%\">Hasta E-Posta</td><td id=\"value_8\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0A1551; font-weight: 500;\" width=\"70%\">{hastaEposta}</td></tr><tr id=\"row_29\" class=\"questionRow\"><td id=\"question_29\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6F76A7;\" valign=\"top\" width=\"30%\">Dan&#x131;&#x15F;an</td><td id=\"value_29\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0A1551; font-weight: 500;\" width=\"70%\">{typeA}</td></tr><tr id=\"row_9\" class=\"questionRow\"><td id=\"question_9\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6F76A7;\" valign=\"top\" width=\"30%\">Hasta &#x130;smi</td><td id=\"value_9\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0A1551; font-weight: 500;\" width=\"70%\">{hastaIsmi}</td></tr><tr id=\"row_25\" class=\"questionRow\"><td id=\"question_25\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6F76A7;\" valign=\"top\" width=\"30%\">G&#xF6;r&#xFC;&#x15F;me Ba&#x11F;lant&#x131;s&#x131;</td><td id=\"value_25\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0A1551; font-weight: 500;\" width=\"70%\">{gorusmeBaglantisi}</td></tr><tr id=\"row_3\" class=\"questionRow\"><td id=\"question_3\" class=\"questionColumn\" style=\"padding: 12px 8px 12px 0; color: #6f76a7;\" valign=\"top\" width=\"30%\">Randevu</td><td id=\"value_3\" class=\"valueColumn\" valign=\"top\" style=\"padding: 12px 0; color: #0a1551; font-weight: 500;\" width=\"70%\">\n                        {randevu}\n                    </td></tr></tbody>\n                            </table>\n                                        </td>\n                                        <td width=\"36\">&#xA0;</td>\n                                    </tr>\n                                    <tr>\n                                        <td colspan=\"3\" height=\"24\">&#xA0;</td>\n                                    </tr>\n                                </tbody>\n                            </table>        </td>\n                    </tr>\n                    <tr>\n                        <td align=\"center\" class=\"mobile-padding-fix\" style=\"padding: 0 40px;\">\n                            <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\" style=\"font-family:Helvetica, sans-serif; border-radius: 0 0 4px 4px;\">\n                                <tr>\n                                    <td width=\"36\">&#xA0;</td>\n                                    <td height=\"20\" style=\"border-top: 1px solid #ECEDF2;\">&#xA0;</td>\n                                    <td width=\"36\">&#xA0;</td>\n                                </tr>\n                                <tr>\n                                    <td width=\"36\">&#xA0;</td>\n                                    <td class=\"forward-text-show\" style=\"display: block; font-size: 13px; text-align: center; color: #0a1551;\">\n                                        You can <span style=\"color: #4573E3;\">{edit_submission}</span> and <span style=\"color: #4573E3;\">{all_submissions}</span> easily.\n                                    </td>\n                                    <td width=\"36\">&#xA0;</td>\n                                </tr>\n                                <tr>\n                                    <td colspan=\"3\" height=\"20\">&#xA0;</td>\n                                </tr>\n                            </table>        </td>\n                    </tr>\n                    <tr>\n                        <td height=\"30\">&#xA0;</td>\n                    </tr>\n                </tbody>\n            </table>\n        \n    </body></html>\n",
                        "branding21Email" => "1",
                        "dirty" => "",
                        "from" => "{danisanAdi}",
                        "hideEmptyFields" => "1",
                        "html" => "1",
                        "lastQuestionID" => "5",
                        "name" => "Notification 1",
                        "pdfattachment" => "",
                        "replyTo" => "none",
                        "sendOnEdit" => "1",
                        "sendOnSubmit" => "1",
                        "subject" => "Re: {form_title} - {danisanAdi}",
                        "to" => $therapist->eposta,
                        "type" => "notification",
                        "uniqueID" => "230444196945058",
                        "uploadAttachment" => ""
                    ],
                    [
                        "attachment" => "",
                        "body" => "html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n                \n                \n                <meta name=\"viewport\" content=\"width=device-width\">\n                <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n                <meta name=\"x-apple-disable-message-reformatting\">\n                <style>\n                    table {\n                        border-spacing: 0 !important;\n                        border-collapse: collapse !important;\n                        margin: 0 auto !important;\n                    }\n                    table, td {\n                        border-spacing: 0 !important;\n                        border-collapse:collapse !important;\n                    }\n                    @media screen and (max-device-width: 700px), screen and (max-width: 700px) {\n                        .email-container {\n                            width: 100% !important;\n                            margin: auto !important;\n                        }\n                        .mobile-padding-fix {\n                            padding-left: 15px !important;\n                            padding-right: 15px !important;\n                        }\n                    }\n                    @media screen and (max-device-width: 480px), screen and (max-width: 480px) {\n                        td.questionColumn, td.valueColumn, td.footerCol1, td.footerCol2 {\n                            display: block !important;\n                            width: 100% !important;\n                        }\n                        td.questionColumn {\n                            padding: 0 !important;\n                        }\n                        td.valueColumn {\n                            padding: 4px 0 16px !important;\n                        }\n                        td.footerCol1, td.footerCol2 {\n                            text-align: center !important;\n                        }\n                        td.footerCol1 {\n                            padding-bottom: 16px\n                        }\n                    }\n                </style>\n            \n            </head><body width=\"100%\" style=\"width: 100%; margin: 0 auto !important; padding: 0 !important; mso-line-height-rule: exactly; background-color: #F3F3FE;\">\n<table class=\"email-container body-bg\" style=\"width: 720px; text-align: center;\" border=\"0\" width=\"720\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F3F3FE\">\n<tbody>\n<tr>\n<td height=\"36\">&#xA0;</td>\n</tr>\n<tr>\n<td class=\"mobile-padding-fix\" style=\"padding: 0 40px;\" align=\"center\">\n<table style=\"width: 100%;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\">\n<tbody>\n<tr>\n<td style=\"line-height: 0; font-size: 0; border-radius: 4px 4px 0 0;\" colspan=\"3\" bgcolor=\"#FF6100\" height=\"8\">&#xA0;</td>\n</tr>\n<tr>\n<td width=\"36\">&#xA0;</td>\n<td>\n<table style=\"width: 100%; border-bottom: 1px solid #ECEDF2;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\">\n<tbody>\n<tr>\n<td style=\"line-height: 0; font-size: 0;\" colspan=\"2\" height=\"16\">&#xA0;</td>\n</tr>\n<tr>\n<td align=\"left\" valign=\"top\" width=\"40\"><img style=\"display: block; height: 40px;\" src=\"https://cdn.jotfor.ms/assets/img/logo2021/jotform-logo@144x144.png\" alt=\"Jotform Logo\" height=\"40\"></td>\n<td align=\"left\" valign=\"middle\">\n<h3 style=\"display: inline-block; vertical-align: middle; margin: 0 0 0 8px; font-size: 18px; font-weight: bold; color: #0a1551; font-family: Helvetica, sans-serif;\">{form_title}</h3>\n</td>\n</tr>\n<tr>\n<td style=\"line-height: 0; font-size: 0;\" colspan=\"2\" height=\"16\">&#xA0;</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td width=\"36\">&#xA0;</td>\n</tr>\n</tbody>\n</table>\n<table style=\"width: 100%; border-radius: 0 0 4px 4px;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\">\n<tbody>\n<tr>\n<td style=\"text-align: center;\" colspan=\"3\" height=\"24\">\n<p>&#xA0;Merhaba&#xA0;{hastaIsmi},</p>\n<p>Afet Psikososyal Destek Platformu arac&#x131;l&#x131;&#x11F;&#x131;yla olu&#x15F;turulan randevu tarihiniz, a&#x15F;a&#x11F;&#x131;daki gibidir.</p>\n</td>\n</tr>\n<tr>\n<td width=\"36\">&#xA0;</td>\n<td align=\"center\">\n<table id=\"emailFieldsTable\" class=\"mceNonEditable\" style=\"font-size: 14px; font-family: Helvetica, sans-serif;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">\n<tbody id=\"emailFieldsTableBody\"><tr id=\"row_29\" class=\"questionRow\"><td id=\"question_29\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6F76A7;\" valign=\"top\" width=\"30%\">Dan&#x131;&#x15F;an</td><td id=\"value_29\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0A1551; font-weight: 500;\" width=\"70%\">{typeA}</td></tr><tr id=\"row_25\" class=\"questionRow\"><td id=\"question_25\" class=\"questionColumn\" style=\"padding: 12px 4px 12px 0; color: #6f76a7;\" valign=\"top\" width=\"30%\">G&#xF6;r&#xFC;&#x15F;me Ba&#x11F;lant&#x131;s&#x131;</td><td id=\"value_25\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0a1551; font-weight: 500;\" width=\"70%\">{gorusmeBaglantisi}</td></tr><tr id=\"row_3\" class=\"questionRow\"><td id=\"question_3\" class=\"questionColumn\" style=\"padding: 12px 8px 12px 0; color: #6f76a7;\" valign=\"top\" width=\"30%\">Randevu</td><td id=\"value_3\" class=\"valueColumn\" style=\"padding: 12px 0; color: #0a1551; font-weight: 500;\" valign=\"top\" width=\"70%\">{randevu}</td></tr></tbody>\n</table>\n</td>\n<td width=\"36\">&#xA0;</td>\n</tr>\n<tr>\n<td colspan=\"3\" height=\"24\">&#xA0;</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n<tr>\n<td height=\"30\">&#xA0;</td>\n</tr>\n</tbody>\n</table>\n</body></html>\n",
                        "branding21Email" => "1",
                        "dirty" => "",
                        "from" => $therapistName,
                        "hideEmptyFields" => "1",
                        "html" => "1",
                        "lastQuestionID" => "5",
                        "name" => "Autoresponder 1",
                        "pdfattachment" => "",
                        "replyTo" => $therapist->eposta,
                        "sendDate" => "",
                        "sendOnEdit" => "",
                        "sendOnSubmit" => "1",
                        "subject" => "Randevunuz Ayarlanmıştır {form_title}",
                        "to" => "{hastaEposta}",
                        "type" => "autorespond",
                        "uniqueID" => "230444654805052"
                    ]
                ],
                "conditions" => [
                    [
                        "action" => "[{\"replaceText\":\"\",\"readOnly\":false,\"newCalculationType\":true,\"useCommasForDecimals\":false,\"operands\":\"24\",\"equation\":\"{24}\",\"showBeforeInput\":false,\"showEmptyDecimals\":false,\"ignoreHiddenFields\":false,\"insertAsText\":false,\"id\":\"action_0_1676568222687\",\"resultField\":\"8\",\"decimalPlaces\":\"2\",\"isError\":false}]",
                        "id" => "1676568219701",
                        "index" => "0",
                        "link" => "Any",
                        "priority" => "0",
                        "terms" => "[{\"id\":\"term_0_1676568222687\",\"field\":\"24\",\"operator\":\"isFilled\",\"value\":\"\",\"isError\":false}]",
                        "type" => "calculation"
                    ]
                ]
            ]
        ];

        $addedPropForm = SheetDB::api()->setMultipleFormProperties($appointmentForm[0]['id'], json_encode($additionalProperties));
        $therapist->appointmentForm = 'https://www.jotform.com/' . $appointmentForm[0]['id'];
        $therapist->save();
        $appointmentForm = array_merge($appointmentForm, $addedPropForm);
        return $appointmentForm;
    }

    public function getAllAppointmentsOfTherapists()
    {
        $allTherapists = $this->getAllTherapists();
        $therapistAppointmentFormIds = array_map(function ($appointmentForm) {
            return end(explode('/', $appointmentForm));
        }, array_column($allTherapists, 'appointmentForm'));
        var_dump($therapistAppointmentFormIds);
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

    public function getAllAppointments($filter = ['offset' => 0, 'limit' => 1000]) {
        $therapists = (new TherapistModel())->all();
        $mapped = array_map(function ($model) {
            return $this->getTherapistCalendar($model['id']);
        }, $therapists);
        $modelFilter = $filter;
        unset($modelFilter['slug'], $modelFilter['offset'], $modelFilter['limit']);
        $mapped = $this->filterAppointments($mapped, $modelFilter);
        $offset = $filter['offset'] ?? 0;
        $limit = $filter['limit'] ?? 1000;
        return array_slice($mapped, $offset, $limit);
    }

    public function getTherapistCalendar($therapistID) {
        $therapist = (new TherapistModel())->findByPrimaryKey($therapistID);
        $therapistAppointmentFormID = end(explode('/', $therapist->appointmentForm));
        //$formProps = SheetDB::table($therapistAppointmentFormID)->withColumns()->get();
        $customTherapistAppointmentModel = CustomModel::class;
        $customTherapistAppointmentModel::set('tableName', $therapistAppointmentFormID);

        if (empty($therapistAppointmentFormID)) {
            return;
        }
        $appointmentModel = (new $customTherapistAppointmentModel());
        $return = $appointmentModel->all();
        $clientModel = (new ClientModel());
        $appointments = array_map(function ($model) use ($clientModel) {
            $formattedDate = date('F m, Y H:i', strtotime($model['randevu']['date']));
            $client = $clientModel->findByEposta($model['hastaEposta'])[0];
            return [
                'client_name' => $client['isim']['first'] . ' ' . $client['isim']['last'],
                'appointment' => $formattedDate
            ];
        }, $return);

        return [
            'therapist_name' => $therapist->isim['first'] . ' ' . $therapist->isim['last'],
            'appointments' => $appointments
        ];
    }

    public function filterAppointments($appointments, $filter = []) {
        $filtered = $appointments;
        $filtered = array_filter($appointments, function ($appointment) use ($filter) {
            $condition = true;
            foreach ($filter as $key => $value) {
                if (!in_array($key, ['date', 'startDate', 'endDate', 'client_name'])) {
                    $condition = $condition && $appointment[$key] === $value;
                }
            }
            return $condition;
        });

        if (empty($filtered)) {
            return [];
        }

        array_walk($filtered, function (&$appointment) use ($filter) {
            $therapistAppointments = $appointment['appointments'];
            if (!empty($therapistAppointments)) {
                $filteredAppointments = array_filter($therapistAppointments, function ($therapistAppointment) use ($filter) {
                    $condition = true;
                    foreach ($filter as $key => $value) {
                        if (in_array($key, ['startDate', 'endDate', 'client_name'])) {
                            if ($key === 'startDate') {
                                $date = DateTime::createFromFormat('F m, Y H:i', $therapistAppointment['appointment'])->getTimestamp();
                                $condition = $condition && $date >= strtotime($value);
                            } elseif ($key === 'endDate') {
                                $date = DateTime::createFromFormat('F m, Y H:i', $therapistAppointment['appointment'])->getTimestamp();
                                $condition = $condition && $date <= strtotime($value);
                            } else {
                                $condition = $condition && $therapistAppointment === $value;
                            }
                        }
                    }
                    return $condition;
                });
            }
            $appointment["appointments"] = $filteredAppointments;
        });

        return $filtered;
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
