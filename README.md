# Laravel-crud


## Installation and Usage Instructions
```
composer require azizjon-aliev/laravel-crud
```

## What It Does
This package frees you from the chore of creating a Crud operation.

After installation, you will have access to this command:
```
php artisan make:crud MyCrud --Version=1 --service --resource --request 
```

Depending on the operation you choose, files will be created (Controller, Model, Requests, Resource, Service... )

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\MyCrud\StoreRequest;
use App\Http\Requests\V1\MyCrud\UpdateRequest;
use App\Http\Resources\V1\MyCrudResource;
use App\Services\Api\V1\MyCrudService;

class MyCrudController extends Controller
{
    protected $service;

    public function __construct(MyCrudService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $objects = $this->service->get();

        return MyCrudResource::collection($objects);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\V1\MyCrud\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $object = $this->service->create($data);

        return new MyCrudResource($object);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $object = $this->service->get_object($id);

        return new MyCrudResource($object);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\MyCrud\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $object = $this->service->update($data, $id);

        return new MyCrudResource($object);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = $this->service->delete($id);

        return response()->json(null, 204);

    }
}

```

file api.php
```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function() {
	Route::apiResource('mycruds', App\Http\Controllers\Api\V1\MyCrudController::class);
});

```

after that all you have to do is go to migration file, requests and resources, add required fields

run migrations
```
php artisan migrate
```

run server
```
php artisan serve
```

### Security

If you discover any security-related issues, please email [azizaliev2337@gmail.com](mailto:azizaliev2337@gmail.com) instead of using the issue tracker.
