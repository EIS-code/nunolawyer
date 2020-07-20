<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseModel;
use App\Client;
use App\Role;
use App\ModelHasRoles;
use App\TranslateModelDocument;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalClients = 0;
        $totalEditors = 0;

        $clients = Client::query();
        $editors = Client::query();

        $clients = $clients->select(Client::getTableName() . '.*')
                           ->join(ModelHasRoles::getTableName(), function($join) {
                               $join->on(ModelHasRoles::getTableName() . '.model_id', '=', Client::getTableName() . '.id');
                           })
                           ->join(Role::getTableName(), function($join) {
                               $join->on(ModelHasRoles::getTableName() . '.role_id', '=', Role::getTableName() . '.id');
                           })
                           ->whereRaw('lower(' . Role::getTableName() . '.name) = "client"')
                           ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved)
                           ->get();
        if (!empty($clients) && !$clients->isEmpty()) {
            $totalClients = $clients->count();
        }

        $editors = $editors->select(Client::getTableName() . '.*')
                           ->join(ModelHasRoles::getTableName(), function($join) {
                               $join->on(ModelHasRoles::getTableName() . '.model_id', '=', Client::getTableName() . '.id');
                           })
                           ->join(Role::getTableName(), function($join) {
                               $join->on(ModelHasRoles::getTableName() . '.role_id', '=', Role::getTableName() . '.id');
                           })
                           ->whereRaw('lower(' . Role::getTableName() . '.name) = "editor"')
                           ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved)
                           ->get();
        if (!empty($editors) && !$editors->isEmpty()) {
            $totalEditors = $editors->count();
        }

        $translateModelDocument         = TranslateModelDocument::query();
        $totalTranslateModelDocuments   = 0;
        $translateModelDocument = $translateModelDocument->where(TranslateModelDocument::getTableName() . '.is_removed', BaseModel::$notRemoved)
                                                         ->join(Client::getTableName(), TranslateModelDocument::getTableName() . '.client_id', '=', Client::getTableName() . '.id')
                                                         ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved)
                                                         ->get();
        if (!empty($translateModelDocument) && !$translateModelDocument->isEmpty()) {
            $totalTranslateModelDocuments = $translateModelDocument->count();
        }

        return view('dashboard', ['totalClients' => $totalClients, 'totalEditors' => $totalEditors, 'totalTranslateModelDocuments' => $totalTranslateModelDocuments]);
    }
}
