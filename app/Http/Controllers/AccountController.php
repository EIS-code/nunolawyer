<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Exports\AccountExport;
use App\BaseModel;
use Maatwebsite\Excel\Facades\Excel;

class AccountController extends Controller
{

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

        return view('app.accounts.list', ['accounts' => $accounts, 'term' => $request, 'isFiltered' => $isFiltered]);
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
}
