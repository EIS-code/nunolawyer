<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Nunolawyer') }}</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

        <style type="text/css">
            * {
                font-size: 20px;
            }
        </style>
    </head>
    <body onbeforeprint="beforePrints()" onafterprint="afterPrints()">
        <div class="container">
            <div class="print-only" style="margin-top: 20px;width: 100%;font-size: 22px;">
                <h1 class="header" style="text-transform: uppercase;color: blue;">{{ $client->first_name .' '. $client->last_name }}</h1>
                <table class="user-table table table-bordered">
                    <tbody>
                        <tr>
                            <th class="table-main">{{ __('Registered Date') }}</th>
                            <th class="table-contain">
                                {{ date('Y-m-d', strtotime($client->registration_date)) }}
                            </th>
                            <th class="table-main">{{ __('Terms and Condition') }}</th>
                        </tr>
                        <tr>
                            <td class="table-main">{{ __('Name') }}</td>
                            <td class="table-contain">{{ $client->first_name .' '. $client->last_name }}</td>
                            <td rowspan="8" style="width:50%;">
                                <p>1. {{ __('Adm. Fee is for mention purpose, Art and for one time attempt in Lisbon work area only, otherwise additional charge, hotel booking cost plus transportation charge 0.50 Cent Euro per K.M will be charged.') }}</p>
                                <p>2. {{ __('Missing / Due amount will be charged before your appointment on any suitable day.') }}</p>
                                <p>3. {{ __('All the money we have received and will be received is always non refundable.') }}</p>
                                <p>4. {{ __('If the related authority, Gov. Policy, rules or law change in the mean time our fee is also subject to change without pre notice and information will be delivered at your presence in our office.') }}</p>
                                <p>5.&nbsp; {{ __('It confirms the client is agreed to allow us to use their personal data and details for related work on their behalf.') }}</p>
                                <p>6. {{ __('This document considers as an agreement between clients and us to follow their process, and also it is the receipt of payment. If client fails in agreement the purposed all amount will be charged accordingly by legal way.') }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-main">{{ __('Nationality') }}</td>
                            <td class="table-contain">{{ $client->nationality }}</td>
                        </tr>
                        <tr>
                            <td class="table-main">{{ __('Date of Birth') }}</td>
                            <td class="table-contain">{{ $client->dob }}</td>
                        </tr>
                        <tr>
                            <td class="table-main">{{ __('Email') }}</td>
                            <td class="table-contain">
                                {{ $client->email }} <br /><hr />
                                {{ __('Password') }}   &nbsp;&nbsp;&nbsp;: <i>{{ $client->password_text }}</i> <br />
                                {{ __('Password 2') }} : <i>{{ $client->password_text_2 }}</i>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-main">{{ __('Contact') }}</td>
                            <td class="table-contain">{{ $client->contact . (!empty($client->secondary_contact) ? ', ' . $client->secondary_contact : '') }}</td>
                        </tr>
                        <tr>
                            <td class="table-main">{{ __('Process Address') }}</td>
                            <td class="table-contain">{{ $client->process_address }}</td>
                        </tr>
                        <tr>
                            @php
                                $titles = [];
                                $client->clientPurposeArticles->map(function($data) use(&$titles) {
                                    if (!empty($data->purposeArticle)) {
                                        $titles[] = $data->purposeArticle->title;
                                    }
                                });
                            @endphp
                            <td class="table-main" rowspan="{{ count($titles) }}">{{ __('Purpose/Art.') }}</td>
                            <td class="table-contain">
                                @if (!empty($titles))
                                    <table class="table table-bordered">
                                        @foreach ($titles as $title)
                                            <tr>
                                                <td>{{ $title }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead>
                        <tr class="fee-header">
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Fee Details') }}</th>
                            <th>{{ __('Lawyer Fee Euro') }}</th>
                            <th>{{ __('Fee Details') }}</th>
                            <th>{{ __('Gov. Or Fee Euro for other Authority') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($client->clientFees as $index => $clientFee)
                            <tr>
                                <td rowspan="3" style="text-align: center;vertical-align: middle;">{{ date('Y-m-d', strtotime($clientFee->date)) }}</td>
                                <td>{{ __('Total Proposed') }}</td>
                                <td>{{ $clientFee->proposed_lawyer_fee }}</td>
                                <td>{{ __('Total Proposed') }}</td>
                                <td>{{ $clientFee->proposed_government_fee }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Received') }}</td>
                                <td>{{ $clientFee->received_lawyer_fee }}</td>
                                <td>{{ __('Received') }}</td>
                                <td>{{ $clientFee->received_government_fee }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Missing') }}</td>
                                <td>{{ $clientFee->missing_lawyer_fee }}</td>
                                <td>{{ __('Missing') }}</td>
                                <td>{{ $clientFee->missing_government_fee }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead>
                        <tr class="fee-header">
                            <th style="width:10%">{{ __('Date') }}</th>
                            <th>{{ __('Client Condition') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($client->clientConditions as $index => $clientCondition)
                            <tr>
                                <td>{{ date('Y-m-d', strtotime($clientCondition->date)) }}</td>
                                <td>
                                    <p>{{ $clientCondition->condition }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($loggedInId == $client->id || $client->hasSuperAdmin())
                    <table class="table table-bordered" id="print-no">
                        <thead>
                            <tr class="fee-header">
                                <th style="width:10%">{{ __('Date') }}</th>
                                <th>
                                    {{ __('Client Private Information') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->clientPrivateInformations as $index => $clientPrivateInformation)
                                <tr>
                                    <td>{{ date('Y-m-d', strtotime($clientPrivateInformation->date)) }}</td>
                                    <td>
                                        <p>{{ $clientPrivateInformation->private_information }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <table class="table table-bordered">
                    <thead>
                        <tr class="fee-header">
                            <th style="width:10%">{{ __('SN') }}</th>
                            <th>{{ __('Client Documents') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($client->clientDocuments as $index => $clientDocument)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if (!empty($clientDocument))
                                        <a href="{{ $clientDocument->file }}" target="__blank">
                                            {{ __('View') }}
                                        </a>

                                    @else
                                        <mark>{{ __('No file!') }}</mark>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead>
                        <tr class="fee-header">
                            <th style="width:10%">{{ __('Date') }}</th>
                            <th>{{ __('Progress Report To The Client') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($client->clientEmailProgressReports as $index => $clientEmailProgressReport)
                            <tr>
                                <td>{{ date('Y-m-d', strtotime($clientEmailProgressReport->date)) }}</td>
                                <td>
                                    <p>{{ $clientEmailProgressReport->progress_report }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead>
                        <tr class="fee-header">
                            <th style="width:10%">{{ __('SN') }}</th>
                            <th>{{ __('Translate Documents') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($client->translateModelDocuments as $index => $translateModelDocument)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if (!empty($translateModelDocument))
                                        <a href="{{ $translateModelDocument->file }}" target="__blank">
                                            {{ __('View') }}
                                        </a>

                                    @else
                                        <mark>{{ __('No file!') }}</mark>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                -----------------<br>
                {{ __('Client Signature') }}<br>
                <h1 class="nuno-header" style="padding-top:0px;color: blue;">
                    {{ __('Dr. Nuno Ramos Correia') }}<br>
                </h1>
                <p class="nuno-details">
                    {{ __('Lawyer Office Lisbon') }}<br>
                    {{ __('Address: Avenida Almirante Reis n, 59, 3rd Floor to the right, 1150-011, Lisbon, Portugal.') }}<br>
                    {{ __('Phone: +351211356129') }}<br>
                    {{ __('Phone: +351966817351 (For Call/Message/Whats up /Viber /Imo)') }}<br>
                    {{ __('Email: nrcadvogados.pt@gmail.com') }}<br>
                </p>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        function beforePrints()
        {
            let printNo = document.getElementById('print-no');

            if (printNo) {
                printNo.style.display = "none";
            }
        }

        function afterPrints()
        {
            let printNo = document.getElementById('print-no');

            if (printNo) {
                printNo.style.display = "block";
            }
        }
    </script>
</html>
