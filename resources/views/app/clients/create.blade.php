@extends('layout')

@section('sub_content')

    @php
        $isEditors = $clientModel::$isEditors;
    @endphp

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <a href="{{ route(($isEditors ? 'editors.index' : 'clients.index')) }}" class="breadcrumb-item">{{ ($isEditors ? __('Editors') : __('Clients')) }}</a>
                    <span class="breadcrumb-item active">{{ ($isEditors ? __('Add New Editor') : __('Add New Client')) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card bg-white">
            <div class="card-body">
                <form method="POST" action="{{ route(($isEditors ? 'editors.store' : 'clients.store')) }}" enctype='multipart/form-data'>
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Registration Date') }}<span style="color: red;">*</span></label>
                            <input id="registration_date" type="date" class="form-control{{ $errors->has('registration_date') ? ' is-invalid' : '' }}" name="registration_date" value="{{ old('registration_date', date('Y-m-d', time())) }}" required autofocus>

                            @if ($errors->has('registration_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('registration_date') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Date of birth') }}</label>
                            <input id="dob" type="date" class="form-control{{ $errors->has('dob') ? ' is-invalid' : '' }}" name="dob" value="{{ old('dob') }}">

                            @if ($errors->has('dob'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dob') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('First Name') . ' ' . __('(Middle Name if any)') }}<span style="color: red;">*</span></label>
                            <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}">

                            @if ($errors->has('first_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Last Name') }}<span style="color: red;">*</span></label>
                            <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}">

                            @if ($errors->has('last_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('E-Mail') }}<span style="color: red;">*</span></label>
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Secondary E-Mail') }}</label>
                            <input id="secondary_email" type="email" class="form-control{{ $errors->has('secondary_email') ? ' is-invalid' : '' }}" name="secondary_email" value="{{ old('secondary_email') }}">

                            @if ($errors->has('secondary_email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('secondary_email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Password') }}<span style="color: red;">*</span></label>
                            <div class="inner-addon right-addon">
                                <i class="fa fa-eye togglePassword" id=""></i>
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Password 2') }}</label>
                            <div class="inner-addon right-addon">
                                <i class="fa fa-eye togglePassword" id=""></i>
                                <input id="password-2" type="password" class="form-control{{ $errors->has('password_2') ? ' is-invalid' : '' }}" name="password_2">
                                @if ($errors->has('password_2'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password_2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Process Address') }}</label>
                            <input id="process_address" type="text" class="form-control{{ $errors->has('process_address') ? ' is-invalid' : '' }}" name="process_address" value="{{ old('process_address') }}">

                            @if ($errors->has('process_address'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('process_address') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ __('Contact') }}</label>
                                    <input id="contact" type="number" class="form-control{{ $errors->has('contact') ? ' is-invalid' : '' }}" name="contact" value="{{ old('contact') }}">

                                    @if ($errors->has('contact'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('contact') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label>{{ __('Secondary Contact') }}</label>
                                    <input id="secondary_contact" type="number" class="form-control{{ $errors->has('secondary_contact') ? ' is-invalid' : '' }}" name="secondary_contact" value="{{ old('secondary_contact') }}">

                                    @if ($errors->has('secondary_contact'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('secondary_contact') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{ __('Passport Number') }}</label>
                            <input id="passport_number" type="text" class="form-control{{ $errors->has('passport_number') ? ' is-invalid' : '' }}" name="passport_number" value="{{ old('passport_number') }}">

                            @if ($errors->has('passport_number'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('passport_number') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('Nationality') }}<span style="color: red;">*</span></label>
                            <input id="nationality" type="text" class="form-control{{ $errors->has('nationality') ? ' is-invalid' : '' }}" name="nationality" value="{{ old('nationality') }}" required>

                            @if ($errors->has('nationality'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('nationality') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">{{ __('Purpose and Article') }}</div>
                        <div class="col-md-10">
                            <select class="form-control{{ $errors->has('purpose_articles') ? ' is-invalid' : '' }} purpose_articles" name="purpose_articles[]" multiple="true" required="true">
                                <!--option value="" {{ (empty(old('purpose_articles')) ? 'selected="true"' : '') }}>{{ __('Select') }}</option-->

                                @foreach ($purposeArticles as $purposeArticle)
                                    <option value="{{ $purposeArticle->id }}" {{ (!empty(old('purpose_articles')) && in_array($purposeArticle->id, old('purpose_articles')) ? 'selected="true"' : '') }}>{{ $purposeArticle->title }}</option>
                                @endforeach
                                <input type="hidden" name="last_purpose_articles" id="last_purpose_articles" />
                            </select>

                            @if ($errors->has('purpose_articles'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('purpose_articles') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row" id="row-cf">
                        <div class="col-md-2">{{ __('Client Fee') }}</div>

                        <div class="col-md-10">
                            <div class="row" id="main-cf">
                                <div class="col-md-12 table-respopnsive text-nowrap">
                                    <table class="table table-bordered" width="100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 1%">#</th>
                                                <th style="width: 14%">{{ __('Date') }}</th>
                                                <th style="width: 10%">{{ __('Total Proposed - ') }}<br />{{ __('Lawyer Fee') }}</th>
                                                <th style="width: 10%">{{ __('Received - ') }}<br />{{ __('Lawyer Fee') }}</th>
                                                <th style="width: 10%">{{ __('Missing - ') }}<br />{{ __('Lawyer Fee') }}</th>
                                                <th style="width: 10%">{{ __('Total Proposed - ') }}<br />{{ __('Gov Fee') }}</th>
                                                <th style="width: 10%">{{ __('Received - ') }}<br />{{ __('Gov Fee') }}</th>
                                                <th style="width: 10%">{{ __('Missing - ') }}<br />{{ __('Gov Fee') }}</th>
                                                <th style="width: 1%"></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td rowspan="4">1</td>
                                                <td>
                                                    <input type="date" class="form-control{{ $errors->has('fee_dates.0') ? ' is-invalid' : '' }}" name="fee_dates[]" value="{{ old('fee_dates.0') }}">

                                                    @if ($errors->has('fee_dates.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('fee_dates.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control{{ $errors->has('total_proposed_lawyer_fee.0') ? ' is-invalid' : '' }}" name="total_proposed_lawyer_fee[]" value="{{ old('total_proposed_lawyer_fee.0') }}">

                                                    @if ($errors->has('total_proposed_lawyer_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('total_proposed_lawyer_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control{{ $errors->has('received_lawyer_fee.0') ? ' is-invalid' : '' }}" name="received_lawyer_fee[]" value="{{ old('received_lawyer_fee.0') }}">

                                                    @if ($errors->has('received_lawyer_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('received_lawyer_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control{{ $errors->has('missing_lawyer_fee.0') ? ' is-invalid' : '' }}" name="missing_lawyer_fee[]" value="{{ old('missing_lawyer_fee.0') }}">

                                                    @if ($errors->has('missing_lawyer_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('missing_lawyer_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control{{ $errors->has('total_proposed_government_fee.0') ? ' is-invalid' : '' }}" name="total_proposed_government_fee[]" value="{{ old('total_proposed_government_fee.0') }}">

                                                    @if ($errors->has('total_proposed_government_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('total_proposed_government_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control{{ $errors->has('received_government_fee.0') ? ' is-invalid' : '' }}" name="received_government_fee[]" value="{{ old('received_government_fee.0') }}">

                                                    @if ($errors->has('received_government_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('received_government_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control{{ $errors->has('missing_government_fee.0') ? ' is-invalid' : '' }}" name="missing_government_fee[]" value="{{ old('missing_government_fee.0') }}">

                                                    @if ($errors->has('missing_government_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('missing_government_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td  rowspan="4">
                                                    <i class="fa fa-plus" id="plus-cf" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                            <!--tr>
                                                <th style="width: 15%">{{ __('Total Proposed-Gov Fee') }}</th>
                                                <th style="width: 15%">{{ __('Received-Gov Fee') }}</th>
                                                <th style="width: 15%">{{ __('Missing-Gov Fee') }}</th>
                                                <th rowspan="2"></th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control{{ $errors->has('total_proposed_government_fee.0') ? ' is-invalid' : '' }}" name="total_proposed_government_fee[]" value="{{ old('total_proposed_government_fee.0') }}">

                                                    @if ($errors->has('total_proposed_government_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('total_proposed_government_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control{{ $errors->has('received_government_fee.0') ? ' is-invalid' : '' }}" name="received_government_fee[]" value="{{ old('received_government_fee.0') }}">

                                                    @if ($errors->has('received_government_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('received_government_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control{{ $errors->has('missing_government_fee.0') ? ' is-invalid' : '' }}" name="missing_government_fee[]" value="{{ old('missing_government_fee.0') }}">

                                                    @if ($errors->has('missing_government_fee.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('missing_government_fee.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-cf"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-pa">
                        <div class="col-md-2">{{ __('Client Condition / Work To Do') }}</div>

                        <div class="col-md-10">
                            <div class="row" id="main-pa">
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
                                                <td >1</td>
                                                <td>
                                                    <input type="date" class="form-control{{ $errors->has('condition_dates.0') ? ' is-invalid' : '' }}" name="condition_dates[]" value="{{ old('condition_dates.0') }}">

                                                    @if ($errors->has('condition_dates.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('condition_dates.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <textarea class="form-control{{ $errors->has('conditions.0') ? ' is-invalid' : '' }}" rows="2" cols="5" name="conditions[]">{{ old('conditions.0') }}</textarea>

                                                    @if ($errors->has('conditions.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('conditions.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa fa-plus" id="plus-pa" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-pa"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-ci">
                        <div class="col-md-2">{{ __('Client Private Information') }}</div>

                        <div class="col-md-10">
                            <div class="row" id="main-ci">
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
                                                <td >1</td>
                                                <td>
                                                    <input type="date" class="form-control{{ $errors->has('client_private_dates.0') ? ' is-invalid' : '' }}" name="client_private_dates[]" value="{{ old('client_private_dates.0') }}">

                                                    @if ($errors->has('client_private_dates.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('client_private_dates.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <textarea class="form-control{{ $errors->has('client_private_informations.0') ? ' is-invalid' : '' }}" rows="2" cols="5" name="client_private_informations[]">{{ old('client_private_informations.0') }}</textarea>

                                                    @if ($errors->has('client_private_informations.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('client_private_informations.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa fa-plus" id="plus-ci" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-ci"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-cd">
                        <div class="col-md-2">{{ __('Client Documents') }}</div>

                        <div class="col-md-10">
                            <div class="row" id="main-cd">
                                <div class="col-md-12">
                                    <table class="table table-respopnsive table-bordered">
                                        <thead>
                                            <th width="1%">#</th>
                                            <th width="99%">{{ __('File') }}</th>
                                            <th></th>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td >1</td>
                                                <td>
                                                    <input type="file" class="form-control{{ $errors->has('client_documents.0') ? ' is-invalid' : '' }}" name="client_documents[]" value="{{ old('client_documents.0') }}">

                                                    @if ($errors->has('client_documents.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('client_documents.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa fa-plus" id="plus-cd" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-cd"></div>

                        </div>
                    </div>

                    <div class="form-group row" id="row-pr">
                        <div class="col-md-2">{{ __('Progress Report To The Client (By Email)') }}</div>

                        <div class="col-md-10">
                            <div class="row" id="main-pr">
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
                                                <td >1</td>
                                                <td>
                                                    <input type="date" class="form-control{{ $errors->has('progress_report_dates.0') ? ' is-invalid' : '' }}" name="progress_report_dates[]" value="{{ old('progress_report_dates.0') }}">

                                                    @if ($errors->has('progress_report_dates.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('progress_report_dates.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <textarea class="form-control{{ $errors->has('progress_reports.0') ? ' is-invalid' : '' }}" rows="2" cols="5" name="progress_reports[]">{{ old('progress_reports.0') }}</textarea>

                                                    @if ($errors->has('progress_reports.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('progress_reports.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="file" class="{{ $errors->has('progress_report_files.0') ? ' is-invalid' : '' }}" name="progress_report_files[]" />

                                                    @if ($errors->has('progress_report_files.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('progress_report_files.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa fa-plus" id="plus-pr" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-pr"></div>

                        </div>
                    </div>

                    <div class="form-group row {{ ($isEditors ? 'd-none' : '') }}" id="row-tc">
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
                            <div class="row" id="main-tc">
                                <div class="col-md-12">
                                    <table class="table table-respopnsive table-bordered" style="margin-bottom: 0;">
                                        <thead>
                                            <th width="1%">#</th>
                                            <th width="99%">{{ __('Custom') }}</th>
                                            <th></th>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td >1</td>
                                                <td>
                                                    <textarea class="form-control{{ $errors->has('terms_and_conditions.0') ? ' is-invalid' : '' }}" rows="2" cols="5" name="terms_and_conditions[]">{{ old('terms_and_conditions.0') }}</textarea>

                                                    @if ($errors->has('terms_and_conditions.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('terms_and_conditions.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa fa-plus" id="plus-tc" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-tc"></div>

                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">{{ __('Role') }}</div>
                        <div class="col-md-3">
                            <select class="form-control" name="role_id" style="display: none;">
                                @foreach($roles as $role)
                                    @if ($isEditors && $role->id != '3')
                                        @continue
                                    @elseif (!$isEditors && $role->id != '2')
                                        @continue
                                    @endif
                                    <option value="{{$role->id}}" {{ ($isEditors && $role->id == '3' ? 'selected="true"' : ($isEditors ? 'disabled="true"' : '')) }}>{{$role->name}}</option>
                                @endforeach
                            </select>
                            <span class="badge badge-lg badge-secondary text-white">
                                @if ($isEditors)
                                    {{ __('Editor') }}
                                @else
                                    {{ __('Client') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">{{ __('Work Status') }}</div>
                        <div class="col-md-6">
                            <!--label><input type="radio" id="status-default" name="work_status" value="0" @if (old('work_status') == '0' || old('work_status') == '') checked="true" @endif />&nbsp;{{ __('Default') }}</label>
                            <label><input type="radio" id="status-to-follow" name="work_status" value="1" @if (old('work_status') == '1') checked="true" @endif />&nbsp;{{ __('To Follow') }}</label-->
                            @foreach ($clientModel::$workStatus as $value => $text)
                                <label>
                                    <input type="radio" id="status-{{ strtolower(str_ireplace(' ', '-', $text)) }}" name="work_status" value="{{ $value }}" @if (old('work_status') == $value) checked="true"' @endif />&nbsp;{{ $text }}
                                </label>&nbsp;
                            @endforeach
 
                            @if ($errors->has('work_status'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('work_status') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row div-to-follow {{ (old('work_status') == '1' ? '' : 'd-none') }}" id="row-sas">
                        <div class="col-md-2">{{ __('Assign') }}<span style="color: red;">*</span></div>

                        <div class="col-md-10">
                            <div class="row" id="main-sas">
                                <div class="col-md-12">
                                    <table class="table table-respopnsive table-bordered" style="margin-bottom: 0;">
                                        <thead>
                                            <tr>
                                                <th width="1%">#</th>
                                                <th width="10%">{{ __('Date') }}</th>
                                                <th width="88%">{{ __('To') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    1
                                                </td>
                                                <td>
                                                    <input id="assign_dates" type="date" class="form-control{{ $errors->has('assign_dates.0') ? ' is-invalid' : '' }}" name="assign_dates[]" value="{{ old('assign_dates.0') }}">

                                                    @if ($errors->has('assign_dates.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('assign_dates.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select id="assign_to" class="form-control {{ $errors->has('assign_to.0') ? ' is-invalid' : '' }} " name="assign_to[]" >
                                                        <option value="">{{ __('Select') }}</option>

                                                        @foreach ($assignTo as $index => $assign)
                                                            <option value="{{ $assign->id }}" {{ (old('assign_to.0') == $assign->id ? 'selected' : '') }}>{{ $assign->first_name . ' ' . $assign->last_name }}</option>
                                                        @endforeach
                                                    </select>

                                                    @if ($errors->has('assign_to.0'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('assign_to.0') }}</strong>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa fa-plus" id="plus-sas" style="cursor: pointer;"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="cloned-sas"></div>

                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary">
                                <i class="metismenu-icon pe-7s-diskette"></i> {{ __('Create') }}
                            </button>
                            <a href="{{ route(($isEditors ? 'editors.index' : 'clients.index')) }}" class="btn btn-danger">
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
