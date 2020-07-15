<?php

namespace App\Http\Controllers;

use App\Role;
use App\BaseModel;
use App\Client;
use App\PurposeArticle;
use App\ClientPurposeArticle;
use App\ClientCondition;
use App\ClientFee;
use App\ClientEmailProgressReport;
use App\ClientPrivateInformation;
use App\ClientDocument;
use App\ClientTermsAndCondition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Models\Audit;
use App\Audit\Tools\AuditMessages;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientExport;

class ClientController extends Controller
{
    private $clientExport, $isEditors;

    public function __construct()
    {
        $this->middleware(['permission:clients_create'])->only(['create','store']);
        $this->middleware(['permission:clients_show'])->only('show');
        $this->middleware(['permission:clients_edit'])->only(['edit','update']);
        $this->middleware(['permission:clients_delete'])->only('destroy');
        $this->middleware(['permission:clients_ban'])->only(['banClient','activateClient']);
        $this->middleware(['permission:clients_activity'])->only('activityLog');

        $this->clientExport = new ClientExport();
        Client::$isEditors  = $this->isEditors = request()->is('editors*');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clientModel = new Client();
        $isFiltered  = false;
        $isExport    = $request->filled('export');

        $cleanup = $request->except(['export']);
        $request->query = new \Symfony\Component\HttpFoundation\ParameterBag($cleanup);

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $clients = Client::query();
        if ($isFiltered) {
            if ($request->get('s', false)) {
                $s = $request->get('s');

                $clients->where(function($query) use($s) {
                            $query->where('first_name','LIKE',"%$s%")
                                  ->orWhere('last_name','LIKE',"%$s%")
                                  ->orWhere(DB::raw('concat(first_name, " ", last_name)'),'LIKE',"%$s%")
                                  ->orWhere('email','LIKE',"%$s%");
                        });
            }

            if ($request->get('dob', false)) {
                $dob = $request->get('dob');

                $clients->where('dob', $dob);
            }

            if ($request->get('rs', false) || $request->get('rst', false)) {
                $rs  = $request->get('rs', false);
                $rs  = ($rs) ? date('Y-m-d', strtotime($rs)) : NULL;
                $rst = $request->get('rst', false);
                $rst = ($rst) ? date('Y-m-d', strtotime($rst)) : NULL;

                if (!empty($rs) && !empty($rst)) {
                    $clients->whereBetween('registration_date', [$rs." 00:00:00", $rst." 23:59:59"]);
                } elseif (!empty($rs)) {
                    $clients->where('registration_date', '>=', $rs." 00:00:00");
                } elseif (!empty($rst)) {
                    $clients->where('registration_date', '<=', $rst." 23:59:59");
                }
            }

            if ($request->get('ws', false)) {
                $ws = $request->get('ws', []);

                $clients->whereIn('work_status', $ws);
            }

            if ($request->get('pur', false)) {
                $pur = $request->get('pur');

                $clients->join(ClientPurposeArticle::getTableName(), ClientPurposeArticle::getTableName() . '.client_id', Client::getTableName() . '.id');
            }
        }

        /*$clients = $clientModel::orderBy('id','ASC')
                        ->when($isFiltered, function($query) use ($request){
                            $s   = $request->get('s', false);
                            $dob = $request->get('dob', false);

                            if ($dob) {
                                $query = $query->where('dob', $dob);
                            }

                            if ($s) {
                                $query = $query->where('first_name','LIKE',"%$s%")
                                             ->orWhere('last_name','LIKE',"%$s%")
                                             ->orWhere(DB::raw('concat(first_name, " ", last_name)'),'LIKE',"%$s%")
                                             ->orWhere('email','LIKE',"%$s%");
                            }

                            return $query;
                        })
                        ->when($request->filled('dob'), function($query) use ($request) {
                            $term = $request->get('dob');

                            return $query->where('dob', $term);
                        })
                        ->when($request->filled('rs'), function($query) use ($request) {
                            $term = $request->get('rs');
                            dd($term);
                        })
                        ->when($request->has('new'), function($query){
                            $now = Carbon::now();
                            $monthAgo = $now->copy()->subMonth();
                            return $query->where('id', '!=', 1)->whereBetween('created_at', [$monthAgo->format('Y-m-d 00:00:00'), $now->format('Y-m-d 23:59:59')]);
                        })
                        ->when($request->has('active'), function($query){
                            return $query->where('banned', 0)->whereNotNull('email_verified_at');
                        })
                        ->when($request->has('banned'), function($query){
                            return $query->where('banned', 1);
                        })
                        ->paginate(20);*/

        $clients->select(Client::getTableName() . '.*')
                ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved);

        if ($isExport) {
            $this->clientExport->collection = $clients->get();

            return $this->exportCSV();
        }

        $clients = $clients->paginate(20);

        return view('app.clients.list', ['clients' => $clients, 'term' => $request, 'request' => $request, 'clientModel' => $clientModel, 'isFiltered' => $isFiltered]);
    }


    /**
     * Export to csv
     */
    public function exportCSV()
    {
        if (empty($this->clientExport->collection())) {
            return false;
        }

        return Excel::download($this->clientExport, 'list.csv');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientModel     = new Client();
        $roles           = Role::orderBy('id', 'ASC')->get();
        $editorRoles     = Role::whereRaw("lower(name) = 'editor'")->first();
        $assignTo        = [];
        $purposeArticles = PurposeArticle::where('is_removed', PurposeArticle::$notRemoved)->get();

        if (!empty($editorRoles)) {
            $assignTo = $editorRoles->users;
        }

        return view('app.clients.create', ['roles' => $roles, 'assignTo' => $assignTo, 'purposeArticles' => $purposeArticles, 'clientModel' => $clientModel]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $assignTo = $assignDate = ['nullable'];
        if (isset($data['work_status']) && $data['work_status'] == 1) {
            $assignTo = $assignDate = ['required'];
        }

        $validator = Validator::make($data, [
            'registration_date' => ['required', 'date_format:Y-m-d'],
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            // 'email'             => ['required', 'string', 'email', 'max:255', 'unique:' . Client::getTableName() . ',email'],
            // 'secondary_email'   => ['string', 'nullable', 'email', 'max:255', 'unique:' . Client::getTableName() . ',secondary_email'],
            'dob'               => ['date_format:Y-m-d', 'nullable'],
            'contact'           => ['string', 'nullable', 'not_regex:/[#$%^&*+=\\[\]\';,\/{}|":<>?~\\\\]/'],
            'passport_number'   => ['string', 'nullable'],
            'process_address'   => ['string', 'nullable'],
            'nationality'       => ['string', 'nullable'],
            'work_status'       => ['string', 'nullable', 'in:0,1,2'],
            'photo'             => ['string', 'nullable'],
            'banned'            => ['integer', 'in:0,1'],
            'assign_date'       => array_merge(['date_format:Y-m-d'], $assignDate),
            'assign_to'         => array_merge(['integer', 'exists:' . Client::getTableName() . ',id'], $assignTo),
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'is_superadmin'     => ['in:0']
        ]);

        $validator->validate();

        $client = CLient::create([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            // 'email'           => $data['email'],
            // 'secondary_email' => (!empty($data['secondary_email'])) ? $data['secondary_email'] : NULL,
            'dob'             => (!empty($data['dob'])) ? $data['dob'] : NULL,
            'contact'         => (!empty($data['contact'])) ? $data['contact'] : NULL,
            'passport_number' => (!empty($data['passport_number'])) ? $data['passport_number'] : NULL,
            'process_address' => (!empty($data['process_address'])) ? $data['process_address'] : NULL,
            'nationality'     => (!empty($data['nationality'])) ? $data['nationality'] : NULL,
            'work_status'     => (isset($data['work_status'])) ? $data['work_status'] : NULL,
            'photo'           => (!empty($data['photo'])) ? $data['photo'] : NULL,
            'banned'          => (isset($data['banned'])) ? $data['banned'] : '0',
            'assign_date'     => (!empty($data['assign_date'])) ? $data['assign_date'] : NULL,
            'assign_to'       => (!empty($data['assign_to'])) ? $data['assign_to'] : NULL,
            'password'        => Hash::make($data['password']),
            'is_superadmin'   => '0'
        ]);

        if ($client) {
            $clientId = $client->id;

            $this->storeOtherInfos($clientId, $request);

            // Send verification email
            if (setting('auth.email_verification')) {
                $client->sendEmailVerificationNotification();
            }

            //Assign role
            if (isset($data['role_id']) && $data['role_id'] != "") {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $client->assignRole($role);
                }
            }

            return redirect('clients')->with('success', __("Client created!"));
        } else {
            return redirect('clients/create')->with('error', __("There has been an error!"));
        }
    }

    public function storeOtherInfos(int $clientId, Request $request, $operation = 'insert')
    {
        if (!empty($clientId)) {
            // Purpose and articles.
            $purposeAarticals = $request->get('purpose_articles', []);

            // Remove old records.
            if ($operation == 'update') {
                $find = ClientPurposeArticle::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->update(['is_removed' => BaseModel::$removed]);
            }

            if (!empty($purposeAarticals)) {
                foreach ((array)$purposeAarticals as $index => $purposeAartical) {
                    $data = [];

                    $data[$index] = [
                        'purpose_article_id' => $purposeAartical,
                        'client_id'          => $clientId,
                        'is_removed'         => BaseModel::$notRemoved
                    ];

                    $validator = ClientPurposeArticle::validators($data[$index], true);
                    if ($validator) {
                        ClientPurposeArticle::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            }

            // Client conditions.
            $clientConditionDates = $request->get('condition_dates', []);
            $clientConditions     = $request->get('conditions', []);
            // $existsIds            = $request->get('id_client_conditions', []);

            // Remove old records.
            if ($operation == 'update') {
                $find = ClientCondition::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->update(['is_removed' => BaseModel::$removed]);
            }

            if (!empty($clientConditionDates)) {
                foreach ((array)$clientConditionDates as $index => $clientConditionDate) {
                    $data = [];

                    $data[$index] = [
                        'date'       => $clientConditionDate,
                        'condition'  => (!empty($clientConditions[$index]) ? $clientConditions[$index] : NULL),
                        'client_id'  => $clientId,
                        'is_removed' => BaseModel::$notRemoved
                    ];

                    $validator = ClientCondition::validators($data[$index], true);
                    if ($validator) {
                        ClientCondition::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            }

            // Client fees.
            $feeDates               = $request->get('fee_dates', []);
            $proposedLawyerFees     = $request->get('total_proposed_lawyer_fee', []);
            $receivedLawyerFees     = $request->get('received_lawyer_fee', []);
            $missingLawyerFees      = $request->get('missing_lawyer_fee', []);
            $proposedGovernmentFees = $request->get('total_proposed_government_fee', []);
            $receivedGovernmentFees = $request->get('received_government_fee', []);
            $missingGovernmentFees  = $request->get('missing_government_fee', []);

            // Remove old records.
            if ($operation == 'update') {
                $find = ClientFee::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->update(['is_removed' => BaseModel::$removed]);
            }

            if (!empty($feeDates)) {
                foreach ((array)$feeDates as $index => $feeDate) {
                    $data = [];

                    $data[$index] = [
                        'date'                    => $feeDate,
                        'proposed_lawyer_fee'     => (!empty($proposedLawyerFees[$index]) ? $proposedLawyerFees[$index] : NULL),
                        'received_lawyer_fee'     => (!empty($receivedLawyerFees[$index]) ? $receivedLawyerFees[$index] : NULL),
                        'missing_lawyer_fee'      => (!empty($missingLawyerFees[$index]) ? $missingLawyerFees[$index] : NULL),
                        'proposed_government_fee' => (!empty($proposedGovernmentFees[$index]) ? $proposedGovernmentFees[$index] : NULL),
                        'received_government_fee' => (!empty($receivedGovernmentFees[$index]) ? $receivedGovernmentFees[$index] : NULL),
                        'missing_government_fee'  => (!empty($missingGovernmentFees[$index]) ? $missingGovernmentFees[$index] : NULL),
                        'client_id'               => $clientId,
                        'is_removed'              => BaseModel::$notRemoved
                    ];

                    $validator = ClientFee::validators($data[$index], true);
                    if ($validator) {
                        ClientFee::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            }

            // Progress report emails.
            $progressReportDates = $request->get('progress_report_dates', []);
            $progressReports     = $request->get('progress_reports', []);
            $progressReportFiles = $request->file('progress_report_files', []);
            $existsIds           = $request->get('id_client_email_progress_reports', []);

            // Remove old records.
            if ($operation == 'update') {
                // $find = ClientEmailProgressReport::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->update(['is_removed' => BaseModel::$removed]);
            }

            if (!empty($progressReportDates)) {
                foreach ((array)$progressReportDates as $index => $progressReportDate) {
                    $data = [];

                    $data[$index] = [
                        'date'            => $progressReportDate,
                        'progress_report' => (!empty($progressReports[$index]) ? $progressReports[$index] : NULL),
                        'file'            => '',
                        'client_id'       => $clientId,
                        'is_removed'      => BaseModel::$notRemoved
                    ];

                    if ($operation == 'update' && !empty($existsIds[$index])) {
                        $find = ClientEmailProgressReport::where(['id' => $existsIds[$index], 'client_id' => $clientId]);

                        if (!empty($find)) {
                            $updated = $find->first();
                            $find->update(['is_removed' => BaseModel::$removed]);

                            if (!empty($updated) && !empty($updated->getAttributes()['file'])) {
                                $data[$index]['file'] = $updated->getAttributes()['file'];
                            }
                        }

                        unset($existsIds[$index]);
                    }

                    $validator = ClientEmailProgressReport::validators($data[$index], true);
                    if ($validator) {
                        $progressReport   = ClientEmailProgressReport::updateOrCreate($data[$index], $data[$index]);
                        $progressReportId = $progressReport->id;

                        if (!empty($progressReportFiles[$index]) && $progressReportFiles[$index] instanceof UploadedFile ) {
                            $pathInfos = pathinfo($progressReportFiles[$index]->getClientOriginalName());

                            if (!empty($pathInfos['extension'])) {
                                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $clientId . '_' . $progressReportId . '.' . $pathInfos['extension'];
                                $storeFile = $progressReportFiles[$index]->storeAs('client' . '\\' . $clientId . '\\'. ClientEmailProgressReport::$storageFolderName, $fileName, ClientEmailProgressReport::$fileSystem);

                                if ($storeFile) {
                                    ClientEmailProgressReport::find($progressReportId)->update(['file' => $fileName]);
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($existsIds)) {
                ClientEmailProgressReport::whereIn('id', $existsIds)->update(['is_removed' => BaseModel::$removed]);
            }

            // Client private informations.
            $privateDates        = $request->get('client_private_dates', []);
            $privateInformations = $request->get('client_private_informations', []);

            // Remove old records.
            if ($operation == 'update') {
                $find = ClientPrivateInformation::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->update(['is_removed' => BaseModel::$removed]);
            }

            if (!empty($privateDates)) {
                foreach ((array)$privateDates as $index => $privateDate) {
                    $data = [];

                    $data[$index] = [
                        'date'                => $privateDate,
                        'private_information' => (!empty($privateInformations[$index]) ? $privateInformations[$index] : NULL),
                        'client_id'           => $clientId,
                        'is_removed'          => BaseModel::$notRemoved
                    ];

                    $validator = ClientPrivateInformation::validators($data[$index], true);
                    if ($validator) {
                        ClientPrivateInformation::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            }

            // Client documents
            $clientDocuments = $request->file('client_documents', []);
            $existsIds       = $request->get('id_client_documents', []);
            $existsOldIds    = $request->get('id_client_documents_old', []);

            if (!empty($existsIds)) {
                foreach ((array)$existsIds as $index => $existsId) {
                    $data           = [];
                    $clientDocument = (!empty($clientDocuments[$index])) ? $clientDocuments[$index] : [];
                    $fileName       = NULL;

                    if ($operation == 'update' && isset($existsOldIds[$existsId])) {
                        $find = ClientDocument::where(['id' => $existsId, 'client_id' => $clientId]);

                        if (!empty($find)) {
                            $updated = $find->first();
                            $find->update(['is_removed' => BaseModel::$removed]);

                            if (!empty($updated) && !empty($updated->getAttributes()['file'])) {
                                $fileName = $updated->getAttributes()['file'];
                            }
                        }
                    }

                    if (!empty($clientDocument) && $clientDocument instanceof UploadedFile) {
                        $pathInfos = pathinfo($clientDocument->getClientOriginalName());

                        if (!empty($pathInfos['extension'])) {
                            $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $clientId . '.' . $pathInfos['extension'];
                            $storeFile = $clientDocument->storeAs('client' . '\\' . $clientId . '\\'. ClientDocument::$storageFolderName, $fileName, ClientDocument::$fileSystem);

                            if ($storeFile) {
                                $data[$index] = [
                                    'file'       => $fileName,
                                    'client_id'  => $clientId,
                                    'is_removed' => BaseModel::$notRemoved
                                ];

                                ClientDocument::updateOrCreate($data[$index]);
                            }
                        }
                    } elseif (!empty($fileName)) {
                        $data[$index] = [
                            'file'       => $fileName,
                            'client_id'  => $clientId,
                            'is_removed' => BaseModel::$notRemoved
                        ];

                        ClientDocument::updateOrCreate($data[$index]);
                    }
                }
            }

            // Terms and conditions.
            $termsAndConditions = $request->get('terms_and_conditions', []);

            // Remove old records.
            if ($operation == 'update') {
                $find = ClientTermsAndCondition::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->update(['is_removed' => BaseModel::$removed]);
            }

            if (!empty($termsAndConditions)) {
                foreach ((array)$termsAndConditions as $index => $termsAndCondition) {
                    $data = [];

                    $data[$index] = [
                        'terms_and_conditions' => $termsAndCondition,
                        'client_id'            => $clientId,
                        'is_removed'           => BaseModel::$notRemoved
                    ];

                    $validator = ClientTermsAndCondition::validators($data[$index], true);
                    if ($validator) {
                        ClientTermsAndCondition::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::find($id);

        if ($client) {
            $permissions = app('App\Http\Controllers\RoleController')->getPermissionsByGroup();

            return view('app.clients.show', ['client' => $client, 'groups' => $permissions]);
        } else {
            return redirect('clients')->with('error',__("Client not found!"));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::find($id);

        if ($client) {
            $roles           = Role::orderBy('id', 'ASC')->get();
            $editorRoles     = Role::whereRaw("lower(name) = 'editor'")->first();
            $assignTo        = [];
            $purposeArticles = PurposeArticle::where('is_removed', PurposeArticle::$notRemoved)->get();

            if (!empty($editorRoles)) {
                $assignTo = $editorRoles->users;
            }

            return view('app.clients.edit', ['client' => $client, 'roles' => $roles, 'assignTo' => $assignTo, 'purposeArticles' => $purposeArticles]);
        } else {
            if ($this->isEditors) {
                return redirect('editors')->with('error',__("Editor not found!"));
            } else {
                return redirect('clients')->with('error',__("Client not found!"));
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if ($client) {
            // Prevent super admin client from updation
            if ($client->isSuperAdmin()) {
                if ($this->isEditors) {
                    return redirect('editors')->with('error',__("Super admin cannot be update!"));
                } else {
                    return redirect('clients')->with('error',__("Super admin cannot be update!"));
                }
            }

            $data = $request->all();

            if (!isset($data['password']) || $data['password'] === null) {
                unset($data['password']);
                unset($data['password_confirmation']);
            }

            $assignTo = $assignDate = ['nullable'];
            if (isset($data['work_status']) && $data['work_status'] == 1) {
                $assignTo = $assignDate = ['required'];
            }

            $validator = Validator::make($data, [
                'registration_date' => ['required', 'date_format:Y-m-d'],
                'first_name'        => ['required', 'string', 'max:255'],
                'last_name'         => ['required', 'string', 'max:255'],
                'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('clients')->ignore($client, 'email')],
                'secondary_email'   => ['string', 'nullable', 'email', 'max:255', Rule::unique('clients')->ignore($client, 'secondary_email')],
                'dob'               => ['date_format:Y-m-d', 'nullable'],
                'contact'           => ['string', 'nullable', 'not_regex:/[#$%^&*+=\\[\]\';,\/{}|":<>?~\\\\]/'],
                'passport_number'   => ['string', 'nullable'],
                'process_address'   => ['string', 'nullable'],
                'nationality'       => ['string', 'nullable'],
                'work_status'       => ['string', 'nullable', 'in:0,1,2'],
                'photo'             => ['string', 'nullable'],
                'banned'            => ['integer', 'in:0,1'],
                'assign_date'       => array_merge(['date_format:Y-m-d'], $assignDate),
                'assign_to'         => array_merge(['integer', 'exists:' . Client::getTableName() . ',id'], $assignTo),
                'password'          => ['string', 'min:8', 'confirmed'],
                'is_superadmin'     => ['in:0']
            ]);

            $validator->validate();

            if (isset($data['password']) && $data['password'] !== null) {
                $data['password'] = Hash::make($data['password']);
            }
            if (empty($data['assign_date'])) {
                unset($data['assign_date']);
            }

            if ($client->update($data)) {
                $this->storeOtherInfos($id, $request, 'update');

                // Update role
                if (!$client->isSuperAdmin()) {
                    if (isset($data['role_id']) && $data['role_id'] != "") {
                        $role = Role::find($data['role_id']);

                        // Check if the posted role_id same with client's current role
                        // if not revoke the old role and assign a new one
                        if ($role && !$client->hasRole($role)) {

                            // Check if the client has any role
                            if (!$client->hasAnyRole(Role::all())) {
                                $client->assignRole($role);
                            } else {
                                $currentRole = $client->getRoleNames()[0];
                                $client->removeRole($currentRole);
                                $client->assignRole($role);
                            } 
                        }
                    }
                }

                if ($this->isEditors) {
                    return redirect('editors/'.$id)->with('success',__("Editor updated!"));
                } else {
                    return redirect('clients/'.$id)->with('success',__("Client updated!"));
                }
            } else {
                if ($this->isEditors) {
                    return redirect('editors/'.$id)->with('error',__("There has been an error!"));
                } else {
                    return redirect('clients/'.$id)->with('error',__("There has been an error!"));
                }
            }
        } else {
            if ($this->isEditors) {
                return redirect('editors')->with('error',__("Editor not found!"));
            } else {
                return redirect('clients')->with('error',__("Client not found!"));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if ($client) {
            // Prevent super admin client from deletion
            if ($client->isSuperAdmin()) {
                if ($this->isEditors) {
                    return redirect('editors')->with('error',__("Super admin cannot be deleted!"));
                } else {
                    return redirect('clients')->with('error',__("Super admin cannot be deleted!"));
                }
            }

            $client->audits()->delete();
            // $client->delete();
            $client->update(['is_removed' => BaseModel::$removed]);

            if ($this->isEditors) {
                return redirect('editors')->with('success',__("Editor deleted!"));
            } else {
                return redirect('clients')->with('success',__("Client deleted!"));
            }
        } else {
            if ($this->isEditors) {
                return redirect('editors')->with('error',__("Editor not found!"));
            } else {
                return redirect('clients')->with('error',__("Client not found!"));
            }
        }
    }

    /**
     * Resend the email verification link
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resendVerificationLink($id)
    {
        $client = Client::find($id);

        if ($client) {
            $client->sendEmailVerificationNotification();

            return redirect('clients/'.$id)->with('success',__("A fresh verification link has been sent to client email address."));
        } else {
            return redirect('clients')->with('error',__("Client not found!"));
        }
    }

    /**
     * Ban client from the application
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function banClient($id)
    {
        $client = Client::find($id);

        if ($client) {
            // Prevent super admin client from being banned
            if ($client->isSuperAdmin()) {
                if ($this->isEditors) {
                    return redirect('editors/'.$id)->with('error',__("Super admin editor cannot be banned!"));
                } else {
                    return redirect('clients/'.$id)->with('error',__("Super admin client cannot be banned!"));
                }
            }

            $client->banned = true;
            $client->save();

            return redirect('clients/'.$id)->with('success',__("Client banned!"));
        } else {
            return redirect('clients')->with('error',__("Client not found!"));
        }
    }

    /**
     * Activate banned client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activateClient($id)
    {
        $client = Client::find($id);

        if ($client) {
            $client->banned = false;
            $client->save();

            return redirect('clients/'.$id)->with('success',__("Client account activated!"));
        } else {
            return redirect('clients')->with('error',__("Client not found!"));
        }
    }

    /**
     * Show user's activities
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activityLog($id)
    {
        $client = Client::find($id);

        if ($client) {
            $audits = Audit::where('new_values','NOT LIKE','%remember_token%')->where('user_id', $client->id)->orderBy('created_at','DESC')->paginate(20);

            $audits = AuditMessages::get($audits);

            return view('app.clients.activity', ['client' => $client, 'audits' => $audits]);
        } else {
            return redirect('clients')->with('error',__("Client not found!"));
        }
    }

    /**
     * Update profile photo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto(Request $request, $id){
        $user = User::find($id);

        if($user){
            $data = $request->image;

            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);

            $data = base64_decode($data);
            $image_name = time().'.png';
            $path = storage_path() . "/app/avatars/" . $image_name;

            file_put_contents($path, $data);

            $user->photo = $image_name;
            $user->save();

            return response()->json(['success'=>'done']);
        } else {
            return response()->json(['error'=>__("User not found!")]);
        }
    }

    /**
     * Delete profile photo.
     *
     * @return \Illuminate\Http\Response
     */
    public function deletePhoto($id){
        $user = User::find($id);

        if($user){
            $user->photo = null;
            $user->save();

            return redirect('users/'.$id)->with('success',__("User profile photo deleted!"));
        } else {
            return response()->json(['error'=>__("User not found!")]);
        }
    }

    public function print($id)
    {
        $clientModel = new Client();

        $client = $clientModel->find($id);

        return view('app.clients.print', ['client' => $client]);
    }
}
