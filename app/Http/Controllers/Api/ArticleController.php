<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Http\Resources\Article as ArticleResource;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $article = Article::with('files')->get();
       
        return ArticleResource::collection($article);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = $request->isMethod('put') ? Article::findOrFail($request->id) : new Article;

        $article->id = $request->input('id');
        $article->title = $request->input('title');
        $article->text = $request->input('text');
    
        if($request->isMethod('delete') && $request->images) {
            $article::removeTableFiles($request->images);
        } else {
            if($article->save()) {
                if ($request->isMethod('post') && is_array($request->images) || $request->isMethod('put') && is_array($request->images)) {
                    foreach ($request->images as $key => $item) {
                        $article::saveTableFiles(json_decode($request->images[$key])->id, $article->id);    
                    }
                }
            }
        }
        return new ArticleResource($article);
    }

    public function addExcelData(Request $request)
    {
        //TODO уведомлять пользователя о незаполненых пустых полях - валидация
        //TODO создать метод в модели и вызвать
        $data = [];
        foreach ($request->input() as $key => $value) {
            $article = new Article;
            $article->title = array_values($request[$key])[0] == null ? '' : array_values($request[$key])[0];
            $article->text = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
            $article->save();
            $data[$key] = new ArticleResource($article);
        }
        return response()->json(['errors' => [], 'data' => $data, 'status' => 200], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);
        return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if($article->delete()) {
            return new ArticleResource($article);
        }
    }
}
