<?php

namespace App\Http\Controllers;

use App\BaseModel;
use App\TermsAndCondition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class TermsAndConditionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:terms_and_conditions_access'])->only(['index']);
        $this->middleware(['permission:terms_and_conditions_create'])->only(['create','store']);
        $this->middleware(['permission:terms_and_conditions_edit'])->only(['edit','update']);
        $this->middleware(['permission:terms_and_conditions_delete'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $termsAndConditionModel = new TermsAndCondition();
        $isFiltered                = false;

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $termsAndConditions = $termsAndConditionModel::query();

        if ($isFiltered) {
            if ($request->get('s', false)) {
                $s = $request->get('s');

                $termsAndConditions->where(function($query) use($s) {
                    $query->where('title','LIKE',"%$s%")
                          ->orWhere('text','LIKE',"%$s%");
                });
            }
        }

        $termsAndConditions = $termsAndConditions->where('is_removed', BaseModel::$notRemoved)->paginate(20);

        return view('app.terms_and_conditions.list', ['termsAndConditions' => $termsAndConditions, 'term' => $request, 'isFiltered' => $isFiltered]);
    }

    public function create()
    {
        return view('app.terms_and_conditions.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'text'  => ['nullable', 'string']
        ]);

        $validator->validate();

        $termsAndCondition = TermsAndCondition::create([
            'title' => $data['title'],
            'text'  => $data['text']
        ]);

        if ($termsAndCondition) {
            return redirect('terms_and_conditions')->with('success', __("Terms and conditions created!"));
        }

        return redirect('terms_and_conditions/create')->with('error', __("There has been an error!"));
    }

    public function edit(int $id)
    {
        $termsAndCondition = TermsAndCondition::find($id);

        if ($termsAndCondition) {
            return view('app.terms_and_conditions.edit', ['termsAndCondition' => $termsAndCondition]);
        }

        return redirect('terms_and_conditions')->with('error', __("Not found!"));
    }

    public function update(Request $request, int $id)
    {
        $termsAndCondition = TermsAndCondition::find($id);

        if ($termsAndCondition) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => ['required', 'string', 'max:255'],
                'text'  => ['nullable', 'string']
            ]);

            $validator->validate();

            if ($termsAndCondition->update($data)) {
                return redirect('terms_and_conditions')->with('success', __("Terms and conditions updated!"));
            } else {
                return redirect('terms_and_conditions')->with('error', __("There has been an error!"));
            }
        }

        return redirect('terms_and_conditions')->with('error', __("Not found!"));
    }
 
    public function destroy(int $id)
    {
        $termsAndCondition = TermsAndCondition::find($id);

        if ($termsAndCondition) {
            $isRemoved = $termsAndCondition->update(['is_removed' => BaseModel::$removed]);

            if ($isRemoved) {
                return redirect('terms_and_conditions')->with('success', __("Terms and conditions deleted!"));
            } else {
                return redirect('terms_and_conditions')->with('error', __("There has been an error!"));
            }
        }

        return redirect('terms_and_conditions')->with('error', __("Not found!"));
    }
}
