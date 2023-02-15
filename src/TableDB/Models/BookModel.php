<?php

namespace Models;

include_once 'v1/lib/SheetDB/init.php';

class BookModel extends Model
{
    protected static $tableName = 'BOOKS';
    protected $primaryKey = "slug";
    protected $fillables = ["name", "slug", "color", "description", "created_at", "updated_at"];

    public function pages()
    {
        return $this->hasMany(PageModel::class, 'book_slug', $this->slug);
    }

    public function rootPages()
    {
        $roots = $this->whereFrom("PAGES", 'book_slug', $this->slug)
        ->where('up_slug', "")
        ->orderBy("created_at")->get();
        return array_map(function ($page) {
            return new PageModel($page);
        }, $roots);
    }

    public function findBySlug($slug)
    {
        return $this->findByPrimaryKeyOrFail($slug);
    }
}
