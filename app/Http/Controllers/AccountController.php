<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Exports\AccountExport;
use App\BaseModel;
use App\Client;
use App\Role;
use App\ModelHasRoles;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class AccountController extends Controller
{
    public $isEditors;

    public function __construct()
    {
        $this->middleware(['permission:account_access'])->only('index');

        $this->accountExport = new AccountExport();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accountModel = new Account();
        $isFiltered   = false;
        $isExport     = $request->filled('export');

        $cleanup = $request->except(['export']);
        $request->query = new \Symfony\Component\HttpFoundation\ParameterBag($cleanup);

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $accounts = $accountModel::query();

        $this->isEditors();

        if ($this->isEditors) {
            $accounts->where('created_by', \Auth::user()->id);
        }

        if ($isFiltered) {
            if ($request->get('dt', false) || $request->get('dtt', false)) {
                $dt  = $request->get('dt', false);
                $dt  = ($dt) ? date('Y-m-d', strtotime($dt)) : NULL;
                $dtt = $request->get('dtt', false);
                $dtt = ($dtt) ? date('Y-m-d', strtotime($dtt)) : NULL;

                if (!empty($dt) && !empty($dtt)) {
                    $accounts->whereBetween('date', [$dt." 00:00:00", $dtt." 23:59:59"]);
                } elseif (!empty($dt)) {
                    $accounts->where('date', '>=', $dt." 00:00:00");
                } elseif (!empty($dtt)) {
                    $accounts->where('date', '<=', $dtt." 23:59:59");
                }
            }

            if ($request->get('s', false)) {
                $s = $request->get('s');

                $accounts->join(Client::getTableName(), function($join) use($s, $accountModel) {
                    $join->on($accountModel::getTableName() . '.client_id', '=', Client::getTableName() . '.id')
                         ->where(function($query) use($s) {
                            $query->where('first_name','LIKE',"%$s%")
                                  ->orWhere('last_name','LIKE',"%$s%")
                                  ->orWhere(DB::raw('concat(first_name, " ", last_name)'),'LIKE',"%$s%");
                         });
                });
            }

            if ($request->get('role', false)) {
                $role = $request->get('role');

                if (!$request->get('s', false)) {
                    $accounts->join(Client::getTableName(), $accountModel::getTableName() . '.client_id', '=', Client::getTableName() . '.id');
                }

                $accounts->join(ModelHasRoles::getTableName(), function($join) {
                    $join->on(ModelHasRoles::getTableName() . '.model_id', '=', Client::getTableName() . '.id');
                })
                ->join(Role::getTableName(), function($join) {
                    $join->on(ModelHasRoles::getTableName() . '.role_id', '=', Role::getTableName() . '.id');
                })
                ->whereRaw('lower(' . Role::getTableName() . '.name) = "' . $role . '"');
            }
        }

        $accounts->select($accountModel::getTableName() . '.*')
                 ->where($accountModel::getTableName() . '.is_removed', BaseModel::$notRemoved)
                 ->orderBy('updated_at', 'desc');
                 // ->groupBy($accountModel::getTableName() . '.client_id');

        if ($isExport) {
            $this->accountExport->collection = $accounts->get();

            return $this->exportCSV();
        }

        $accounts = $accounts->paginate(20);

        $roles    = Role::where('id', '!=', Client::$roleAdminId)->orderBy('id', 'ASC')->get();

        return view('app.accounts.list', ['accounts' => $accounts, 'term' => $request, 'isFiltered' => $isFiltered, 'roles' => $roles, 'isEditors' => $this->isEditors]);
    }

    /**
     * Export to csv
     */
    public function exportCSV()
    {
        if (empty($this->accountExport->collection())) {
            return false;
        }

        return Excel::download($this->accountExport, 'account.csv');
    }

    public function isEditors()
    {
        return $this->isEditors = \Auth::user()->hasRole('editor');
    }
}
