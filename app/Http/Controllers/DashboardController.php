<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseModel;
use App\Client;
use App\Role;
use App\ModelHasRoles;
use App\TranslateModelDocument;
use App\PoaAgreement;
use App\PurposeArticle;
use App\ClientPurposeArticle;
use App\OurFeePolicyDocument;
use App\TermsAndCondition;

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

        $poaAgreement       = PoaAgreement::query();
        $totalPoaAgreement  = 0;
        $poaAgreement       = $poaAgreement->where('is_removed', BaseModel::$notRemoved)->get();
        if (!empty($poaAgreement) && !$poaAgreement->isEmpty()) {
            $totalPoaAgreement = $poaAgreement->count();
        }

        $purposeArticle       = PurposeArticle::query();
        $totalPurposeArticle  = 0;
        $purposeArticle       = $purposeArticle->where(PurposeArticle::getTableName() . '.is_removed', BaseModel::$notRemoved)
                                               ->join(ClientPurposeArticle::getTableName(), PurposeArticle::getTableName() . '.id', '=', ClientPurposeArticle::getTableName() . '.purpose_article_id')
                                               ->join(Client::getTableName(), ClientPurposeArticle::getTableName() . '.client_id', '=', Client::getTableName() . '.id')
                                               ->where(ClientPurposeArticle::getTableName() . '.is_removed', BaseModel::$notRemoved)
                                               ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved)
                                               ->groupBy(PurposeArticle::getTableName() . '.id')
                                               ->get();
        if (!empty($purposeArticle) && !$purposeArticle->isEmpty()) {
            $totalPurposeArticle = $purposeArticle->count();
        }

        $ourFeePolicyDocument       = OurFeePolicyDocument::query();
        $totalOurFeePolicyDocument  = 0;
        $ourFeePolicyDocument       = $ourFeePolicyDocument->where('is_removed', BaseModel::$notRemoved)->get();
        if (!empty($ourFeePolicyDocument) && !$ourFeePolicyDocument->isEmpty()) {
            $totalOurFeePolicyDocument = $ourFeePolicyDocument->count();
        }

        $termsAndCondition       = TermsAndCondition::query();
        $totalTermsAndCondition  = 0;
        $termsAndCondition       = $termsAndCondition->where('is_removed', BaseModel::$notRemoved)->get();
        if (!empty($termsAndCondition) && !$termsAndCondition->isEmpty()) {
            $totalTermsAndCondition = $termsAndCondition->count();
        }

        return view('dashboard', [
            'totalClients' => $totalClients, 'totalEditors' => $totalEditors, 'totalTranslateModelDocuments' => $totalTranslateModelDocuments,
            'totalPoaAgreement' => $totalPoaAgreement, 'totalPurposeArticle' => $totalPurposeArticle, 'totalOurFeePolicyDocument' => $totalOurFeePolicyDocument, 'totalTermsAndCondition' => $totalTermsAndCondition
        ]);
    }
}
