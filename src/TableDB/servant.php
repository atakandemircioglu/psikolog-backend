<?php

const TEXT_BOLD = "1";
const TEXT_DIM = "2m";
const TEXT_UNDERLINE = "4m";
const TEXT_BLINK = "5m";
const TEXT_REVERSE = "7m";
const TEXT_HIDDEN = "8m";
const TEXT_RESET = "0m";

const TEXT_RED = "31m";
const TEXT_GREEN = "32m";
const TEXT_YELLOW = "33m";
const TEXT_BLUE = "34m";
const TEXT_MAGENTA = "35m";
const TEXT_CYAN = "36m";
const TEXT_WHITE = "37m";

const BG_RED = "41m";
const BG_GREEN = "42m";
const BG_YELLOW = "43m";
const BG_BLUE = "44m";
const BG_MAGENTA = "45m";
const BG_CYAN = "46m";


if ($argv[1] == "--help") {
    if (!$argv[2]) {
        echo "\e[2J";
        echo "\e[1;1H";
        echoStyled("help --[command:name] to get more information about the command\n", TEXT_BOLD, BG_GREEN);
        echoStyled("\n \n", TEXT_RESET);
        echoStyled("create:model [ModelName] -> creates a new model in SheetDB/Models \n", TEXT_BOLD, BG_CYAN);
        echoStyled("create:seeder [SeederName] -> creates a new seeder in SheetDB/Database/Seeders \n", TEXT_BOLD, BG_CYAN);
        echoStyled("create:exception [ExceptionName] -> creates a new exception in SheetDB/Exception", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        exit(0);
    }

    if ($argv[2] == "create:model") {
        echo "\e[2J";
        echo "\e[1;1H";
        echoStyled("create:model [ModelName] -> creates a new model in SheetDB/Models \n", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        echoStyled("Example: create:model User \n", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        exit(0);
    }

    if ($argv[2] == "create:seeder") {
        echo "\e[2J";
        echo "\e[1;1H";
        echoStyled("create:seeder [SeederName] -> creates a new seeder in SheetDB/Database/Seeders \n", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        echoStyled("Example: create:seeder User \n", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        exit(0);
    }

    if ($argv[2] == "create:exception") {
        echo "\e[2J";
        echo "\e[1;1H";
        echoStyled("create:exception [ExceptionName] -> creates a new exception in SheetDB/Exception", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        echoStyled("Example: create:exception SheetDB \n", TEXT_BOLD, BG_CYAN);
        echoStyled("\n \n", TEXT_RESET);
        exit(0);
    }
}

function createTableName($className)
{
    $words = preg_filter("/[A-Z]/", "_$0", $className);
    $words = preg_replace("/^(_)/", "", $words);
    $words = $words . "S";
    $words = strtoupper($words);
    return $words;
}

function echoStyled($string, ...$codes)
{
    $codes = implode(";", $codes);
    echo "\e[" . $codes . $string . "\033[0m";
}

if ($argv[1] == "create:model") {
    createModel($argv[2]);
} elseif ($argv[1] == "create:seeder") {
    createSeeder($argv[2]);
} elseif ($argv[1] == "create:exception") {
    createException($argv[2]);
}

function createException($exceptionName)
{
    $exceptionFilename = $exceptionName . "Exception" . ".php";
    $folder = __DIR__ . "/Exceptions";
    

    if (file_exists($folder . "/" . $exceptionFilename)) {
        echoStyled("Exception already exists\n", TEXT_BOLD, TEXT_RED);
        exit(1);
    }

    $content = sprintf(
        '<?php

namespace Exceptions;

class %s extends SheetDBException
{
}
',
        $exceptionName . "Exception"
    );

    file_put_contents($folder . "/" . $exceptionFilename, $content);
    echoStyled("Exception created successfully!\n", TEXT_BOLD, TEXT_GREEN);
}

function createSeeder($seederName)
{
    $seederFileName = $seederName . "Seeder" . ".php";
    $folder = __DIR__ . "/Database/Seeders";
    $tableName = createTableName($seederName);
    $className = $seederName . "Seeder";

    if (file_exists($folder . "/" . $seederFileName)) {
        echoStyled("Seeder already exists!\n", TEXT_BOLD, TEXT_RED);
        exit(1);
    }

    $content = sprintf(
        '<?php
class %s extends Seeder
{
    protected $table = "%s";
    public function run()
    {
        $this->seed();
    }

    protected function seed()
    {
        // Create your seed here
    }
}
',
        $className,
        $tableName
    );

    file_put_contents($folder . "/" . $seederFileName, $content);
    echoStyled("Seeder created successfully!\n", TEXT_BOLD, TEXT_GREEN);
}

function createModel($modelName)
{
    $modelFileName = $modelName . "Model" . ".php";
    $folder = __DIR__ . "/Models";
    $tableName = strtoupper($modelName . "S");
    $file = $folder . "/" . $modelFileName;
    
    if (file_exists($file)) {
        echo "Model already exists.\n";
        exit(0);
    }

    $content = sprintf(
        '<?php

namespace %s;

class %s extends Model
{
    protected static $tableName = "%s";
    protected $primaryKey = "%s";
    protected $fillables = [];
}
',
        "Models",
        $modelName . "Model",
        $tableName,
        "id",
    );
    file_put_contents($file, $content);
    echo "Model {$modelName}Model created successfully!\n";
}


function showHelp()
{
}

function test()
{
    print("it works!");
}
