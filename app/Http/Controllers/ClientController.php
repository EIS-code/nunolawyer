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
use App\ModelHasRoles;
use App\Account;
use App\FollowUp;
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
use Illuminate\Pagination\LengthAwarePaginator;

class ClientController extends Controller
{
    private $clientExport, $isEditors, $isViewClients;

    public function __construct()
    {
        $this->clientExport    = new ClientExport();
        Client::$isEditors     = $this->isEditors = request()->is('editors*');
        Client::$isViewClients = $this->isViewClients = request()->is('clients/view');

        if ($this->isEditors) {
            $this->middleware(['permission:editors_create'])->only(['create','store']);
            $this->middleware(['permission:editors_show'])->only('show');
            $this->middleware(['permission:editors_edit'])->only(['edit','update']);
            $this->middleware(['permission:editors_delete'])->only('destroy');
            $this->middleware(['permission:editors_ban'])->only(['banEditor','activateEditor']);
            $this->middleware(['permission:editors_activity'])->only('activityLog');
            $this->middleware(['permission:editors_profile_access'])->only(['profile']);
        } else {
            $this->middleware(['permission:clients_create'])->only(['create','store']);
            $this->middleware(['permission:clients_show'])->only('show');
            $this->middleware(['permission:clients_edit'])->only(['edit','update']);
            $this->middleware(['permission:clients_delete'])->only('destroy');
            $this->middleware(['permission:clients_ban'])->only(['banClient','activateClient']);
            $this->middleware(['permission:clients_activity'])->only('activityLog');
            $this->middleware(['permission:clients_activity_own'])->only('activityLog');
            $this->middleware(['permission:clients_profile_access'])->only(['profile']);
        }
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

        $clients = [];
        if (!$this->isViewClients || $isFiltered) {
            $clients = Client::query();

            if ($isFiltered) {
                if ($request->get('s', false)) {
                    $s = $request->get('s');

                    $clients->where(function($query) use($s) {
                                $query->where('first_name','LIKE',"%$s%")
                                      ->orWhere('last_name','LIKE',"%$s%")
                                      ->orWhere(DB::raw('concat(first_name, " ", last_name)'),'LIKE',"%$s%")
                                      ->orWhere('email','LIKE',"%$s%")
                                      ->orWhere('contact','LIKE',"%$s%");
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

                    $clients->join(ClientPurposeArticle::getTableName(), function($join) use($pur) {
                        $join->on(Client::getTableName() . '.id', '=', ClientPurposeArticle::getTableName() . '.client_id')
                             ->where(ClientPurposeArticle::getTableName() . '.purpose_article_id', (int)$pur)
                             ->where(ClientPurposeArticle::getTableName() . '.is_removed', BaseModel::$notRemoved);
                    });
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
                    ->join(ModelHasRoles::getTableName(), function($join) {
                        $join->on(ModelHasRoles::getTableName() . '.model_id', '=', Client::getTableName() . '.id');
                    })
                    ->join(Role::getTableName(), function($join) {
                        $join->on(ModelHasRoles::getTableName() . '.role_id', '=', Role::getTableName() . '.id');
                    })
                    ->whereRaw('lower(' . Role::getTableName() . '.name) = "client"')
                    ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved)
                    ->where(Client::getTableName() . '.id', '!=', \Auth::user()->id)
                    ->orderBy(Client::getTableName() . '.id', 'DESC');

            if ($isExport) {
                $this->clientExport->collection = $clients->get();

                return $this->exportCSV();
            }

            $clients = $clients->paginate(20);
        }

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

        if (empty($data['password_2'])) {
            $data['password_2'] = '';
        }

        $validator = Validator::make($data, [
            'registration_date' => ['required', 'date_format:Y-m-d'],
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:' . Client::getTableName() . ',email'],
            'secondary_email'   => ['string', 'nullable', 'email', 'max:255', 'unique:' . Client::getTableName() . ',secondary_email'],
            'dob'               => ['date_format:Y-m-d', 'nullable'],
            'contact'           => ['string', 'nullable', 'not_regex:/[#$%^&*+=\\[\]\';,\/{}|":<>?~\\\\]/'],
            'secondary_contact' => ['string', 'nullable', 'not_regex:/[#$%^&*+=\\[\]\';,\/{}|":<>?~\\\\]/'],
            'passport_number'   => ['string', 'nullable'],
            'process_address'   => ['string', 'nullable'],
            'nationality'       => ['required', 'string', 'nullable'],
            'work_status'       => ['string', 'nullable', 'in:0,1,2'],
            'photo'             => ['string', 'nullable'],
            'banned'            => ['integer', 'in:0,1'],
            // 'assign_date'       => array_merge(['date_format:Y-m-d'], $assignDate),
            // 'assign_to'         => array_merge(['integer', 'exists:' . Client::getTableName() . ',id'], $assignTo),
            'password'          => ['required', 'string', 'min:8'],
            'password_2'        => ['string', 'min:8'],
            'is_superadmin'     => ['in:0'],
            'purpose_articles'  => ['required']
        ]);

        $validator->validate();

        $client = CLient::create([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'],
            'secondary_email' => (!empty($data['secondary_email'])) ? $data['secondary_email'] : NULL,
            'dob'             => (!empty($data['dob'])) ? $data['dob'] : NULL,
            'contact'         => (!empty($data['contact'])) ? $data['contact'] : NULL,
            'secondary_contact' => (!empty($data['secondary_contact'])) ? $data['secondary_contact'] : NULL,
            'passport_number' => (!empty($data['passport_number'])) ? $data['passport_number'] : NULL,
            'process_address' => (!empty($data['process_address'])) ? $data['process_address'] : NULL,
            'nationality'     => (!empty($data['nationality'])) ? $data['nationality'] : NULL,
            'work_status'     => (isset($data['work_status'])) ? $data['work_status'] : NULL,
            'photo'           => (!empty($data['photo'])) ? $data['photo'] : NULL,
            'banned'          => (isset($data['banned'])) ? $data['banned'] : '0',
            // 'assign_date'     => (!empty($data['assign_date'])) ? $data['assign_date'] : NULL,
            // 'assign_to'       => (!empty($data['assign_to'])) ? $data['assign_to'] : NULL,
            'password'        => Hash::make($data['password']),
            'password_2'      => (!empty($data['password_2'])) ? Hash::make($data['password_2']) : '',
            'password_text'   => $data['password'],
            'password_text_2' => (!empty($data['password_2'])) ? $data['password_2'] : '',
            'is_superadmin'   => '0'
        ]);

        if ($client) {
            $clientId = $client->id;

            $assignTo    = (!empty($data['assign_to'])) ? $data['assign_to'] : [];
            $assignDates = (!empty($data['assign_dates'])) ? $data['assign_dates'] : [];
            $existsIds   = (!empty($data['id_follow_up'])) ? $data['id_follow_up'] : [];
            $this->storeAssignTo($clientId, $assignTo, $assignDates, $existsIds);
            /*if (!empty($assignTo)) {
                $this->storeAssignTo($clientId, $assignTo, $assignDates, $existsIds);
            }*/

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

            if ($this->isEditors) {
                return redirect('editors')->with('success', __("Editor created!"));
            } else {
                return redirect('clients')->with('success', __("Client created!"));
            }
        } else {
            if ($this->isEditors) {
                return redirect('editors/create')->with('error', __("There has been an error!"));
            } else {
                return redirect('clients/create')->with('error', __("There has been an error!"));
            }
        }
    }

    public function storeAssignTo(int $clientId, array $assignTo, array $assignDates, array $existsIds, $operation = 'insert')
    {
        $create = function($index, $existsId = null) use($assignTo, $assignDates, $clientId, $operation) {
            if (!isset($index)) {
                return false;
            }

            $data       = [];
            $assignDate = (isset($assignDates[$index])) ? $assignDates[$index] : false;

            $data[$index] = [
                'date'        => $assignDate,
                'follow_by'   => $assignTo[$index],
                'follow_from' => \Auth::user()->id,
                'client_id'   => $clientId,
                'is_removed'  => BaseModel::$notRemoved
            ];

            $validator = FollowUp::validators($data[$index], true);
            if ($validator) {
                $update = NULL;
                if (!empty($existsId)) {
                    $update = FollowUp::find($existsId);
                }

                if (!empty($update)) {
                    $update->update($data[$index]);
                } else {
                    FollowUp::updateOrCreate($data[$index], $data[$index]);
                }
            }
        };

        if (!empty($clientId) && !empty($assignTo)) {
            $remove = [];

            if (!empty($existsIds)) {
                foreach ($existsIds as $index => $existsId) {
                    if (!empty($assignDates[$index])) {
                        unset($assignDates[$index]);

                        $create($index, $existsId);
                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($assignDates)) {
                foreach ($assignDates as $index => $assignee) {
                    $create($index);
                }
            }

            if (!empty($remove)) {
                $find = FollowUp::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }
        } else {
            $data[$index] = [
                'date'        => '0000-00-00',
                'follow_by'   => '0',
                'follow_from' => \Auth::user()->id,
                'client_id'   => $clientId,
                'is_removed'  => BaseModel::$notRemoved
            ];
        }
    }

    public function storeOtherInfos(int $clientId, Request $request, $operation = 'insert')
    {
        if (!empty($clientId)) {
            // Purpose and articles.
            $purposeAarticals = $request->get('purpose_articles', []);
            $lastSelectedId   = $request->get('last_purpose_articles', '0');

            $find    = ClientPurposeArticle::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->get();
            $new     = $purposeAarticals;
            if (!empty($find) && !$find->isEmpty()) {
                $records = $find->pluck('purpose_article_id')->toArray();

                $remove = array_diff($records, $purposeAarticals);
                $new    = array_diff($purposeAarticals, $records);
            }

            if (!empty($new)) {
                foreach ((array)$new as $index => $purposeAartical) {
                    $data = [];

                    $isLastInserted = ($purposeAartical == $lastSelectedId) ? '1' : '0';

                    $data[$index] = [
                        'purpose_article_id' => $purposeAartical,
                        'client_id'          => $clientId,
                        'is_removed'         => BaseModel::$notRemoved,
                        'is_last_inserted'   => $isLastInserted
                    ];

                    $validator = ClientPurposeArticle::validators($data[$index], true);
                    if ($validator) {
                        ClientPurposeArticle::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            }
            if (!empty($remove)) {
                $find = ClientPurposeArticle::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('purpose_article_id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }

            // Client conditions.
            $clientConditionDates = $request->get('condition_dates', []);
            $clientConditions     = $request->get('conditions', []);
            $existsIds            = $request->get('id_client_conditions', []);
            $remove               = [];

            $create = function($index, $existsId = null) use($clientConditionDates, $clientConditions, $clientId) {
                if (!isset($index)) {
                    return false;
                }

                $data = [];

                $data[$index] = [
                    'date'       => $clientConditionDates[$index],
                    'condition'  => $clientConditions[$index],
                    'client_id'  => $clientId,
                    'is_removed' => BaseModel::$notRemoved
                ];

                $validator = ClientCondition::validators($data[$index], true);
                if ($validator) {
                    $update = NULL;
                    if (!empty($existsId)) {
                        $update = ClientCondition::find($existsId);
                    }

                    if (!empty($update)) {
                        $update->update($data[$index]);
                    } else {
                        ClientCondition::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            };

            if (!empty($existsIds)) {
                foreach ($existsIds as $index => $existsId) {
                    if (!empty($clientConditionDates[$index])) {

                        unset($clientConditionDates[$index]);
                        $create($index, $existsId);

                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($clientConditionDates)) {
                foreach ($clientConditionDates as $index => $clientConditionDate) {
                    $create($index);
                }
            }
            if (!empty($remove)) {
                $find = ClientCondition::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }

            // Client fees.
            $feeDates               = $request->get('fee_dates', []);
            $proposedLawyerFees     = $request->get('total_proposed_lawyer_fee', []);
            $receivedLawyerFees     = $request->get('received_lawyer_fee', []);
            $missingLawyerFees      = $request->get('missing_lawyer_fee', []);
            $proposedGovernmentFees = $request->get('total_proposed_government_fee', []);
            $receivedGovernmentFees = $request->get('received_government_fee', []);
            $missingGovernmentFees  = $request->get('missing_government_fee', []);
            $existsIds              = $request->get('id_client_fees', []);
            $remove                 = [];

            $create = function($index, $count, $inc, $existsId = null) use($feeDates, $proposedLawyerFees, $receivedLawyerFees, $missingLawyerFees, $proposedGovernmentFees, $receivedGovernmentFees, $missingGovernmentFees, $clientId) {
                if (!isset($index)) {
                    return false;
                }

                $data = [];

                $receivedLawyerFee      = (!empty($receivedLawyerFees[$index]) ? $receivedLawyerFees[$index] : NULL);
                $receivedGovernmentFee  = (!empty($receivedGovernmentFees[$index]) ? $receivedGovernmentFees[$index] : NULL);
                $data[$index] = [
                    'date'                    => $feeDates[$index],
                    'proposed_lawyer_fee'     => (!empty($proposedLawyerFees[$index]) ? $proposedLawyerFees[$index] : NULL),
                    'received_lawyer_fee'     => $receivedLawyerFee,
                    'missing_lawyer_fee'      => (!empty($missingLawyerFees[$index]) ? $missingLawyerFees[$index] : NULL),
                    'proposed_government_fee' => (!empty($proposedGovernmentFees[$index]) ? $proposedGovernmentFees[$index] : NULL),
                    'received_government_fee' => $receivedGovernmentFee,
                    'missing_government_fee'  => (!empty($missingGovernmentFees[$index]) ? $missingGovernmentFees[$index] : NULL),
                    'client_id'               => $clientId,
                    'is_removed'              => BaseModel::$notRemoved
                ];

                $validator = ClientFee::validators($data[$index], true);
                if ($validator) {
                    $update = ClientFee::find($existsId);

                    if (!empty($update)) {
                        $update->update($data[$index]);
                    } else {
                        ClientFee::updateOrCreate($data[$index], $data[$index]);
                    }
                }

                if ($inc == ($count - 1)) {
                    $accountData = [];

                    // Get last purpose article id.
                    $getLastPurposeArticleId = ClientPurposeArticle::where(['is_removed' => BaseModel::$notRemoved, 'is_last_inserted' => '1', 'client_id' => $clientId])->first();

                    if (!empty($getLastPurposeArticleId)) {
                        $accountData['created_by']          = \Auth::user()->id;
                        $accountData['client_id']           = $clientId;
                        $accountData['date']                = $feeDates[$index];
                        $accountData['received_amount']     = ($receivedLawyerFee + $receivedGovernmentFee);
                        $accountData['purpose_article_id']  = ($getLastPurposeArticleId->purpose_article_id);
                    }

                    $validator = Account::validators($accountData, true);
                    if ($validator) {
                        Account::updateOrCreate($accountData, $accountData);
                    }
                }
            };

            if (!empty($existsIds)) {
                $inc   = 0;
                $count = count($existsIds);
                foreach ($existsIds as $index => $existsId) {
                    if (!empty($feeDates[$index])) {

                        unset($feeDates[$index]);

                        $create($index, $count, $inc, $existsId);

                        $inc++;
                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($feeDates)) {
                $inc   = 0;
                $count = count($feeDates);
                foreach ($feeDates as $index => $feeDate) {
                    $create($index, $count, $inc);

                    $inc++;
                }
            }
            if (!empty($remove)) {
                $find = ClientFee::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }

            // Progress report emails.
            $progressReportDates = $request->get('progress_report_dates', []);
            $progressReports     = $request->get('progress_reports', []);
            $progressReportFiles = $request->file('progress_report_files', []);
            $existsIds           = $request->get('id_client_email_progress_reports', []);
            $remove              = [];

            $create = function($index, $existsId = null) use($progressReportDates, $progressReports, $progressReportFiles, $clientId, $operation) {
                if (!isset($index)) {
                    return false;
                }

                $data = [];

                $data[$index] = [
                    'date'            => $progressReportDates[$index],
                    'progress_report' => (!empty($progressReports[$index]) ? $progressReports[$index] : NULL),
                    'file'            => '',
                    'client_id'       => $clientId,
                    'is_removed'      => BaseModel::$notRemoved
                ];

                if ($operation == 'update' && !empty($existsId)) {
                    $find = ClientEmailProgressReport::where(['id' => $existsId, 'client_id' => $clientId]);

                    if (!empty($find)) {
                        $updated = $find->first();

                        if (!empty($updated) && !empty($updated->getAttributes()['file'])) {
                            $data[$index]['file'] = $updated->getAttributes()['file'];
                        }
                    }
                }

                $validator = ClientEmailProgressReport::validators($data[$index], true);
                if ($validator) {
                    /*$progressReport   = ClientEmailProgressReport::updateOrCreate($data[$index], $data[$index]);
                    $progressReportId = $progressReport->id;*/
                    $getMax             = ClientEmailProgressReport::max('id');
                    $progressReportId   = ($operation == 'update' && !empty($existsId)) ? $existsId : $getMax + 1;
                    $data[$index]['id'] = $progressReportId;
                    $fileName           = null;

                    if (!empty($progressReportFiles[$index]) && $progressReportFiles[$index] instanceof UploadedFile ) {
                        $pathInfos = pathinfo($progressReportFiles[$index]->getClientOriginalName());

                        if (!empty($pathInfos['extension'])) {
                            $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $clientId . '_' . $progressReportId . '.' . $pathInfos['extension'];
                            $storeFile = $progressReportFiles[$index]->storeAs('client' . '\\' . $clientId . '\\'. ClientEmailProgressReport::$storageFolderName, $fileName, ClientEmailProgressReport::$fileSystem);

                            if ($storeFile) {
                                // $data[$index]['file'] = $fileName;
                                // ClientEmailProgressReport::find($progressReportId)->update(['file' => $fileName]);
                            } else {
                                $fileName = null;
                            }
                        }
                    }

                    $newData[$index]         = [];
                    $newData[$index]         = $data[$index];
                    if (!empty($fileName)) {
                        $newData[$index]['file'] = $fileName;
                    }
                    $progressReport = ClientEmailProgressReport::updateOrCreate($data[$index], $newData[$index]);
                }
            };

            if (!empty($existsIds)) {
                foreach ($existsIds as $index => $existsId) {
                    if (!empty($progressReportDates[$index])) {

                        unset($progressReportDates[$index]);

                        $create($index, $existsId);
                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($progressReportDates)) {
                foreach ($progressReportDates as $index => $progressReportDate) {
                    $create($index);
                }
            }
            if (!empty($remove)) {
                $find = ClientEmailProgressReport::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }

            // Client private informations.
            $privateDates        = $request->get('client_private_dates', []);
            $privateInformations = $request->get('client_private_informations', []);
            $existsIds           = $request->get('id_client_private_informations', []);
            $remove              = [];

            $create = function($index, $existsId = null) use($privateDates, $privateInformations, $clientId) {
                if (!isset($index)) {
                    return false;
                }

                $data = [];

                $data[$index] = [
                    'date'                => $privateDates[$index],
                    'private_information' => (!empty($privateInformations[$index]) ? $privateInformations[$index] : NULL),
                    'client_id'           => $clientId,
                    'is_removed'          => BaseModel::$notRemoved
                ];

                $validator = ClientPrivateInformation::validators($data[$index], true);
                if ($validator) {
                    $update = NULL;
                    if (!empty($existsId)) {
                        $update = ClientPrivateInformation::find($existsId);
                    }

                    if (!empty($update)) {
                        $update->update($data[$index]);
                    } else {
                        ClientPrivateInformation::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            };

            if (!empty($existsIds)) {
                foreach ($existsIds as $index => $existsId) {
                    if (!empty($privateDates[$index])) {
                        unset($privateDates[$index]);

                        $create($index, $existsId);
                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($privateDates)) {
                foreach ((array)$privateDates as $index => $privateDate) {
                    $create($index);
                }
            }
            if (!empty($remove)) {
                $find = ClientPrivateInformation::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }

            // Client documents
            $clientDocuments = $request->file('client_documents', []);
            $existsIds       = $request->get('id_client_documents', []);
            $existsIdsOld    = $request->get('id_client_documents_old', []);
            $remove          = [];

            $create = function($index, $existsId = null) use($clientDocuments, $clientId, $operation) {
                if (!isset($index)) {
                    return false;
                }

                $data           = [];
                $clientDocument = (!empty($clientDocuments[$index])) ? $clientDocuments[$index] : [];
                $fileName       = NULL;

                if ($operation == 'update' && !empty($existsId)) {
                    $find = ClientDocument::where(['id' => $existsId, 'client_id' => $clientId]);

                    if (!empty($find)) {
                        $updated = $find->first();

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
            };

            if (!empty($existsIdsOld)) {
                foreach ($existsIdsOld as $index => $existsId) {
                    if (!empty($existsIds[$index])) {

                        unset($clientDocuments[$index]);

                        $create($index, $existsId);
                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($clientDocuments)) {
                foreach ($clientDocuments as $index => $clientDocument) {
                    $create($index);
                }
            }
            if (!empty($remove)) {
                $find = ClientDocument::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
            }

            // Terms and conditions.
            $termsAndConditions = $request->get('terms_and_conditions', []);
            $existsIds          = $request->get('id_client_terms_and_conditions', []);
            $remove             = [];

            $create = function($index, $existsId = null) use($termsAndConditions, $clientId) {
                if (!isset($index)) {
                    return false;
                }

                $data = [];

                $data[$index] = [
                    'terms_and_conditions' => $termsAndConditions[$index],
                    'client_id'            => $clientId,
                    'is_removed'           => BaseModel::$notRemoved
                ];

                $validator = ClientTermsAndCondition::validators($data[$index], true);
                if ($validator) {
                    $update = NULL;
                    if (!empty($existsId)) {
                        $update = ClientTermsAndCondition::find($existsId);
                    }

                    if (!empty($update)) {
                        $update->update($data[$index]);
                    } else {
                        ClientTermsAndCondition::updateOrCreate($data[$index], $data[$index]);
                    }
                }
            };

            if (!empty($existsIds)) {
                foreach ($existsIds as $index => $existsId) {
                    if (!empty($termsAndConditions[$index])) {

                        unset($termsAndConditions[$index]);

                        $create($index, $existsId);
                    } else {
                        $remove[$index] = $existsId;
                    }
                }
            }
            if (!empty($termsAndConditions)) {
                foreach ($termsAndConditions as $index => $termsAndCondition) {
                    $create($index);
                }
            }
            if (!empty($remove)) {
                $find = ClientTermsAndCondition::where(['client_id' => $clientId, 'is_removed' => BaseModel::$notRemoved])->whereIn('id', $remove)->get();
                BaseModel::isRemoveFire($find);
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
        $client     = Client::find($id);
        $loggedInId = \Auth::user()->id;

        if ($client) {
            $permissions = app('App\Http\Controllers\RoleController')->getPermissionsByGroup();

            return view('app.clients.show', ['client' => $client, 'groups' => $permissions, 'loggedInId' => $loggedInId]);
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
        $client     = Client::find($id);
        $loggedInId = \Auth::user()->id;

        if ($client) {
            $roles           = Role::orderBy('id', 'ASC')->get();
            $editorRoles     = Role::whereRaw("lower(name) = 'editor'")->first();
            $assignTo        = [];
            $purposeArticles = PurposeArticle::where('is_removed', PurposeArticle::$notRemoved)->get();
            $assignedTo      = FollowUp::where('is_removed', PurposeArticle::$notRemoved)->where('client_id', $id)->get();
            $assignedTo      = (!empty($assignedTo) && !$assignedTo->isEmpty()) ? $assignedTo->pluck('follow_by')->toArray() : [];

            if (!empty($editorRoles)) {
                $assignTo = $editorRoles->users;
            }

            return view('app.clients.edit', ['client' => $client, 'roles' => $roles, 'assignTo' => $assignTo, 'purposeArticles' => $purposeArticles, 'assignedTo' => $assignedTo, 'loggedInId' => $loggedInId]);
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
            }
            if (empty($data['password_2'])) {
                unset($data['password_2']);
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
                'secondary_contact' => ['string', 'nullable', 'not_regex:/[#$%^&*+=\\[\]\';,\/{}|":<>?~\\\\]/'],
                'passport_number'   => ['string', 'nullable'],
                'process_address'   => ['string', 'nullable'],
                'nationality'       => ['required', 'string', 'nullable'],
                'work_status'       => ['string', 'nullable', 'in:0,1,2'],
                'photo'             => ['string', 'nullable'],
                'banned'            => ['integer', 'in:0,1'],
                // 'assign_date'       => array_merge(['date_format:Y-m-d'], $assignDate),
                // 'assign_to'         => array_merge(['integer', 'exists:' . Client::getTableName() . ',id'], $assignTo),
                'password'          => ['string', 'min:8'],
                'password_2'        => ['string', 'min:8'],
                'is_superadmin'     => ['in:0'],
                'purpose_articles'  => ['required']
            ]);

            $validator->validate();

            if (isset($data['password']) && $data['password'] !== null) {
                $data['password_text'] = $data['password'];
                $data['password']      = Hash::make($data['password']);
            }
            if (isset($data['password_2']) && $data['password_2'] !== null) {
                $data['password_text_2'] = $data['password_2'];
                $data['password_2']      = Hash::make($data['password_2']);
            } else {
                $data['password_text_2'] = '';
                $data['password_2']      = '';
            }
            if (empty($data['assign_date'])) {
                unset($data['assign_date']);
            }
            $assignTo = [];
            if (isset($data['assign_to'])) {
                $assignTo = (array)$data['assign_to'];
                unset($data['assign_to']);
            }

            $assignDates = [];
            if (isset($data['assign_dates'])) {
                $assignDates = (array)$data['assign_dates'];
                unset($data['assign_dates']);
            }

            $existsIds = [];
            if (isset($data['id_follow_up'])) {
                $existsIds = $data['id_follow_up'];
                unset($data['id_follow_up']);
            }

            if ($client->update($data)) {

                $this->storeAssignTo($id, $assignTo, $assignDates, $existsIds, 'update');
                /*if (!empty($assignTo)) {
                    $this->storeAssignTo($id, $assignTo, $assignDates, $existsIds, 'update');
                }*/

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
            // $client->update(['is_removed' => BaseModel::$removed]);
            $find = Client::where('id', $id)->get();
            BaseModel::isRemoveFire($find);

            if ($this->isEditors) {
                return redirect('editors')->with('success',__("Editor deleted!"));
            } else {
                if ($this->isViewClients) {
                    return redirect('clients/view')->with('success', __("Client deleted!"));
                } else {
                    return redirect('clients')->with('success',__("Client deleted!"));
                }
            }
        } else {
            if ($this->isEditors) {
                return redirect('editors')->with('error',__("Editor not found!"));
            } else {
                if ($this->isViewClients) {
                    return redirect('clients/view')->with('error', __("Client not found!"));
                } else {
                    return redirect('clients')->with('error',__("Client not found!"));
                }
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
    public function activityLog($id, Request $request)
    {
        $client = Client::find($id);

        if ($client) {
            $audits = Audit::where('new_values','NOT LIKE','%remember_token%')->where('client_id', $client->id)->orderBy('created_at','DESC');

            $records = $audits->get();
            if (!empty($records) && !$records->isEmpty()) {
                $records->map(function($data, $index) use($records) {
                    if (count($data->getModified()) == 1) {
                        if (!empty($data->getModified()['registration_date'])) {
                            if (strtotime(date('Y-m-d', strtotime($data->getModified()['registration_date']['old']))) == strtotime(date('Y-m-d', strtotime($data->getModified()['registration_date']['new'])))) {
                                unset($records[$index]);
                            }
                        }

                        if (!empty($data->getModified()['date'])) {
                            if (strtotime(date('Y-m-d', strtotime($data->getModified()['date']['old']))) == strtotime(date('Y-m-d', strtotime($data->getModified()['date']['new'])))) {
                                unset($records[$index]);
                            }
                        }
                    }
                });
            }

            // $audits = $audits->paginate(20);

            $page   = $request->get('page', 1);
            $limit  = 20;
            $audits = new LengthAwarePaginator(
                $records->forPage($page, $limit), $records->count(), $limit, $page, ['path' => $request->path()]
            );

            $audits = AuditMessages::get($audits);

            return view('app.clients.activity', ['client' => $client, 'audits' => $audits]);
        } else {
            return redirect('clients')->with('error',__("Client not found!"));
        }
    }

    public function activityLogOwn($id, Request $request)
    {
        $client = Client::find($id);

        if ($client) {
            $audits = Audit::where('new_values','NOT LIKE','%remember_token%')->where('user_id', $client->id)->orderBy('created_at','DESC');

            $records = $audits->get();
            if (!empty($records) && !$records->isEmpty()) {
                $records->map(function($data, $index) use($records) {
                    if (count($data->getModified()) == 1) {
                        if (!empty($data->getModified()['registration_date'])) {
                            if (strtotime(date('Y-m-d', strtotime($data->getModified()['registration_date']['old']))) == strtotime(date('Y-m-d', strtotime($data->getModified()['registration_date']['new'])))) {
                                unset($records[$index]);
                            }
                        }

                        if (!empty($data->getModified()['date'])) {
                            if (strtotime(date('Y-m-d', strtotime($data->getModified()['date']['old']))) == strtotime(date('Y-m-d', strtotime($data->getModified()['date']['new'])))) {
                                unset($records[$index]);
                            }
                        }
                    }
                });
            }

            // $audits = $audits->paginate(20);

            $page   = $request->get('page', 1);
            $limit  = 20;
            $audits = new LengthAwarePaginator(
                $records->forPage($page, $limit), $records->count(), $limit, $page, ['path' => $request->path()]
            );

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
        $loggedInId = \Auth::user()->id;

        $clientModel = new Client();

        $client = $clientModel->find($id);

        return view('app.clients.print', ['client' => $client, 'loggedInId' => $loggedInId]);
    }

    public function profile()
    {
        $user = \Auth::user();

        if (!empty($user)) {
            $purposeArticles = PurposeArticle::where('is_removed', PurposeArticle::$notRemoved)->get();
            $assignTo        = [];
            $purposeArticles = PurposeArticle::where('is_removed', PurposeArticle::$notRemoved)->get();
            $assignedTo      = FollowUp::where('is_removed', PurposeArticle::$notRemoved)->where('client_id', $user->id)->get();
            $assignedTo      = (!empty($assignedTo) && !$assignedTo->isEmpty()) ? $assignedTo->pluck('follow_by')->toArray() : [];

            if (!empty($editorRoles)) {
                $assignTo = $editorRoles->users;
            }

            return view('app.profiles.edit', ['user' => $user, 'isEditors' => $user->isEditors(), 'purposeArticles' => $purposeArticles, 'assignTo' => $assignTo, 'assignedTo' => $assignedTo]);
        }

        return redirect('/')->with('error', __("Not found!"));
    }

    public function email($id, Request $request)
    {
        $emailIds = $request->get('emails');

        return redirect('clients/'.$id)->with('success', __("Emails sent successfully!"));
    }

    public function removeDocument($id)
    {
        if (!empty($id)) {
            $find = ClientDocument::where('id', $id)->get();

            if (!empty($find)) {
                BaseModel::isRemoveFire($find);

                return redirect()->back()->with('success', __('Document removed successfully!'));
            }
        }

        return redirect()->back()->with('email', __('Document not removed!'));
    }
}
