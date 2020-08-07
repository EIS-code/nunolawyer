<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Audit\Tools\AuditMessages;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogController extends Controller
{
    public $excepts = [
        // 'ClientPurposeArticle'
    ];

	public function __construct()
    {
        if (!empty($this->excepts)) {
            foreach ($this->excepts as &$model) {
                $model = 'App\\'.$model;
            }
        }

        $this->middleware(['permission:activitylog_show'])->only('show');
        $this->middleware(['permission:activitylog_delete'])->only('destroy');
    }

    /**
     * Display a listing of the audits.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $audits = Audit::where('new_values','NOT LIKE','%remember_token%')->whereNotNull('user_id')->whereNotIn('auditable_type', $this->excepts)->orderBy('created_at','DESC');

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

        return view('app.activitylog.list', ['audits' => $audits]);
    }

    /**
     * Display the specified audit details.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $audit = Audit::find($id);

        if($audit){
        	$audit['event_message'] = AuditMessages::getMessage($audit);
        	
            return view('app.activitylog.show', ['audit' => $audit]);
        } else {
            return redirect('activitylog')->with('error',__("Activity not found!"));
        }
    }

    /**
     * Remove the specified audit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $audit = Audit::find($id);

        if($audit){
            $audit->delete();
            return redirect('activitylog')->with('success',__("Activity deleted!"));
        } else {
            return redirect('activitylog')->with('error',__("Activity not found!"));
        }
    }
}
