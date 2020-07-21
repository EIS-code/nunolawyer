<?php

namespace App\Http\Controllers;

use App\Role;
use App\BaseModel;
use App\CLient;
use App\ModelHasRoles;
use App\ClientPurposeArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EditorExport;
use DB;

class EditorController extends Controller
{
    private $editorExport;

    public function __construct()
    {
        $this->middleware(['permission:editors_create'])->only(['create','store']);
        $this->middleware(['permission:editors_show'])->only('show');
        $this->middleware(['permission:editors_edit'])->only(['edit','update']);
        $this->middleware(['permission:editors_delete'])->only('destroy');
        $this->middleware(['permission:editors_ban'])->only(['banEditor','activateEditor']);
        $this->middleware(['permission:editors_activity'])->only('activityLog');

        $this->editorExport = new EditorExport();
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

                $clients->join(ClientPurposeArticle::getTableName(), function($join) use($pur) {
                    $join->on(Client::getTableName() . '.id', '=', ClientPurposeArticle::getTableName() . '.client_id')
                         ->where(ClientPurposeArticle::getTableName() . '.purpose_article_id', (int)$pur)
                         ->where(ClientPurposeArticle::getTableName() . '.is_removed', BaseModel::$notRemoved);
                });
            }
        }

        $clients
                ->select(DB::raw($clientModel::getTableName() . '.*'))
                ->join(ModelHasRoles::getTableName(), function($join) {
                    $join->on(ModelHasRoles::getTableName() . '.model_id', '=', Client::getTableName() . '.id');
                })
                ->join(Role::getTableName(), function($join) {
                    $join->on(ModelHasRoles::getTableName() . '.role_id', '=', Role::getTableName() . '.id');
                })
                ->whereRaw('lower(' . Role::getTableName() . '.name) = "editor"')
                ->where(Client::getTableName() . '.is_removed', BaseModel::$notRemoved)
                ->where(Client::getTableName() . '.id', '!=', \Auth::user()->id);

        if ($isExport) {
            $this->editorExport->collection = $clients->get();

            return $this->exportCSV();
        }

        $clients = $clients->paginate(20);

        return view('app.editors.list', ['clients' => $clients, 'term' => $request, 'request' => $request, 'clientModel' => $clientModel, 'isFiltered' => $isFiltered]);
    }

    /**
     * Export to csv
     */
    public function exportCSV()
    {
        if (empty($this->editorExport->collection())) {
            return false;
        }

        return Excel::download($this->editorExport, 'list.csv');
    }
}
