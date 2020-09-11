<?php

use Illuminate\Database\Seeder;
use App\Client;
use App\Role;
use App\PurposeArticle;
use App\ClientPurposeArticle;
use App\ClientFee;
use App\ClientEmailProgressReport;
use App\ClientPrivateInformation;
use App\ClientTermsAndCondition;
use App\ModelHasRoles;
use App\Account;
use App\FollowUp;
use App\ClientCondition;
use App\ClientDocument;
use Illuminate\Support\Facades\Storage;
use App\BaseModel;
use App\PoaAgreement;
use App\TranslateModelDocument;

class OriginalDatabaseSeeder extends Seeder
{
    private $posIds  = [];
    private $feesIds = [];
    private $prsIds  = [];
    private $cpsIds  = [];
    private $tcsIds  = [];
    private $ccsIds  = [];
    private $workStatus = [];
    private $documents  = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // BaseModel::disableAuditing();

        if (env('APP_ENV') == 'dev' || env('APP_ENV') == 'local') {
            $this->domain = 'http://consult.evolutionitsolution.com/wp-content/uploads/';
        } elseif (env('APP_ENV') == 'live') {
            $this->domain = 'https://nunolawyer.com/wp-content/uploads/';
        }

        ini_set('max_execution_time', '0');

    	$this->mysql 		 = \DB::connection('mysql');
        $this->mysqlOriginal = \DB::connection('mysql_original');

        $this->mysqlOriginal->select('SET SESSION group_concat_max_len = 1000000');

        // Clients
        // $this->runClients();

        // Other stiff.
        // $this->mastersClone();

        // Clinet & Editor other infos.
        // $this->runOtherStuff();

        // Add user credentials.
        // $this->runCredentials(true);

        // Create account entries.
        // $this->runAccounts();

        // Add work status.
        // $this->runWorkStatus();

        // AAdd files.
        // $this->runFiles();

        // BaseModel::enableAuditing();
    }

    public function runClients()
    {
        $entity       = "|==|";
    	$oldDatas     = $this->mysqlOriginal->table('nl_users')->where('id', '!=', 1)->get();
        if (env('APP_ENV') == 'local') {
            $oldUserMetas = $this->mysqlOriginal->table('nl_usermeta')->select(\DB::raw("CONCAT('{', GROUP_CONCAT(TRIM(LEADING '{' FROM TRIM(TRAILING '}' FROM JSON_OBJECT(`meta_key`, `meta_value`)))) ,'}') as meta_data, user_id"))->groupBy('user_id')->get()->keyBy('user_id');
        } else {
            $oldUserMetas = $this->mysqlOriginal->table('nl_usermeta')->select(\DB::raw("GROUP_CONCAT(CONCAT(`meta_key`, '|:::::|', `meta_value`) separator '{$entity}') as meta_data, user_id"))->groupBy('user_id')->get()->keyBy('user_id');
        }

    	if (!empty($oldDatas) && !$oldDatas->isEmpty()) {
            $insertData = [];

            // Roles
            $roleClient = Role::find(2);
            $roleEditor = Role::find(3);

    		foreach ($oldDatas->chunk(500) as $index => $oldData) {
    			foreach ($oldData as $data) {
                    $userId   = $data->ID;
                    $userMeta = (!empty($oldUserMetas[$userId])) ? $oldUserMetas[$userId] : [];
                    if (env('APP_ENV') == 'local') {
                        $userMeta = (!empty($userMeta)) ? json_decode($userMeta->meta_data, true) : [];
                    } else {
                        $userMetaBulk = explode($entity, $userMeta->meta_data);
                        $userMeta     = [];

                        if (!empty($userMetaBulk)) {
                            foreach ((array)$userMetaBulk as &$meta) {
                                $meta = explode('|:::::|', $meta);

                                if (!empty($meta[0]) && !empty($meta[1])) {
                                    $userMeta[$meta[0]] = $meta[1];
                                }
                            }
                        }
                    }
                    $roles    = (!empty($userMeta['nl_capabilities'])) ? (is_numeric($userMeta['nl_capabilities']) ? $userMeta['nl_capabilities'] : unserialize($userMeta['nl_capabilities'])) : [];
                    $isClient = (!empty($roles) && count($roles) > 0 && key($roles) == 'client' && $roles['client'] == 1);
                    $isEditor = (!empty($roles) && count($roles) > 0 && key($roles) == 'editor' && $roles['editor'] == 1);

                    $registrationDate = (!empty($userMeta['registration_date'])) ? substr_replace($userMeta['registration_date'], '-', 4, 0) : '0000-00-00';
                    $registrationDate = (!empty($userMeta['registration_date'])) ? substr_replace($registrationDate, '-', 7, 0) : '0000-00-00';

    				$insertData[$index] = [
                        'registration_date' => $registrationDate,
                        'first_name'        => (!empty($userMeta['first_name'])) ? $userMeta['first_name'] : "",
                        'last_name'         => (!empty($userMeta['last_name'])) ? $userMeta['last_name'] : "",
                        'email'             => (!empty($data->user_email)) ? $data->user_email : "",
                        'dob'               => (!empty($userMeta['date_of_birth']) && strtotime($userMeta['date_of_birth']) > 0) ? date("Y-m-d", strtotime($userMeta['date_of_birth'])) : '0000-00-00',
                        'contact'           => (!empty($userMeta['contact'])) ? $userMeta['contact'] : "",
                        'process_address'   => (!empty($userMeta['process_address'])) ? $userMeta['process_address'] : "",
                        'nationality'       => (!empty($userMeta['nationality'])) ? $userMeta['nationality'] : "",
                        'old_id'            => $userId
                    ];

                    if (!empty($userMeta)) {
                        $feeDate = $prDate = $cpDate = false;
                        foreach ((array)$userMeta as $key => $meta) {
                            if (preg_match("/^purpose_and_article_/i", $key)) {
                                $this->posIds[$userId][] = $meta;
                                // $_SESSION['poa'][$userId][] = $meta;
                            }

                            if (preg_match("/^client_fee_/i", $key)) {
                                if (preg_match("/_date/i", $key)) {
                                    $feeDate = $meta;
                                    $meta    = substr_replace($meta, '-', 4, 0);
                                    $meta    = substr_replace($meta, '-', 7, 0);
                                    $this->feesIds[$userId]['client_fee_' . $feeDate]['date'] = $meta;
                                    // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['date'] = $meta;
                                }

                                if ($feeDate) {
                                    if (preg_match("/_tplf/i", $key)) {
                                        $this->feesIds[$userId]['client_fee_' . $feeDate]['proposed_lawyer_fee'] = $meta;
                                        // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['proposed_lawyer_fee'] = $meta;
                                    }

                                    if (preg_match("/_rlf/i", $key)) {
                                        $this->feesIds[$userId]['client_fee_' . $feeDate]['received_lawyer_fee'] = $meta;
                                        // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['received_lawyer_fee'] = $meta;
                                    }

                                    if (preg_match("/_mlf/i", $key)) {
                                        $this->feesIds[$userId]['client_fee_' . $feeDate]['missing_lawyer_fee'] = $meta;
                                        // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['missing_lawyer_fee'] = $meta;
                                    }

                                    if (preg_match("/_tpgf/i", $key)) {
                                        $this->feesIds[$userId]['client_fee_' . $feeDate]['proposed_government_fee'] = $meta;
                                        // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['proposed_government_fee'] = $meta;
                                    }

                                    if (preg_match("/_rgf/i", $key)) {
                                        $this->feesIds[$userId]['client_fee_' . $feeDate]['received_government_fee'] = $meta;
                                        // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['received_government_fee'] = $meta;
                                    }

                                    if (preg_match("/_mgf/i", $key)) {
                                        $this->feesIds[$userId]['client_fee_' . $feeDate]['missing_government_fee'] = $meta;
                                        // $_SESSION['fees'][$userId]['client_fee_' . $feeDate]['missing_government_fee'] = $meta;
                                    }
                                }
                            }

                            if (preg_match("/^progress_report_/i", $key)) {
                                if (preg_match("/_date/i", $key)) {
                                    $prDate = $meta;
                                    $meta   = substr_replace($meta, '-', 4, 0);
                                    $meta   = substr_replace($meta, '-', 7, 0);
                                    $this->prsIds[$userId]['progress_report_' . $prDate]['date'] = $meta;
                                    // $_SESSION['reports'][$userId]['progress_report_' . $prDate]['date'] = $meta;
                                }

                                if ($prDate) {
                                    if (preg_match("/_reporttt/i", $key)) {
                                        $this->prsIds[$userId]['progress_report_' . $prDate]['progress_report'] = $meta;
                                        // $_SESSION['reports'][$userId]['progress_report_' . $prDate]['progress_report'] = $meta;
                                    }

                                    if (preg_match("/_file/i", $key)) {
                                        $this->prsIds[$userId]['progress_report_' . $prDate]['file'] = $meta;
                                    }
                                }
                            }

                            if (preg_match("/^client_remark1_/i", $key)) {
                                if (preg_match("/_date/i", $key)) {
                                    $cpDate = $meta;
                                    $meta   = substr_replace($meta, '-', 4, 0);
                                    $meta   = substr_replace($meta, '-', 7, 0);
                                    $this->cpsIds[$userId]['client_remark1_' . $cpDate]['date'] = $meta;
                                    // $_SESSION['reports'][$userId]['client_remark1_' . $cpDate]['date'] = $meta;
                                }

                                if ($cpDate) {
                                    if (preg_match("/_remark/i", $key)) {
                                        $this->cpsIds[$userId]['client_remark1_' . $cpDate]['private_information'] = $meta;
                                        // $_SESSION['reports'][$userId]['client_remark1_' . $cpDate]['private_information'] = $meta;
                                    }
                                }
                            }

                            if (preg_match("/^client_condition1_/i", $key)) {
                                if (preg_match("/_date/i", $key)) {
                                    $ccDate = $meta;
                                    $meta   = substr_replace($meta, '-', 4, 0);
                                    $meta   = substr_replace($meta, '-', 7, 0);
                                    $this->ccsIds[$userId]['client_condition1_' . $ccDate]['date'] = $meta;
                                }

                                if ($ccDate) {
                                    if (preg_match("/_client_condition/i", $key)) {
                                        $this->ccsIds[$userId]['client_condition1_' . $ccDate]['condition'] = $meta;
                                    }
                                }
                            }

                            if (preg_match("/^tac_/i", $key) && preg_match("/_custom_editor/i", $key)) {
                                $this->tcsIds[$userId][] = $meta;
                                // $_SESSION['terms'][$userId][] = $meta;
                            }

                            if (preg_match("/^client_document_/i", $key) && preg_match("/_file/i", $key)) {
                                $this->documents[$userId]['client_document'][] = $meta;
                                // $_SESSION['terms'][$userId][] = $meta;
                            }
                        }
                    }

                    if ($isClient || $isEditor) {
                        // $create = Client::create($insertData[$index]);
                        // Client::where('old_id', $userId)->update($insertData[$index]);

                        /*if ($create) {
                            if ($isClient) {
                                $create->assignRole($roleClient);
                            } else {
                                $create->assignRole($roleEditor);
                            }
                        }*/
                    }
    			}
    		}

            // $this->mysql->table('clients')->insert($insertData)->assignRole($roleClient);
    	}
    }

    public function mastersClone()
    {
        $entity   = "|==|";
        $oldDatas = $this->mysqlOriginal->table('nl_posts')->get();

        if (!empty($oldDatas) && !$oldDatas->isEmpty()) {
            $getUsers = $this->mysql->table('clients')->get()->keyBy('old_id');

            if (env('APP_ENV') == 'local') {
                $oldPostMetas = $this->mysqlOriginal->table('nl_postmeta')->select(\DB::raw("CONCAT('{', GROUP_CONCAT(TRIM(LEADING '{' FROM TRIM(TRAILING '}' FROM JSON_OBJECT(`meta_key`, `meta_value`)))) ,'}') as meta_data, post_id"))->groupBy('post_id')->get()->keyBy('post_id');
            } else {
                $oldPostMetas = $this->mysqlOriginal->table('nl_postmeta')->select(\DB::raw("GROUP_CONCAT(CONCAT(`meta_key`, '|:::::|', `meta_value`) separator '{$entity}') as meta_data, post_id"))->groupBy('post_id')->get()->keyBy('post_id');
            }

            $insertPoaData = $insertPOAData = $insertTranslateData = $insertFeesPolicyData = $insertTCData = [];

            foreach ($oldDatas->chunk(500) as $index => $oldData) {
                foreach ($oldData as $data) {
                    $postId   = $data->ID;
                    $postMeta = (!empty($oldPostMetas[$postId])) ? $oldPostMetas[$postId] : [];
                    if (env('APP_ENV') == 'local') {
                        $postMeta = (!empty($postMeta)) ? json_decode($postMeta->meta_data, true) : [];
                    } elseif (!empty($postMeta->meta_data)) {
                        $userMetaBulk = explode($entity, $postMeta->meta_data);
                        $postMeta     = [];

                        if (!empty($userMetaBulk)) {
                            foreach ((array)$userMetaBulk as &$meta) {
                                $meta = explode('|:::::|', $meta);

                                if (!empty($meta[0]) && !empty($meta[1])) {
                                    $postMeta[$meta[0]] = $meta[1];
                                }
                            }
                        }
                    }
                    $userData = (!empty($postMeta['client_name']) && !empty($getUsers[$postMeta['client_name']])) ? $getUsers[$postMeta['client_name']] : [];

                    // Purpose & Articles.
                    if ($data->post_type == 'poa') {
                        $insertPoaData[] = [
                            'title'  => $data->post_title,
                            'text'   => $data->post_content,
                            'old_id' => $postId
                        ];
                    }

                    if ($data->post_type == 'modelpoa') {
                        $insertPOAData[] = [
                            'title'  => $data->post_title,
                            'text'   => $data->post_content,
                            'old_id' => $postId
                        ];
                    }

                    if ($data->post_type == 'certification') {
                        $insertTranslateData[] = [
                            'title'     => $data->post_title,
                            'text'      => $data->post_content,
                            'client_id' => (!empty($userData)) ? $userData->id : NULL,
                            'old_id'    => $postId
                        ];
                    }

                    if ($data->post_type == 'fee-policy') {
                        $insertFeesPolicyData[] = [
                            'title'  => $data->post_title,
                            'text'   => $data->post_content,
                            'old_id' => $postId,
                        ];
                    }

                    if ($data->post_type == 'tac') {
                        $insertTCData[] = [
                            'title'  => $data->post_title,
                            'text'   => $data->post_content,
                            'old_id' => $postId
                        ];
                    }
                }
            }

            if (!empty($insertPoaData)) {
                $this->mysql->table('purpose_articles')->insert($insertPoaData);
            }

            if (!empty($insertPOAData)) {
                $this->mysql->table('poa_agreements')->insert($insertPOAData);
            }

            if (!empty($insertTranslateData)) {
                $this->mysql->table('translate_model_documents')->insert($insertTranslateData);
            }

            if (!empty($insertFeesPolicyData)) {
                $this->mysql->table('our_fee_policy_documents')->insert($insertFeesPolicyData);
            }

            if (!empty($insertTCData)) {
                $this->mysql->table('terms_and_conditions')->insert($insertTCData);
            }
        }
    }

    public function runOtherStuff()
    {
        if (!empty($this->posIds)) {
            foreach ($this->posIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    $count = count($values);

                    foreach ((array)$values as $index => $value) {
                        if (empty($value)) {
                            continue;
                        }

                        $isLastInserted = ($count == ($index + 1)) ? '1' : '0';
                        $getPos         = PurposeArticle::where('old_id', $value)->first();

                        if (!empty($getPos)) {
                            $create = [
                                'is_last_inserted'   => $isLastInserted,
                                'purpose_article_id' => $getPos->id,
                                'client_id'          => $getUser->id
                            ];

                            ClientPurposeArticle::updateOrCreate($create, $create);
                        }
                    }
                }
            }
        }

        if (!empty($this->feesIds)) {
            foreach ($this->feesIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value['date'])) {
                            continue;
                        }

                        ClientFee::create([
                            'date'                    => $value['date'],
                            'proposed_lawyer_fee'     => (!empty($value['proposed_lawyer_fee'])) ? (float)$value['proposed_lawyer_fee'] : NULL,
                            'received_lawyer_fee'     => (!empty($value['received_lawyer_fee'])) ? (float)$value['received_lawyer_fee'] : NULL,
                            'missing_lawyer_fee'      => (!empty($value['missing_lawyer_fee'])) ? (float)$value['missing_lawyer_fee'] : NULL,
                            'proposed_government_fee' => (!empty($value['proposed_government_fee'])) ? (float)$value['proposed_government_fee'] : NULL,
                            'received_government_fee' => (!empty($value['received_government_fee'])) ? (float)$value['received_government_fee'] : NULL,
                            'missing_government_fee'  => (!empty($value['missing_government_fee'])) ? (float)$value['missing_government_fee'] : NULL,
                            'client_id'               => $getUser->id
                        ]);
                    }
                }
            }
        }

        if (!empty($this->prsIds)) {
            ClientEmailProgressReport::disableAuditing();

            $create = [];
            $count  = 0;

            $getMax = ClientEmailProgressReport::max('id');

            foreach ($this->prsIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value['date'])) {
                            continue;
                        }

                        $imageName = NULL;
                        if (!empty($value['file'])) {
                            $getPostMetas = $this->mysqlOriginal->table('nl_postmeta')->where('post_id', $value['file'])->where('meta_key', '_wp_attached_file')->first();

                            if (!empty($getPostMetas)) {
                                $pathInfos = pathinfo($getPostMetas->meta_value);

                                if (!empty($pathInfos['extension'])) {
                                    $progressReportId = $getMax + 1;

                                    $imageName = (!empty($pathInfos['filename'])) ? $pathInfos['filename'] . '_' . $getUser->id . '_' . $progressReportId . '.' . $pathInfos['extension'] : $getPostMetas->meta_value;

                                    $imageUrl  = $this->domain . $getPostMetas->meta_value;

                                    $contents  = file_get_contents($imageUrl);

                                    $storeFile = Storage::disk(ClientEmailProgressReport::$fileSystem)->put('client' . '\\' . $getUser->id . '\\'. ClientEmailProgressReport::$storageFolderName .'\\'. $imageName, $contents);

                                    if (!$storeFile) {
                                        $imageName = NULL;
                                    }
                                }
                            }
                        }

                        $create[$count] = [
                            'date'            => $value['date'],
                            'progress_report' => (!empty($value['progress_report'])) ? $value['progress_report'] : NULL,
                            'client_id'       => $getUser->id,
                            'file'            => $imageName
                        ];

                        $count++;
                        $getMax++;
                    }
                }
            }

            ClientEmailProgressReport::insert($create);

            ClientEmailProgressReport::enableAuditing();
        }

        if (!empty($this->cpsIds)) {
            foreach ($this->cpsIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value['date'])) {
                            continue;
                        }

                        $create = [
                            'date'                => $value['date'],
                            'private_information' => (!empty($value['private_information'])) ? $value['private_information'] : NULL,
                            'client_id'           => $getUser->id
                        ];

                        ClientPrivateInformation::updateOrCreate($create, $create);
                    }
                }
            }
        }

        if (!empty($this->ccsIds)) {
            foreach ($this->ccsIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value['date'])) {
                            continue;
                        }

                        $create = [
                            'date'      => $value['date'],
                            'condition' => (!empty($value['condition'])) ? $value['condition'] : NULL,
                            'client_id' => $getUser->id
                        ];

                        ClientCondition::updateOrCreate($create, $create);
                    }
                }
            }
        }

        if (!empty($this->tcsIds)) {
            foreach ($this->tcsIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value)) {
                            continue;
                        }

                        $create = [
                            'terms_and_conditions' => $value,
                            'client_id'            => $getUser->id
                        ];

                        ClientTermsAndCondition::updateOrCreate($create, $create);
                    }
                }
            }
        }
    }

    public function runCredentials($assignAdminsRole = false)
    {
        $clientId = 18067;

        $getClient = Client::find($clientId);

        if (!empty($getClient)) {
            $getClient->update(['is_superadmin' => 1, 'password' => Hash::make('Portugal@123'), 'password_text' => 'Portugal@123']);

            if ($assignAdminsRole) {
                $modelHasRoles = ModelHasRoles::where('model_id', $clientId)->update(['role_id' => 1]);
            }
        }
    }

    public function runAccounts()
    {
        $getClientFees = ClientFee::all();

        if (!empty($getClientFees) && !$getClientFees->isEmpty()) {
            $createdBy = 1;

            foreach ($getClientFees as $index => $getClientFee) {
                $accountData = [];

                $clientId = $getClientFee->client_id;

                // Get last purpose article id.
                $getLastPurposeArticleId = ClientPurposeArticle::where(['is_removed' => ClientPurposeArticle::$notRemoved, 'is_last_inserted' => '1', 'client_id' => $clientId])->first();

                if (!empty($getLastPurposeArticleId)) {
                    $date                  = date('Y-m-d', strtotime($getClientFee->date));
                    $receivedLawyerFee     = $getClientFee->received_lawyer_fee;
                    $receivedGovernmentFee = $getClientFee->received_government_fee;
                    $purposeArticleId      = $getLastPurposeArticleId->purpose_article_id;

                    $accountData['created_by']          = $createdBy;
                    $accountData['client_id']           = $clientId;
                    $accountData['date']                = $date;
                    $accountData['received_amount']     = ($receivedLawyerFee + $receivedGovernmentFee);
                    $accountData['purpose_article_id']  = ($purposeArticleId);

                    $validator = Account::validators($accountData, true);
                    if ($validator) {
                        Account::updateOrCreate($accountData, $accountData);
                    }
                }
            }
        }
    }

    public function runWorkStatus()
    {
        $getClients = Client::all();

        if (!empty($getClients) && !$getClients->isEmpty()) {
            $entity = "|==|";

            if (env('APP_ENV') == 'local') {
                $oldUserMetas = $this->mysqlOriginal->table('nl_usermeta')->select(\DB::raw("CONCAT('{', GROUP_CONCAT(TRIM(LEADING '{' FROM TRIM(TRAILING '}' FROM JSON_OBJECT(`meta_key`, `meta_value`)))) ,'}') as meta_data, user_id"))->groupBy('user_id')->get()->keyBy('user_id');
            } else {
                $oldUserMetas = $this->mysqlOriginal->table('nl_usermeta')->select(\DB::raw("GROUP_CONCAT(CONCAT(`meta_key`, '|:::::|', `meta_value`) separator '{$entity}') as meta_data, user_id"))->groupBy('user_id')->get()->keyBy('user_id');
            }

            foreach ($getClients->chunk(500) as $index => $getClient) {
                foreach ($getClient as $data) {
                    $newUserId = $data->id;
                    $userId    = $data->old_id;
                    $userMeta  = (!empty($oldUserMetas[$userId])) ? $oldUserMetas[$userId] : [];
                    if (env('APP_ENV') == 'local') {
                        $userMeta = (!empty($userMeta)) ? json_decode($userMeta->meta_data, true) : [];
                    } elseif (!empty($userMeta->meta_data)) {
                        $userMetaBulk = explode($entity, $userMeta->meta_data);
                        $userMeta     = [];

                        if (!empty($userMetaBulk)) {
                            foreach ((array)$userMetaBulk as &$meta) {
                                $meta = explode('|:::::|', $meta);

                                if (!empty($meta[0]) && !empty($meta[1])) {
                                    $userMeta[$meta[0]] = $meta[1];
                                }
                            }
                        }
                    }

                    if (!empty($userMeta)) {
                        foreach ((array)$userMeta as $key => $meta) {
                            if (preg_match("/^client_condition1_/i", $key) && preg_match("/_work_status/i", $key)) {
                                $status = '0';
                                if ($meta == 'Work Incomplete') {
                                    $status = '1';
                                } elseif ($meta == 'Work Completed') {
                                    $status = '2';
                                }

                                $this->workStatus[$newUserId]['status'] = $status;
                            }

                            if (preg_match("/^assign_to/i", $key) && !empty($meta)) {
                                $this->workStatus[$newUserId]['assign_to'] = (is_numeric($meta)) ? [$meta] : unserialize($meta);
                            }

                            if (preg_match("/^assign_date/i", $key) && !empty($meta)) {
                                $meta = substr_replace($meta, '-', 4, 0);
                                $meta = substr_replace($meta, '-', 7, 0);
                                $this->workStatus[$newUserId]['assign_date'] = $meta;
                            }
                        }
                    }
                }
            }

            if (!empty($this->workStatus)) {
                foreach ($this->workStatus as $newUserId => $workStatus) {
                    Client::where('id', $newUserId)->update(['work_status' => (!empty($workStatus['status']) ? $workStatus['status'] : '0')]);

                    if (!empty($workStatus['assign_to'])) {
                        $getNewClients = Client::whereIn('old_id', $workStatus['assign_to'])->get();

                        if (!empty($getNewClients) && !$getNewClients->isEmpty()) {
                            $followUps  = [];
                            $followFrom = 1;
                            foreach ($getNewClients as $key => $getNewClient) {
                                $assignDate = (!empty($workStatus['assign_date'])) ? $workStatus['assign_date'] : NULL;
                                $assignTo   = $getNewClient->id;

                                $followUps[$key] = [
                                    'date'        => $assignDate,
                                    'follow_by'   => $assignTo,
                                    'follow_from' => $followFrom,
                                    'client_id'   => $newUserId,
                                    'is_removed'  => FollowUp::$notRemoved
                                ];

                                $validator = FollowUp::validators($followUps[$key], true);
                                if ($validator) {
                                    FollowUp::updateOrCreate($followUps[$key], $followUps[$key]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function runFiles()
    {
        PoaAgreement::disableAuditing();
        TranslateModelDocument::disableAuditing();

        /*if (!empty($this->documents)) {
            $insertData = [];

            foreach ($this->documents as $userId => $document) {
                if (!empty($document)) {
                    $getClients = Client::where('old_id', $userId)->first();

                    if (empty($getClients)) {
                        continue;
                    }

                    foreach ((array)$document as $key => $data) {
                        $getPostMetas = $this->mysqlOriginal->table('nl_postmeta')->whereIn('post_id', $data)->where('meta_key', '_wp_attached_file')->get();

                        if (!empty($getPostMetas) && !$getPostMetas->isEmpty()) {
                            foreach ($getPostMetas as $index => $getPostMeta) {
                                if (empty($getPostMeta->meta_value)) {
                                    continue;
                                }

                                if ($key == 'client_document') {
                                    $pathInfos = pathinfo($getPostMeta->meta_value);

                                    if (!empty($pathInfos['extension'])) {
                                        $imageName = (!empty($getClients) && !empty($pathInfos['filename'])) ? $pathInfos['filename'] . '_' . $getClients->id . '.' . $pathInfos['extension'] : $getPostMeta->meta_value;

                                        $imageUrl  = $this->domain . $getPostMeta->meta_value;

                                        // copy($imageUrl, storage_path('app/public/') . 'client' . '\\' . $getClients->id . '\\'. ClientDocument::$storageFolderName .'\\'. $imageName);
                                        // Image::make($imageUrl)->save(public_path('client' . '\\' . $getClients->id . '\\'. ClientDocument::$storageFolderName .'\\'. $imageName));

                                        $contents  = file_get_contents($imageUrl);

                                        $storeFile = Storage::disk(ClientDocument::$fileSystem)->put('client' . '\\' . $getClients->id . '\\'. ClientDocument::$storageFolderName .'\\'. $imageName, $contents);

                                        if ($storeFile) {
                                            $insertData['documents'][] = [
                                                'file'       => $imageName,
                                                'client_id'  => $getClients->id,
                                                'is_removed' => ClientDocument::$notRemoved
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($insertData['documents'])) {
                $this->mysql->table('client_documents')->insert($insertData['documents']);
            }
        }*/

        $oldPosts = $this->mysqlOriginal->table('nl_posts')->whereIn('post_type', ['modelpoa', 'certification'])->get();

        if (!empty($oldPosts) && !$oldPosts->isEmpty()) {
            $insertData = [];
            $getMax     = PoaAgreement::max('id');

            foreach ($oldPosts as $oldPost) {
                $postId = $oldPost->ID;

                if ($oldPost->post_type == 'modelpoa') {
                    $oldPostMetas = $this->mysqlOriginal->select("SELECT * FROM nl_postmeta WHERE post_id = (SELECT meta_value FROM nl_postmeta WHERE post_id = {$postId} AND meta_key = 'file' LIMIT 1) AND meta_key = '_wp_attached_file';");

                    $imageName = NULL;
                    if (!empty($oldPostMetas[0]->meta_value)) {
                        $metaValue = $oldPostMetas[0]->meta_value;

                        $pathInfos = pathinfo($metaValue);

                        if (!empty($pathInfos['extension'])) {
                            $id = $getMax + 1;

                            $imageName = (!empty($pathInfos['filename'])) ? $pathInfos['filename']  . '_' . $id . '_' . '.' . $pathInfos['extension'] : $metaValue;

                            $imageUrl  = $this->domain . $metaValue;

                            $contents  = file_get_contents($imageUrl);

                            $storeFile = Storage::disk(PoaAgreement::$fileSystem)->put(PoaAgreement::$storageFolderName .'\\'. $imageName, $contents);

                            if (!$storeFile) {
                                $imageName = NULL;
                            }
                        }

                        $insertData[] = [
                            'title'  => $oldPost->post_title,
                            'text'   => $oldPost->post_content,
                            'file'   => $imageName,
                            'old_id' => $postId
                        ];
                        $getMax++;
                    } else {
                        $insertData[] = [
                            'title'  => $oldPost->post_title,
                            'text'   => $oldPost->post_content,
                            'file'   => $imageName,
                            'old_id' => $postId
                        ];
                    }
                }

                if ($oldPost->post_type == 'certification') {
                    $oldPostMetaFiles = $this->mysqlOriginal->select("SELECT * FROM nl_postmeta WHERE post_id = (SELECT meta_value FROM nl_postmeta WHERE post_id = {$postId} AND meta_key = 'file' LIMIT 1) AND meta_key = '_wp_attached_file' LIMIT 1;");

                    if (!empty($oldPostMetaFiles[0]->meta_value)) {
                        $metaValue = $oldPostMetaFiles[0]->meta_value;

                        $getTranslateModelDocuments = TranslateModelDocument::where('old_id', $postId)->first();

                        if (!empty($getTranslateModelDocuments)) {
                            $pathInfos = pathinfo($metaValue);

                            if (!empty($pathInfos['extension'])) {
                                $id = $getTranslateModelDocuments->id;

                                $imageName = (!empty($pathInfos['filename'])) ? $pathInfos['filename']  . '_' . $id . '_' . '.' . $pathInfos['extension'] : $metaValue;

                                $imageUrl  = $this->domain . $metaValue;

                                $contents  = file_get_contents($imageUrl);

                                $storeFile = Storage::disk(TranslateModelDocument::$fileSystem)->put(TranslateModelDocument::$storageFolderName .'\\'. $imageName, $contents);

                                if ($storeFile) {
                                    TranslateModelDocument::where('id', $id)->update(['file' => $imageName]);
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($insertData)) {
                PoaAgreement::insert($insertData);
            }
        }

        PoaAgreement::enableAuditing();
        TranslateModelDocument::enableAuditing();
    }
}
