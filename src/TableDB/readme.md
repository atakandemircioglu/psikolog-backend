# SheetDB
SheetDB is a simple mini orm framework for Jotform Tables.
It provides a simple interface to access and manage Jotform Tables like databases.
SheetDB is a lightweight mini framework that is easy to use and easy to extend.

- [SheetDB](#sheetdb)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Models](#models)
      - [**Creating Models**](#creating-models)
      - [**Getting Models**](#getting-models)
      - [**Inserting Models**](#inserting-models)
      - [**Updating Models**](#updating-models)
      - [**Deleting Models**](#deleting-models)
      - [**Populating Models**](#populating-models)
      - [**Creating Relationships**](#creating-relationships)
      - [**Creating belongsTo relationships (inverse hasOne)**](#creating-belongsto-relationships-inverse-hasone)
      - [**Creating hasMany relationships**](#creating-hasmany-relationships)
      - [**Mutators**](#mutators)
      - [**Accessors**](#accessors)
      - [**Custom Setters**](#custom-setters)
      - [**Using Pool**](#using-pool)
    - [QueryBuilder](#querybuilder)
      - [**Selecting Database**](#selecting-database)
      - [**Selecting Table**](#selecting-table)
      - [**Selecting Fields**](#selecting-fields)
      - [**Creating Conditions**](#creating-conditions)
      - [**Creating OR Conditions**](#creating-or-conditions)
      - [**Getting Data**](#getting-data)
      - [**Inserting Data**](#inserting-data)
      - [**Updating Data**](#updating-data)
      - [**Deleting Data**](#deleting-data)
  - [PLEASE CHECK THE SOURCE CODE TO SEE THE ALL FEATURES OF THE SHEETDB LIBRARY](#please-check-the-source-code-to-see-the-all-features-of-the-sheetdb-library)

## Installation
Copy SheetDB folder to your local env.
Delete files in Models folder below:
```
BookModel.php
CommentModel.php
ErrorLogModel.php
LikeModel.php
PageModel.php
UserModel.php
```

Check the permissions of the Cache.json file in the SheetDB directory.

Open SheetDB.php and set the following attributes

```
const SHEETDB_DB_PREFIX = '{DB_PREFIX}'; // Default : DB
const SHEETDB_TABLE_PREFIX = '{TBL_PREFIX}'; // Default : TBL
const SHEETDB_DB_NAME = "{DB_NAME}"; // Your database name
const JF_API_KEY = '{JF_API_KEY}'; // Jotform API key
```


## Usage

### Models

#### **Creating Models**
SheetDB includes a basic model generator (servant) that can be used to create models. To create a model with servant open terminal and go to /SheetDB folder.

Type:
```
php servant.php create:model [modelName]
```

Or you can go to /SheetDB/Models folder and create a php file named as [modelName]Model.php a basic model should look like below.

```
<?php

namespace Models;

class ExampleModel extends Model
{
    protected static $tableName = "EXAMPLES";
    protected $primaryKey = "id";
    protected $fillables = [];
}

```
you should change the tableName and fillables attributes to your table name and it fields. Example $fillables attribute should look like below.

```
protected $fillables = [
        "id",
        "user_id",
        "username",
        "photo",
        "created_at",
        "updated_at"
    ];
```

#### **Getting Models**
To get the models from the database, you can use the following methods:
$findByPrimaryKey()$ method:
```
$model = (new ExampleModel())->findByPrimaryKey($id);
```
or $findByPrimaryKeyOrFail()$ method:

```
$model = (new ExampleModel())->findByPrimaryKeyOrFail($id); // This will throw NotFoundException if cannot find the model
```
To get the all models you can use $all()$ function
```
$models = (new ExampleModel())->all();
```
To get the models by a specific condition you can use $where()$ function
```
$models = (new ExampleModel())->where("username", "username");
```
Or you can use $where()$ function to get the models by multiple conditions with $where()$ and $orWhere()$ functions
```
$models = (new ExampleModel())->where("username", $username)->where("id", $id);
```

#### **Inserting Models**
To insert a new record to database via model. simply use
```
$model = new ExampleModel();
$model->create();
```

#### **Updating Models**
To update an existing new record to database via model. simply use
```
$model = (new ExampleModel())->findByPrimaryKey($primaryKey);
//TODO: change the values
$model->update();
```

#### **Deleting Models**

To perform a deletion of a model, simply call the $destroy()$ method
```
$model = (new ExampleModel())->findByPrimaryKey($primaryKey);
$model->destroy();
```

#### **Populating Models**
To populate the model with data, you can use the $fill()$ method
```
$data = [
    'name' => 'John',
    'lastname' => 'Doe'
];
$model = new ExampleModel();
$model->fill($data);
```
 or you can use the $populate()$ method to fill the model. Be careful with this method because it will changes the all properties of the model
 ```
$model = new ExampleModel();
$model->populate($data);
 ```

#### **Creating Relationships**

With assistance of QueryBuilder you can create (weak)relationships between tables. Originally SheetDB has two different relationships but you can easily extend relationships by adding new relationships to Model.php .

#### **Creating belongsTo relationships (inverse hasOne)**

For example a PageModel has a belongsTo relationship with BookModel. To define a relationship like this betwwen BookModel and PageModel you should create a method in PageModel.php like below: 

```
public function book()
    {
        return $this->belongsTo(BookModel::class, "slug", $this->book_slug);
    }
```
You can see the method definition in Model.php => belongsTo() method. The first argument is the model class name and the second argument is the foreign key field name.Third argument is the foreign key value of the current model.

#### **Creating hasMany relationships**

For example a CommentModel has a hasMany relationship with Like. To define a relationship like this betwwen LikeModel and CommentModel you should create a method in CommentModel like below: 

```
public function likes()
    {
        return $this->hasMany(LikeModel::class, 'comment_id', $this->id);
    }
```

#### **Mutators**

To set the value of a field before inserting it to the database, you can use the mutators.

To define a mutator for a field you can use the $mutators property.
```
protected $mutators = [
    'name' => 'trim'
];
```

#### **Accessors**

To format data after the field is retrieved from the database, you can use the accessors. This will doesn't affect the data in the database.
```
protected $accessors = [
    'name' => 'ucwords'
];
```

#### **Custom Setters**
To format data when setting the value of a field, you can use the custom setters. Custom setters gives you use the ability to use custom function with parameters.
```
protected $customSetters = [
    'name' => 'ucwords'
];
```

#### **Using Pool**
In SheetDB Model Pool is not a classic pool. The purpose of the pool is to provide mass insert functionality.

To use the pool, you must define a pool in the model. Otherwise the model will use the generic ModelPool.
```
    protected bool $usePool = true;
```


To activate the pool you must use the Model's $usePool() method.
```
$model = new ExampleModel();
$model->usePool();
```
If you perform a create, update, or delete operation on a pooled model. Model will be released from pool.

To perform a mass insert with a pooled model use the pool's $bulkInsertFromPool()$ method. This will insert all pooled models into the table

```
$model->pool()->bulkInsertFromPool();
```

To disable the pool use the $dontUsePool()$ method.
```
$model->dontUsePool();
```

### QueryBuilder

SheetDB's query builder is Query.php in the SheetDB folder. It provides a sql like query methods to access Jotform Tables for retrieving, creating, updating and deleting data. To perform a raw queries on a table or database. You can use the SheetDB class as an entry point.

#### **Selecting Database**
To select a database you can use the $use(dbName)$  method. This will returns an instance of QueryBuilder after that you can use the query methods to retrieve, create, update and delete data.

```
SheetDB::use('DB');
```

#### **Selecting Table**
To select a table you can use the $from(tableName)$  method. This will returns an instance of QueryBuilder after that you can use the query methods to retrieve, create, update and delete data from the selected table.

```
SheetDB::use('DB')->from(tableName);
SheetDB::from(tableName) // This will use the SHEETDB_DB_NAME property as database
```
#### **Selecting Fields**
To select fields you can use the $select(fields)$  method. This will returns an instance of QueryBuilder after that you can use the query methods to retrieve, create, update and delete data from the selected table. Example :

```
SheetDB::use('DB')
->from('USERS')
->select('USERNAME', 'EMAIL');
```

#### **Creating Conditions**
To create conditions you can use the $where(field, value)$  method. This will returns an instance of QueryBuilder after that you can use the query methods to retrieve, create, update and delete data from the selected table. Example :

```
SheetDB::use('DB')->from('USERS')->where('USERNAME', 'John');
```

#### **Creating OR Conditions**

To create OR conditions you can use the $orWhere(field, value)$  method. This will returns an instance of QueryBuilder after that you can use the query methods to retrieve, create, update and delete data from the selected table. Example :

```
SheetDB::use('DB')->from('USERS')->where('USERNAME', 'John')
->orWhere('AGE', >, '30');
```

#### **Getting Data**
To get the data you can use the $get()$  method. This will returns an array with retrieved rows. Example:

```
SheetDB::use('DB')->from('USERS')->where('USERNAME', 'John')->get();
```


#### **Inserting Data**
To insert data you can use the $insert(data)$  method. This will returns an array with the results of the insert operation

```
SheetDB::use('DB')->table('USERS')->insert([
    'USERNAME' => 'John',
    'LASTNAME' => 'Doe' 
    ]); 
```
Note that you can also pass multiple arrays to ->insert() method to perform a mass insert query.

#### **Updating Data**
To update data you can use the $update(data)$  method. This will returns an array with the results of the update operation

```
SheetDB::use('DB')->table('USERS')->where('USERNAME', 'John')->update([
    'LASTNAME' => 'Doe' 
    ]); 

```

Note that you can also pass multiple arrays to ->update() method to perform a mass update query.

#### **Deleting Data**
To delete data you can use the $delete()$  method. This will returns an array with the results of the delete operation

```
SheetDB::use('DB')->table('USERS')->where('USERNAME', 'John')->delete();
```
Note that you must limit the number of rows with where | orWhere clauses to prevent the deletion of all rows in the database


#
## PLEASE CHECK THE SOURCE CODE TO SEE THE ALL FEATURES OF THE SHEETDB LIBRARY
#

