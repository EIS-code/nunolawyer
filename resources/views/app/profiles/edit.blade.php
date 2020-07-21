@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{ $user->first_name . ' ' . $user->last_name }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card bg-white">
            <div class="card-body">
                <form method="POST" action="{{ route(($isEditors ? 'editors.update' : 'clients.update'), $user->id) }}" enctype='multipart/form-data'>
                    @method('PATCH')
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Registration Date') }}<span style="color: red;">*</span></label>
                            <input id="registration_date" type="date" class="form-control{{ $errors->has('registration_date') ? ' is-invalid' : '' }}" name="registration_date" value="{{ date('Y-m-d', strtotime($user->registration_date)) }}" required autofocus>

                            @if ($errors->has('registration_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('registration_date') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('E-Mail') }}<span style="color: red;">*</span></label>
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $user->email }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('First Name') }}<span style="color: red;">*</span></label>
                            <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ $user->first_name }}" required autofocus>

                            @if ($errors->has('first_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Last Name') }}<span style="color: red;">*</span></label>
                            <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ $user->last_name }}">

                            @if ($errors->has('last_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Secondary E-Mail') }}</label>
                            <input id="secondary_email" type="email" class="form-control{{ $errors->has('secondary_email') ? ' is-invalid' : '' }}" name="secondary_email" value="{{ $user->secondary_email }}">

                            @if ($errors->has('secondary_email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('secondary_email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Date of birth') }}</label>
                            <input id="dob" type="date" class="form-control{{ $errors->has('dob') ? ' is-invalid' : '' }}" name="dob" value="{{ $user->dob }}">

                            @if ($errors->has('dob'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dob') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Password') }}<span style="color: red;"> * {{ __("Leave blank if don't want to update") }}</span></label>
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Contact') }}</label>
                            <input id="contact" type="number" class="form-control{{ $errors->has('contact') ? ' is-invalid' : '' }}" name="contact" value="{{ $user->contact }}">

                            @if ($errors->has('contact'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('contact') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Passport Number') }}</label>
                            <input id="passport_number" type="text" class="form-control{{ $errors->has('passport_number') ? ' is-invalid' : '' }}" name="passport_number" value="{{ $user->passport_number }}">

                            @if ($errors->has('passport_number'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('passport_number') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Process Address') }}</label>
                            <input id="process_address" type="text" class="form-control{{ $errors->has('process_address') ? ' is-invalid' : '' }}" name="process_address" value="{{ $user->process_address }}">

                            @if ($errors->has('process_address'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('process_address') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Nationality') }}</label>
                            <input id="nationality" type="text" class="form-control{{ $errors->has('nationality') ? ' is-invalid' : '' }}" name="nationality" value="{{ $user->nationality }}">

                            @if ($errors->has('nationality'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('nationality') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        @php
                            $userPurposeArticles = $user->clientPurposeArticles;
                            $lastInsertedId        = $userPurposeArticles->where('is_last_inserted', '1')->first();
                            $purposeArticleIds     = $userPurposeArticles->pluck('purpose_article_id')->toArray();
                        @endphp

                        <div class="col-md-2">{{ __('Purpose and Article') }}</div>
                        <div class="col-md-10">
                            <select class="form-control{{ $errors->has('nationality') ? ' is-invalid' : '' }} purpose_articles" name="purpose_articles[]" multiple="true">
                                <!--option value="" {{ (empty($purposeArticleIds) ? 'selected="true"' : '') }}>{{ __('Select') }}</option-->

                                @foreach ($purposeArticles as $purposeArticle)
                                    <option value="{{ $purposeArticle->id }}" {{ (!empty($purposeArticleIds) && in_array($purposeArticle->id, $purposeArticleIds) ? 'selected="true"' : '') }}>{{ $purposeArticle->title }}</option>
                                @endforeach
                                <input type="hidden" name="last_purpose_articles" id="last_purpose_articles" value="{{ (!empty($lastInsertedId)) ? $lastInsertedId->purpose_article_id : '' }}" />
                            </select>

                            @if ($errors->has('purpose_articles'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('purpose_articles') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row" id="row-pa">
                        @php
                            $userConditions = $user->clientConditions->toArray();

                            if (empty($userConditions)) {
                                $userConditions[] = [
                                    'id'        => '',
                                    'date'      => '',
                                    'condition' => ''
                                ];
                            }
                        @endphp

                        <div class="col-md-2">{{ __('Client Condition / Work To Do') }}</div>

                        <div class="col-md-10">
                            @foreach ($userConditions as $index => $userCondition)
                                <div class="row" id="{{ ($index == 0 ? 'main-pa' : '') }}">
                                    <div class="col-md-12">
                                        <table class="table table-respopnsive table-bordered">
                                            <thead>
                                                <th width="1%">#</th>
                                                <th width="20%">{{ __('Date') }}</th>
                                                <th width="79%">{{ __('Client Condition') }}</th>
                                                <th></th>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td >{{ $index + 1 }}</td>
                                                    <td>
                                                        <input type="date" class="form-control{{ $errors->has('condition_dates.' . $index) ? ' is-invalid' : '' }}" name="condition_dates[]" value="{{ old('condition_dates.' . $index, (!empty($userCondition['date']) ? date('Y-m-d', strtotime($userCondition['date'])) : '')) }}">

                                                        @if ($errors->has('condition_dates.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('condition_dates.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control{{ $errors->has('conditions.' . $index) ? ' is-invalid' : '' }}" rows="2" cols="5" name="conditions[]">{{ old('conditions.' . $index, $userCondition['condition']) }}</textarea>

                                                        @if ($errors->has('conditions.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('conditions.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <i class="{{ ($index == 0 ? 'fa fa-plus' : 'fa fa-trash') }}" id="{{ ($index == 0 ? 'plus-pa' : 'minus-pa') }}" style="cursor: pointer;"></i>
                                                    </td>
                                                    <input type="hidden" name="id_client_conditions[]" value="{{ $userCondition['id'] }}" />
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            <div id="cloned-pa"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-cf">
                        @php
                            $userFees = $user->clientFees->toArray();

                            if (empty($userFees)) {
                                $userFees[] = [
                                    'id'                      => '',
                                    'date'                    => '',
                                    'proposed_lawyer_fee'     => '',
                                    'received_lawyer_fee'     => '',
                                    'missing_lawyer_fee'      => '',
                                    'proposed_government_fee' => '',
                                    'received_government_fee' => '',
                                    'missing_government_fee'  => ''
                                ];
                            }
                        @endphp

                        <div class="col-md-2">{{ __('Client Fee') }}</div>

                        <div class="col-md-10">
                            @foreach ($userFees as $index => $userFee)
                                <div class="row" id="{{ ($index == 0 ? 'main-cf' : '') }}">
                                    <div class="col-md-12 table-respopnsive text-nowrap">
                                        <table class="table table-bordered" width="100%;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%">#</th>
                                                    <th style="width: 10%">{{ __('Date') }}</th>
                                                    <th style="width: 15%">{{ __('Total Proposed - Lawyer Fee') }}</th>
                                                    <th style="width: 15%">{{ __('Received -Lawyer Fee') }}</th>
                                                    <th style="width: 15%">{{ __('Missing- Lawyer Fee') }}</th>
                                                    <th style="width: 1%"></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td rowspan="4">{{ $index + 1 }}</td>
                                                    <td>
                                                        <input type="date" class="form-control{{ $errors->has('fee_dates.' . $index) ? ' is-invalid' : '' }}" name="fee_dates[]" value="{{ old('fee_dates.' . $index, (!empty($userFee['date']) ? date('Y-m-d', strtotime($userFee['date'])) : '')) }}">

                                                        @if ($errors->has('fee_dates.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('fee_dates.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control{{ $errors->has('total_proposed_lawyer_fee.' . $index) ? ' is-invalid' : '' }}" name="total_proposed_lawyer_fee[]" value="{{ old('total_proposed_lawyer_fee.' . $index, $userFee['proposed_lawyer_fee']) }}">

                                                        @if ($errors->has('total_proposed_lawyer_fee.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('total_proposed_lawyer_fee.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control{{ $errors->has('received_lawyer_fee.' . $index) ? ' is-invalid' : '' }}" name="received_lawyer_fee[]" value="{{ old('received_lawyer_fee.' . $index, $userFee['received_lawyer_fee']) }}">

                                                        @if ($errors->has('received_lawyer_fee.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('received_lawyer_fee.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control{{ $errors->has('missing_lawyer_fee.' . $index) ? ' is-invalid' : '' }}" name="missing_lawyer_fee[]" value="{{ old('missing_lawyer_fee.' . $index, $userFee['missing_lawyer_fee']) }}">

                                                        @if ($errors->has('missing_lawyer_fee.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('missing_lawyer_fee.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td  rowspan="4">
                                                        <i class="{{ ($index == 0 ? 'fa fa-plus' : 'fa fa-trash') }}" id="{{ ($index == 0 ? 'plus-cf' : 'minus-cf') }}" style="cursor: pointer;"></i>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 15%">{{ __('Total Proposed-Gov Fee') }}</th>
                                                    <th style="width: 15%">{{ __('Received-Gov Fee') }}</th>
                                                    <th style="width: 15%">{{ __('Missing-Gov Fee') }}</th>
                                                    <th rowspan="2"></th>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control{{ $errors->has('total_proposed_government_fee.' . $index) ? ' is-invalid' : '' }}" name="total_proposed_government_fee[]" value="{{ old('total_proposed_government_fee.' . $index, $userFee['proposed_government_fee']) }}">

                                                        @if ($errors->has('total_proposed_government_fee.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('total_proposed_government_fee.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control{{ $errors->has('received_government_fee.' . $index) ? ' is-invalid' : '' }}" name="received_government_fee[]" value="{{ old('received_government_fee.' . $index, $userFee['received_government_fee']) }}">

                                                        @if ($errors->has('received_government_fee.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('received_government_fee.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control{{ $errors->has('missing_government_fee.' . $index) ? ' is-invalid' : '' }}" name="missing_government_fee[]" value="{{ old('missing_government_fee.' . $index, $userFee['missing_government_fee']) }}">

                                                        @if ($errors->has('missing_government_fee.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('missing_government_fee.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <input type="hidden" name="id_client_fees[]" value="{{ $userFee['id'] }}" />
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            <div id="cloned-cf"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-pr">
                        @php
                            $userEmailProgressReports = $user->clientEmailProgressReports->toArray();

                            if (empty($userEmailProgressReports)) {
                                $userEmailProgressReports[] = [
                                    'id'              => '',
                                    'date'            => '',
                                    'progress_report' => '',
                                    'file'            => ''
                                ];
                            }
                        @endphp

                        <div class="col-md-2">{{ __('Progress Report To The Client (By Email)') }}</div>

                        <div class="col-md-10">
                            @foreach ($userEmailProgressReports as $index => $userEmailProgressReport)
                                <div class="row" id="{{ ($index == 0 ? 'main-pr' : '') }}">
                                    <div class="table-respopnsive col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th width="1%">#</th>
                                                <th width="20%">{{ __('Date') }}</th>
                                                <th width="69%">{{ __('Progress Report') }}</th>
                                                <th width="10%">{{ __('File') }}</th>
                                                <th></th>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td >{{ $index + 1 }}</td>
                                                    <td>
                                                        <input type="date" class="form-control{{ $errors->has('progress_report_dates.' . $index) ? ' is-invalid' : '' }}" name="progress_report_dates[]" value="{{ old('progress_report_dates.' . $index, (!empty($userEmailProgressReport['date']) ? date('Y-m-d', strtotime($userEmailProgressReport['date'])) : '')) }}">

                                                        @if ($errors->has('progress_report_dates.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('progress_report_dates.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control{{ $errors->has('progress_reports.' . $index) ? ' is-invalid' : '' }}" rows="2" cols="5" name="progress_reports[]">{{ old('progress_reports.' . $index, $userEmailProgressReport['progress_report']) }}</textarea>

                                                        @if ($errors->has('progress_reports.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('progress_reports.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty($userEmailProgressReport['file']))
                                                            <a href="{{ $userEmailProgressReport['file'] }}" target="_blank">{{ __('View') }}</a><br />
                                                        @endif
                                                        <input type="file" class="{{ $errors->has('progress_report_files.' . $index) ? ' is-invalid' : '' }}" name="progress_report_files[]" />

                                                        @if ($errors->has('progress_report_files.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('progress_report_files.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <i class="{{ ($index == 0 ? 'fa fa-plus' : 'fa fa-trash') }}" id="{{ ($index == 0 ? 'plus-pr' : 'minus-pr') }}" style="cursor: pointer;"></i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($userEmailProgressReports as $index => $userEmailProgressReport)
                                <input type="hidden" name="id_client_email_progress_reports[]" value="{{ $userEmailProgressReport['id'] }}" />
                            @endforeach

                            <div id="cloned-pr"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-ci">
                        @php
                            $userPrivateInformations = $user->clientPrivateInformations->toArray();

                            if (empty($userPrivateInformations)) {
                                $userPrivateInformations[] = [
                                    'id'                  => '',
                                    'date'                => '',
                                    'private_information' => ''
                                ];
                            }
                        @endphp

                        <div class="col-md-2">{{ __('Client Private Information') }}</div>

                        <div class="col-md-10">
                            @foreach ($userPrivateInformations as $index => $userPrivateInformation)
                                <div class="row" id="{{ ($index == 0 ? 'main-ci' : '') }}">
                                    <div class="col-md-12">
                                        <table class="table table-respopnsive table-bordered">
                                            <thead>
                                                <th width="1%">#</th>
                                                <th width="20%">{{ __('Date') }}</th>
                                                <th width="79%">{{ __('Client Private Information') }}</th>
                                                <th></th>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td >{{ $index + 1 }}</td>
                                                    <td>
                                                        <input type="date" class="form-control{{ $errors->has('client_private_dates.' . $index) ? ' is-invalid' : '' }}" name="client_private_dates[]" value="{{ old('client_private_dates.' . $index, (!empty($userPrivateInformation['date']) ? date('Y-m-d', strtotime($userPrivateInformation['date'])) : '')) }}">

                                                        @if ($errors->has('client_private_dates.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('client_private_dates.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control{{ $errors->has('client_private_informations.' . $index) ? ' is-invalid' : '' }}" rows="2" cols="5" name="client_private_informations[]">{{ old('client_private_informations.' . $index, $userPrivateInformation['private_information']) }}</textarea>

                                                        @if ($errors->has('client_private_informations.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('client_private_informations.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <i class="{{ ($index == 0 ? 'fa fa-plus' : 'fa fa-trash') }}" id="{{ ($index == 0 ? 'plus-ci' : 'minus-ci') }}" style="cursor: pointer;"></i>
                                                    </td>
                                                    <input type="hidden" name="id_client_private_informations[]" value="{{ $userPrivateInformation['id'] }}" />
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            <div id="cloned-ci"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-cd">
                        @php
                            $userDocuments = $user->clientDocuments->toArray();

                            if (empty($userDocuments)) {
                                $userDocuments[] = [
                                    'id'   => '',
                                    'file' => ''
                                ];
                            }
                        @endphp

                        <div class="col-md-2">{{ __('Client Documents') }}</div>

                        <div class="col-md-10">
                            @foreach ($userDocuments as $index => $userDocument)
                                <div class="row" id="{{ ($index == 0 ? 'main-cd' : '') }}">
                                    <div class="col-md-12">
                                        <table class="table table-respopnsive table-bordered">
                                            <thead>
                                                <th width="1%">#</th>
                                                <th width="99%">{{ __('File') }}</th>
                                                <th></th>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td >{{ $index + 1 }}</td>
                                                    <td>
                                                        @if (!empty($userDocument['file']))
                                                            <a href="{{ $userDocument['file'] }}" target="_blank">{{ __('View') }}</a><br />
                                                        @endif
                                                        <input type="file" class="form-control{{ $errors->has('client_documents.' . $index) ? ' is-invalid' : '' }}" name="client_documents[]" value="{{ old('client_documents.' . $index) }}">

                                                        @if ($errors->has('client_documents.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('client_documents.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <i class="{{ ($index == 0 ? 'fa fa-plus' : 'fa fa-trash') }}" id="{{ ($index == 0 ? 'plus-cd' : 'minus-cd') }}" style="cursor: pointer;"></i>
                                                    </td>
                                                    <input type="hidden" name="id_client_documents[]" value="{{ $userDocument['id'] }}" />
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($userDocuments as $index => $userDocument)
                                <input type="hidden" name="id_client_documents_old[{{ $userDocument['id'] }}]" value="{{ $userDocument['id'] }}" />
                            @endforeach

                            <div id="cloned-cd"></div>

                        </div>
                    </div>

                    <div class="form-group row {{ ($isEditors ? 'd-none' : '') }}" id="row-tc">
                        @php
                            $userTermsAndConditions = $user->clientTermsAndConditions->toArray();

                            if (empty($userTermsAndConditions)) {
                                $userTermsAndConditions[] = [
                                    'id'                   => '',
                                    'terms_and_conditions' => ''
                                ];
                            }
                        @endphp

                        <div class="col-md-2">{{ __('Terms and Condition') }}</div>

                        <div class="col-md-10">
                            <table class="table table-respopnsive table-bordered" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th width="10%">{{ __('Default') }}</th>
                                        <td><i class="text text-danger">{{ __('Default Terms and Condition') }}</i></td>
                                    </tr>
                                </thead>
                            </table>
                            @foreach ($userTermsAndConditions as $index => $userTermsAndCondition)
                                <div class="row" id="{{ ($index == 0 ? 'main-tc' : '') }}">
                                    <div class="col-md-12">
                                        <table class="table table-respopnsive table-bordered" style="margin-bottom: 0;">
                                            <thead>
                                                <th width="1%">#</th>
                                                <th width="99%">{{ __('Custom') }}</th>
                                                <th></th>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td >{{ $index + 1 }}</td>
                                                    <td>
                                                        <textarea class="form-control{{ $errors->has('terms_and_conditions.' . $index) ? ' is-invalid' : '' }}" rows="2" cols="5" name="terms_and_conditions[]">{{ old('terms_and_conditions.' . $index, $userTermsAndCondition['terms_and_conditions']) }}</textarea>

                                                        @if ($errors->has('terms_and_conditions.' . $index))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('terms_and_conditions.' . $index) }}</strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <i class="{{ ($index == 0 ? 'fa fa-plus' : '') }}" id="{{ ($index == 0 ? 'plus-tc' : 'minus-tc') }}" style="cursor: pointer;"></i>
                                                    </td>
                                                    <input type="hidden" name="id_client_terms_and_conditions[]" value="{{ $userTermsAndCondition['id'] }}" />
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            <div id="cloned-tc"></div>

                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">{{ __('Role') }}</div>
                        <div class="col-md-3">
                            @if($user->isSuperAdmin())
                                <span class="badge badge-lg badge-secondary text-white">
                                    {{ __('admin') }}
                                </span>
                            @else
                                <select class="form-control" name="role_id" style="display: none;">
                                    @foreach($roles as $role)
                                        @if ($isEditors && $role->id != '3')
                                            @continue
                                        @elseif (!$isEditors && $role->id != '2')
                                            @continue
                                        @endif
                                        <option value="{{$role->id}}" @if($user->hasRole($role)) selected @endif>{{$role->name}}</option>
                                    @endforeach
                                </select>
                                <span class="badge badge-lg badge-secondary text-white">
                                    @if ($isEditors)
                                        {{ __('Editor') }}
                                    @else
                                        {{ __('Client') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">{{ __('Work Status') }}</div>
                        <div class="col-md-6">
                            @foreach ($user::$workStatus as $value => $text)
                                <label>
                                    <input type="radio" id="status-{{ strtolower(str_ireplace(' ', '-', $text)) }}" name="work_status" value="{{ $value }}" @if (old('work_status', $user->getAttributes()['work_status']) == $value) checked="true"' @endif />&nbsp;{{ $text }}
                                </label>&nbsp;
                            @endforeach

                            @if ($errors->has('work_status'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('work_status') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    @php
                        $isShow = (old('work_status', $user->getAttributes()['work_status']) == '1');
                    @endphp
                    <div class="div-to-follow {{ ($isShow ? '' : 'd-none') }}">
                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Assign Date') }}<span style="color: red;">*</span></div>
                            <div class="col-md-3">
                                <input id="assign_date" type="date" class="form-control{{ $errors->has('assign_date') ? ' is-invalid' : '' }}" name="assign_date" value="{{ (!empty($user->assign_date) ? date('Y-m-d', strtotime($user->assign_date)) : NULL) }}">

                                @if ($errors->has('assign_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('assign_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">{{ __('Assign To') }}<span style="color: red;">*</span></div>
                            <div class="col-md-3">
                                <select id="assign_to" multiple="true" class="form-control {{ $errors->has('assign_to.0') ? ' is-invalid' : '' }} assign_to" name="assign_to[]">
                                    <!--option value="">{{ __('Select') }}</option-->

                                    @foreach ($assignTo as $assign)
                                        <option value="{{ $assign->id }}" {{ (in_array($assign->id, $assignedTo) ? 'selected' : '') }}>{{ $assign->first_name . ' ' . $assign->last_name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('assign_to.0'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('assign_to.0') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Save') }}
                            </button>
                            <a href="{{route('clients.show', $user->id)}}" class="btn btn-danger">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('app.clients.scripts')
@endsection
