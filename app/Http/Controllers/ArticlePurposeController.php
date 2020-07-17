<?php

namespace App\Http\Controllers;

use App\Role;
use App\BaseModel;
use App\PurposeArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class ArticlePurposeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:article_purpose_show_client']);
        $this->middleware(['permission:article_purpose_show_editor']);
        $this->middleware(['permission:article_purpose_create'])->only(['create','store']);
        $this->middleware(['permission:article_purpose_edit'])->only(['edit','update']);
        $this->middleware(['permission:article_purpose_delete'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $articlePurposeModel = new PurposeArticle();
        $isFiltered          = false;

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $articlePurposes = $articlePurposeModel::query();

        if ($isFiltered) {
            if ($request->get('s', false)) {
                $s = $request->get('s');

                $articlePurposes->where(function($query) use($s) {
                    $query->where('title','LIKE',"%$s%");
                });
            }
        }

        $articlePurposes = $articlePurposes->where('is_removed', BaseModel::$notRemoved)->paginate(20);

        return view('app.article_purpose.list', ['articlePurposes' => $articlePurposes, 'term' => $request, 'isFiltered' => $isFiltered]);
    }

    public function create()
    {
        return view('app.article_purpose.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
        ]);

        $validator->validate();

        $articlePurpose = PurposeArticle::create([
            'title' => $data['title']
        ]);

        if ($articlePurpose) {
            return redirect('article_purpose')->with('success', __("Article purpose created!"));
        }

        return redirect('article_purpose/create')->with('error', __("There has been an error!"));
    }

    public function edit(int $id)
    {
        $articlePurpose = PurposeArticle::find($id);

        if ($articlePurpose) {
            return view('app.article_purpose.edit', ['articlePurpose' => $articlePurpose]);
        }

        return redirect('article_purpose')->with('error', __("Not found!"));
    }

    public function update(Request $request, int $id)
    {
        $articlePurpose = PurposeArticle::find($id);

        if ($articlePurpose) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => ['required', 'string', 'max:255'],
            ]);

            $validator->validate();

            if ($articlePurpose->update($data)) {
                return redirect('article_purpose')->with('success', __("Article purpose updated!"));
            } else {
                return redirect('article_purpose')->with('error', __("There has been an error!"));
            }
        }

        return redirect('article_purpose')->with('error', __("Not found!"));
    }

    public function destroy(int $id)
    {
        $articlePurpose = PurposeArticle::find($id);

        if ($articlePurpose) {
            $isRemoved = $articlePurpose->update(['is_removed' => BaseModel::$removed]);

            if ($isRemoved) {
                return redirect('article_purpose')->with('success', __("Article purpose deleted!"));
            } else {
                return redirect('article_purpose')->with('error', __("There has been an error!"));
            }
        }

        return redirect('article_purpose')->with('error', __("Not found!"));
    }
}
