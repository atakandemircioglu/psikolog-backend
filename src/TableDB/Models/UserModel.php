<?php

class UserModel extends Model {
    protected static $tableName = "230514587437056";
    protected $fillables = [
        'id',
        'username',
        'password',
        'role',
        'group'
    ];

    public function getByUsername($username) {
        return $this->where('username', $username)->get()[0] ?? null;
    }
}
