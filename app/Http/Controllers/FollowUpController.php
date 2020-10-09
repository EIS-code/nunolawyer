<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\BaseModel;
use App\FollowUp;
use App\Role;
use App\ModelHasRoles;

class FollowUpController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:follow_up_access'])->only('index');
        $this->middleware(['permission:follow_up_show'])->only('show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedInUserId = \Auth::user()->id;
        $followUpModel  = new FollowUp();
        $clientModel    = new Client();
        $followUps      = [];

        /*$getClientInfos = $clientModel::where('id', $loggedInUserId)->where('is_removed', BaseModel::$notRemoved)->first();

        if (!empty($getClientInfos)) {
            $followUps = $followUpModel::query();

            if (!$getClientInfos->isSuperAdmin()) {
                $followUps->where('follow_by', $loggedInUserId);
            }

            $followUps = $followUps->with('clientFollowedBy')->where('is_removed', BaseModel::$notRemoved)->groupBy('follow_by')->get();
        }*/

        $followUps = $clientModel->select($clientModel::getTableName() . '.*')
                        ->join(ModelHasRoles::getTableName(), function($join) use($clientModel) {
                            $join->on(ModelHasRoles::getTableName() . '.model_id', '=', $clientModel::getTableName() . '.id');
                        })
                        ->join(Role::getTableName(), function($join) {
                            $join->on(ModelHasRoles::getTableName() . '.role_id', '=', Role::getTableName() . '.id');
                        })
                        ->whereRaw('lower(' . Role::getTableName() . '.name) = "' . $clientModel::$roleEditors .'"')
                        ->where($clientModel::getTableName() . '.is_removed', BaseModel::$notRemoved)
                        ->where($clientModel::getTableName() . '.id', '!=', \Auth::user()->id)
                        ->orderBy($clientModel::getTableName() . '.id', 'DESC')
                        ->get();

        return view('app.follow_up.list', ['followUps' => $followUps]);
    }

    public function show(int $followId)
    {
        $followUps      = [];
        $loggedInUserId = \Auth::user()->id;
        $clientModel    = new Client();
        $getClientInfos = $clientModel::where('id', $loggedInUserId)->where('is_removed', BaseModel::$notRemoved)->first();

        if ($followId != $loggedInUserId && !$getClientInfos->isSuperAdmin()) {
            abort(404);
        }

        if (!empty($followId)) {
            $followUpModel  = new FollowUp();

            $followUps = $followUpModel::query();

            $followUps = $followUps->with('clientPurposeArticles')
                                   ->leftJoin(Client::getTableName(), Client::getTableName() . '.id', '=', $followUpModel::getTableName() . '.client_id')
                                   ->where($followUpModel::getTableName() . '.follow_by', $followId)
                                   ->where($followUpModel::getTableName() . '.is_removed', BaseModel::$notRemoved)
                                   ->get();
        }

        return view('app.follow_up.show', ['followUps' => $followUps, 'clientModel' => $clientModel]);
    }
}
