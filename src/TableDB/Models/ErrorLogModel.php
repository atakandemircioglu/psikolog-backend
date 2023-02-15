<?php

namespace Models;

class ErrorLogModel extends Model
{
    protected static $tableName = "ERROR_LOGS";
    protected $primaryKey = "id";
    protected $fillables = [
        "remote_addr",
        "forwarded_for",
        "request_uri",
        "request_method",
        "exception_class",
        "exception_message",
        "exception_trace",
        "exception_type",
    ];
}
