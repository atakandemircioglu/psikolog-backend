<?php

namespace Models;

class UserModel extends Model
{
    protected static $tableName = "USERS";
    protected $primaryKey = "user_id";
    protected $fillables = [
        "id",
        "user_id",
        "username",
        "photo",
        "created_at",
        "updated_at"
    ];

    public function isExists()
    {
        return count($this->where('user_id', $this->user_id)->get()) > 0;
    }
}
