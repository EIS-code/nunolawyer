<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\BaseModel;
use App\FollowUp;

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

        $getClientInfos = $clientModel::where('id', $loggedInUserId)->where('is_removed', BaseModel::$notRemoved)->first();

        if (!empty($getClientInfos)) {
            $followUps = $followUpModel::query();

            if (!$getClientInfos->isSuperAdmin()) {
                $followUps->where('follow_by', $loggedInUserId);
            }

            $followUps = $followUps->with('clientFollowedBy')->where('is_removed', BaseModel::$notRemoved)->groupBy('follow_by')->get();
        }

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
