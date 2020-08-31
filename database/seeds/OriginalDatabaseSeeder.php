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

class OriginalDatabaseSeeder extends Seeder
{
    private $posIds  = [];
    private $feesIds = [];
    private $prsIds  = [];
    private $cpsIds  = [];
    private $tcsIds  = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->mysql 		 = \DB::connection('mysql');
        $this->mysqlOriginal = \DB::connection('mysql_original');

        // Clients
        $this->runClients();

        // Other stiff.
        $this->mastersClone();

        // Clinet & Editor other infos.
        $this->runOtherStuff();
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
                    $roles    = (!empty($userMeta['nl_capabilities'])) ? unserialize($userMeta['nl_capabilities']) : [];
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

                            if (preg_match("/^tac_/i", $key) && preg_match("/_custom_editor/i", $key)) {
                                $this->tcsIds[$userId][] = $meta;
                                // $_SESSION['terms'][$userId][] = $meta;
                            }
                        }
                    }

                    if ($isClient || $isEditor) {
                        $create = Client::create($insertData[$index]);

                        if ($create) {
                            if ($isClient) {
                                $create->assignRole($roleClient);
                            } else {
                                $create->assignRole($roleEditor);
                            }
                        }
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
                            ClientPurposeArticle::create([
                                'is_last_inserted'   => $isLastInserted,
                                'purpose_article_id' => $getPos->id,
                                'client_id'          => $getUser->id
                            ]);
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
                            'proposed_lawyer_fee'     => (!empty($value['proposed_lawyer_fee'])) ? $value['proposed_lawyer_fee'] : NULL,
                            'received_lawyer_fee'     => (!empty($value['received_lawyer_fee'])) ? $value['received_lawyer_fee'] : NULL,
                            'missing_lawyer_fee'      => (!empty($value['missing_lawyer_fee'])) ? $value['missing_lawyer_fee'] : NULL,
                            'proposed_government_fee' => (!empty($value['proposed_government_fee'])) ? $value['proposed_government_fee'] : NULL,
                            'received_government_fee' => (!empty($value['received_government_fee'])) ? $value['received_government_fee'] : NULL,
                            'missing_government_fee'  => (!empty($value['missing_government_fee'])) ? $value['missing_government_fee'] : NULL,
                            'client_id'               => $getUser->id
                        ]);
                    }
                }
            }
        }

        if (!empty($this->prsIds)) {
            foreach ($this->prsIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value['date'])) {
                            continue;
                        }

                        ClientEmailProgressReport::create([
                            'date'            => $value['date'],
                            'progress_report' => (!empty($value['progress_report'])) ? $value['progress_report'] : NULL,
                            'client_id'       => $getUser->id
                        ]);
                    }
                }
            }
        }

        if (!empty($this->cpsIds)) {
            foreach ($this->cpsIds as $userId => $values) {
                $getUser = Client::where('old_id', $userId)->first();

                if (!empty($getUser) && !empty($values)) {
                    foreach ((array)$values as $index => &$value) {
                        if (empty($value['date'])) {
                            continue;
                        }

                        ClientPrivateInformation::create([
                            'date'                => $value['date'],
                            'private_information' => (!empty($value['private_information'])) ? $value['private_information'] : NULL,
                            'client_id'           => $getUser->id
                        ]);
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

                        ClientTermsAndCondition::create([
                            'terms_and_conditions' => $value,
                            'client_id'            => $getUser->id
                        ]);
                    }
                }
            }
        }
    }
}
