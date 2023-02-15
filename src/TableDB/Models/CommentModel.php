<?php

namespace Models;

include_once 'v1/lib/SheetDB/init.php';
use NotFoundException;

class CommentModel extends Model
{
    protected static $tableName = "COMMENTS";
    protected $primaryKey = "id";
    protected $fillables = [
        "id",
        "page_slug",
        "user_id",
        "text",
        "main_comment_id",
        "reply_to",
        "created_at",
        "updated_at"
    ];
    protected $onDelete = [
        'replies',
        'exactReplies',
        'likes'
    ];

    public function save(?string $primaryKey = null): mixed
    {
        $user = $this->user();
        $page = $this->page();
        if (!$user) {
            throw new NotFoundException("User-{$this->user_id} not found");
        }

        if (!$page) {
            throw new NotFoundException("Page-{$this->page_slug} not found");
        }

        return parent::save($primaryKey);
    }

    public function user()
    {
        $user = $this->belongsTo(UserModel::class, 'user_id', $this->user_id);
        if ($user) {
            return $user->except("user_id", "id");
        }
        return null;
    }

    public function page()
    {
        return $this->belongsTo(PageModel::class, 'slug', $this->page_slug);
    }

    public function mainComment()
    {
        return $this->belongsTo(self::class, 'id', $this->main_comment_id);
    }

    public function replyTo()
    {
        $replyTo = $this->belongsTo(self::class, 'id', $this->reply_to);
        if ($replyTo) {
            return $replyTo->user();
        } else {
            return null;
        }
    }

    public function allReplies()
    {
        $replies = $this->replies();
        $exactReplies = $this->exactReplies();
        $all = array_unique(array_merge($replies, $exactReplies));
        return $all;
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'main_comment_id', $this->id);
    }

    public function exactReplies()
    {
        return $this->hasMany(self::class, 'reply_to', $this->id);
    }

    public function likes()
    {
        return $this->hasMany(LikeModel::class, 'comment_id', $this->id);
    }

    public function likeCount()
    {
        return count($this->likes());
    }

    public function isLikedByCurrentUser($userId)
    {
        $likes = $this->likes();
        foreach ($likes as $like) {
            if ($like->user_id === $userId) {
                return true;
            }
        }
        return false;
    }
}
