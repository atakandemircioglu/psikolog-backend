<?php

namespace Models;

class PageModel extends Model
{
    protected static $tableName = "PAGES";
    protected $primaryKey = "slug";
    protected $fillables = [
        'slug',
        'title',
        'order',
        'next_slug',
        'prev_slug',
        'up_slug',
        'created_at',
        'updated_at'
    ];

    protected static ?ModelPool $pool = null;

    public function destroy($cacheMode = null): mixed
    {
        if ($this->next_slug !== null && $this->next_slug !== "") {
            $nextPage = (new PageModel())->findByPrimaryKeyOrFail($this->next_slug);
            $nextPage->prev_slug = $this->prev_slug ?? null;
            $nextPage->save();
        }

        if ($this->prev_slug !== null && $this->prev_slug !== "") {
            $prevPage = (new PageModel())->findByPrimaryKeyOrFail($this->prev_slug);
            $prevPage->next_slug = $this->next_slug ?? null;
            $prevPage->save();
        }

        return parent::destroy();
    }

    public function roots()
    {
        return $this->where('up_slug', "")->orWhere('up_slug', null)->orderBy("updated_at")->get();
    }

    public function book()
    {
        return $this->belongsTo(BookModel::class, "slug", $this->book_slug);
    }

    public function next()
    {
        return $this->belongsTo(self::class, 'slug', $this->next_slug);
    }

    public function prev()
    {
        return $this->belongsTo(self::class, 'slug', $this->prev_slug);
    }

    public function up()
    {
        return $this->belongsTo(self::class, 'slug', $this->up_slug);
    }

    public function hasUpper()
    {
        return $this->up_slug !== null && $this->up_slug !== "";
    }

    public function hasPrevious()
    {
        return $this->prev_slug !== null && $this->prev_slug !== "";
    }

    public function hasNext()
    {
        return $this->next_slug !== null && $this->next_slug !== "";
    }

    public function subPages()
    {
        return $this->hasMany(self::class, 'up_slug', $this->slug);
    }

    public function findBySlug($slug)
    {
        $page = $this->where('slug', $slug)->get()[0];
        return new static($page);
    }

    public function comments()
    {
        $tmpQuery = new \Query();
        $comments = $this->hasMany(CommentModel::class, 'page_slug', $this->slug);

        $comments = array_map(function ($comment) {
            return $comment->toArray();
        }, $comments);
        $tmpQuery->result($comments);
        return $tmpQuery->where('reply_to', '')->orWhere('main_comment_id', '')->get();
    }
}
