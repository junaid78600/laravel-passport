# laravel-passport

What is Laravel Sanctum ? A passport is a package that provides a simple and convenient way to implement token-based authentication for APIs. Passport allows you to issue access tokens that can be used to authenticate API requests, as well as refresh tokens that can be used to obtain new access tokens without requiring the user to log in again.

## Getting Started
### Step 1: Install Passport Auth

we need to install passport via the Composer package manager, so one your terminal and fire bellow command:
````
composer require laravel/passport
````
### Step 2: Register providers at config/app.php

After successfully install laravel passport, register providers. Open config/app.php . and put the bellow code :
````
 // config/app.php

'providers' =>[
 Laravel\Passport\PassportServiceProvider::class,
 ],
````
![image](https://user-images.githubusercontent.com/45033213/232249275-3e36605d-54eb-4706-8668-161428e174cb.png)

### Step 3: Run Migrations
````
php artisan migrate
````

### Step 4: Install passport for generate passport encryption keys

Now, you need to install laravel to generate passport encryption keys. This command will create the encryption keys needed to generate secure access tokens:

````
php artisan passport:install

````
### Step 5: Passport Configuration in Model

In this step, Navigate to App/Models directory and open User.php file. Then update the following code into User.php:

````
	
<?php
 
namespace App\Models;
 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
 
class User extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
 
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
 
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

````

### Step 6: Change the API driver
Navigate to config/auth.php and open auth.php file. Then Change the API driver to the session to passport. Put this code ‘driver’ => ‘passport’, in API :

````
   'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
````

### Step 7:  Create Passport Auth Controller

 you need to create a controller name PassportAuthController. Use the below command and create a controller
 
 ````
  php artisan make:controller Api\PassportAuthController

 ````
After that, you need to create some methods in PassportAuthController.php. So navigate to app/http/controllers/API directory and open PassportAuthController.php file. After that, update the following methods into your PassportAuthController.php file:

````
	
<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
 
class PassportAuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
  
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
  
        $token = $user->createToken('Laravel8PassportAuth')->accessToken;
  
        return response()->json(['token' => $token], 200);
    }
  
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
  
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
 
    public function userInfo() 
    {
 
     $user = auth()->user();
      
     return response()->json(['user' => $user], 200);
 
    }
}
````
### Step 8: Create APIs Route
````

use App\Http\Controllers\API\PassportAuthController;
 
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
  
Route::middleware('auth:api')->group(function () {
    Route::get('get-user', [PassportAuthController::class, 'userInfo']);
});
````

Note: in this regiter and login route run without token but you see in get-user use  middelware for passport token

### Step 9: Show error if not use token

Navigate to app/Exception/Handler.php and open it and add this
````
<?php

namespace App\Exceptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


class Handler extends ExceptionHandler
{
   
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];


    public function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'error' => 'Unauthenticated.'
        ], 401);
    }
}


and then add login route with name it api.php
Route::post('/login', [AuthController::class, 'login'])->name('login');

````

### Step 10:  Run laravel project by this command

````
 php artisan serve

````
### Step 11: Now Test Laravel REST API in Postman

### Register Api

![image](https://user-images.githubusercontent.com/45033213/232250985-7ea6da43-f541-4c6f-8ff0-7e45a8e91ec4.png)


### Login Api

![image](https://user-images.githubusercontent.com/45033213/232251167-19674c6d-bb3a-497f-98a6-62a4be409705.png)

### get-user Api with token( fetch from login)
Copy token from login api and put it in to header with Bearer keyword (shown in screenshot)

![image](https://user-images.githubusercontent.com/45033213/232251456-68cab24d-8613-4ef9-93db-545bb8bc93dc.png)










