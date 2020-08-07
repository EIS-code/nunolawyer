@extends('layout')

@section('sub_content')

    @php
        $isEditors = $client::$isEditors;
    @endphp
    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <a href="{{route('clients.index')}}" class="breadcrumb-item">{{ ($isEditors ? __('Editors') : __('Clients')) }}</a>
                    <span class="breadcrumb-item active">{{ ($isEditors ? __('Show Editor') : __('Show Client')) }}</span>
                    <span class="breadcrumb-item active">{{$client->first_name . ' ' . $client->last_name}}</span>
                </div>
            </div>
        </div>
    </div>
	
    <div class="content">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            @if (is_array(session('error')))
                <div class="alert alert-danger" role="alert">
                    @foreach (session('error') as $error)
                        {{ $error }}<br />
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('app.clients.nav')

                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Registration Date') }}</div>
                            <div class="col-md-8">
                                {{ $client->registration_date }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('DOB') }}</div>
                            <div class="col-md-8">
                                {{ $client->dob }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('First Name') }}</div>
                            <div class="col-md-8">
                                {{ $client->first_name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Last Name') }}</div>
                            <div class="col-md-8">
                                {{ $client->last_name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Email') }}</div>
                            <div class="col-md-8">
                                {{ $client->email }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Secondary Email') }}</div>
                            <div class="col-md-8">
                                {{ $client->secondary_email }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Password') }}</div>
                            <div class="col-md-8">
                                {{ $client->password_text }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Password 2') }}</div>
                            <div class="col-md-8">
                                {{ $client->password_text_2 }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Process Address') }}</div>
                            <div class="col-md-8">
                                {{ $client->process_address }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Contact') }}</div>
                            <div class="col-md-8">
                                {{ $client->contact }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Secondary Contact') }}</div>
                            <div class="col-md-8">
                                {{ $client->secondary_contact }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Passport Number') }}</div>
                            <div class="col-md-3">
                                {{ $client->passport_number }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Nationality') }}</div>
                            <div class="col-md-8">
                                {{ $client->nationality }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Purpose and Article') }}</div>
                            <div class="col-md-8">
                                @php
                                    $titles = []; 
                                    $client->clientPurposeArticles->map(function($data) use(&$titles) {
                                        if (!empty($data->purposeArticle)) {
                                            $titles[] = $data->purposeArticle->title;
                                        }
                                    });
                                @endphp
                                {{ implode(", ", $titles) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Client Fee') }}</div>
                            <div class="col-md-10">
                                <table class="table table-respopnsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">#</th>
                                            <th style="width: 10%">{{ __('Date') }}</th>
                                            <th style="width: 15%">{{ __('Total Proposed - Lawyer Fee') }}</th>
                                            <th style="width: 15%">{{ __('Received -Lawyer Fee') }}</th>
                                            <th style="width: 15%">{{ __('Missing- Lawyer Fee') }}</th>
                                            <th style="width: 15%">{{ __('Total Proposed-Gov Fee') }}</th>
                                            <th style="width: 15%">{{ __('Received-Gov Fee') }}</th>
                                            <th style="width: 15%">{{ __('Missing-Gov Fee') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($client->clientFees as $index => $clientFee)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $clientFee->date }}</td>
                                                <td>{{ $clientFee->proposed_lawyer_fee }}</td>
                                                <td>{{ $clientFee->received_lawyer_fee }}</td>
                                                <td>{{ $clientFee->missing_lawyer_fee }}</td>
                                                <td>{{ $clientFee->proposed_government_fee }}</td>
                                                <td>{{ $clientFee->received_government_fee }}</td>
                                                <td>{{ $clientFee->missing_government_fee }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Client Condition / Work To Do') }}</div>
                            <div class="col-md-8">
                                <table class="table table-respopnsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="1%">#</th>
                                            <th width="25%">{{ __('Date') }}</th>
                                            <th width="74%">{{ __('Client Condition ') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($client->clientConditions as $index => $clientCondition)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $clientCondition->date }}</td>
                                                <td>{{ $clientCondition->condition }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($loggedInId == $client->id || $client->hasSuperAdmin())
                            <div class="form-group row">
                                <div class="col-md-2">{{ __('Client Private Informations') }}</div>
                                <div class="col-md-8">
                                    <table class="table table-respopnsive table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="1%">#</th>
                                                <th width="25%">{{ __('Date') }}</th>
                                                <th width="74%">{{ __('Client Private Informations') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($client->clientPrivateInformations as $index => $clientPrivateInformation)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $clientPrivateInformation->date }}</td>
                                                    <td>{{ $clientPrivateInformation->private_information }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Client Documents') }}</div>
                            <div class="col-md-8">
                                <table class="table table-respopnsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="1%">#</th>
                                            <th width="99%">{{ __('File') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($client->clientDocuments as $index => $clientDocument)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ $clientDocument->file }}" target="__blank">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Progress Report To The Client (By Email)') }}</div>
                            <div class="col-md-8">
                                <table class="table table-respopnsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="1%">#</th>
                                            <th width="25%">{{ __('Date') }}</th>
                                            <th width="64%">{{ __('Progress Report') }}</th>
                                            <th width="10%">{{ __('File') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($client->clientEmailProgressReports as $index => $clientEmailProgressReport)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $clientEmailProgressReport->date }}</td>
                                                <td>{{ $clientEmailProgressReport->progress_report }}</td>
                                                <td>
                                                    <a href="{{ $clientEmailProgressReport->file }}" target="__blank">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Terms and Condition') }}</div>
                            <div class="col-md-8">
                                <table class="table table-respopnsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="1%">#</th>
                                            <th width="99%">{{ __('Custom') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($client->clientTermsAndConditions as $index => $clientTermsAndCondition)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    {{ $clientTermsAndCondition->terms_and_conditions }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Work Status') }}</div>
                            <div class="col-md-3">
                                {{ $client->work_status }}
                            </div>
                        </div>
                        @if ($client->getAttributes()['work_status'] == '1')
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Assign') }}</div>
                            <div class="col-md-8">
                                <table class="table table-respopnsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="1%">#</th>
                                            <th width="20%">{{ __('Date') }}</th>
                                            <th width="79%">{{ __('Custom') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($client->followUps as $index => $followUp)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    {{ $followUp->date }}
                                                </td>
                                                <td>
                                                    {{ $client->getClientNames($followUp->follow_by) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Created at') }}</div>
                            <div class="col-md-3">
                                {{$client->created_at}}
                            </div>
                        </div>
                        <!--div class="form-group row">
                            <div class="col-md-2">{{ __('Role') }}</div>
                            <div class="col-md-3">
                                <span class="badge badge-lg badge-secondary text-white">{{@$client->getRoleNames()[0]}}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Permissions') }}</div>
                            <div class="col-md-10">
                                <table class="table table-striped table-bordered permissions_table">
                                    @foreach($groups as $group)
                                        <tr>
                                            <td>
                                                <h6 class="mb-2 font-weight-bold">{{$group['name']}}</h6>
                                                <div>
                                                    @foreach($group['permissions'] as $perm)
                                                        <label class="mr-4">
                                                            @if($client->hasPermissionTo($perm['id'])) 
                                                                <i class="metismenu-icon pe-7s-plus" style="color: green;"></i>
                                                            @else
                                                                <i class="metismenu-icon pe-7s-close-circle" style="color: red;"></i>
                                                            @endif
                                                            {{$perm['display_name'] !== null ? $perm['display_name'] : $perm['name']}}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div-->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
