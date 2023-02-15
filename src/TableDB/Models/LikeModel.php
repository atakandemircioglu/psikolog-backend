<?php

namespace Models;

class LikeModel extends Model
{
    protected static $tableName = "LIKES";
    protected $primaryKey = "id";
    protected $fillables = [
        "id",
        "comment_id",
        "user_id",
        "created_at",
        "updated_at"
    ];

    public function save(?string $primaryKey = null): mixed
    {
        $user = $this->user();
        $comment = $this->comment();
        if ($user) {
            $this->user_id = $user->id;
        } else {
            throw new Exception("User not found");
        }

        if ($comment) {
            $this->comment_id = $comment->id;
        } else {
            throw new Exception("Comment not found");
        }

        parent::save($primaryKey);
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function comment()
    {
        return $this->belongsTo(CommentModel::class, 'comment_id', 'id');
    }
}
