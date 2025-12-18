---
alwaysApply: true
---

# You need to follow these rules:

- This project is a Laravel 12 application which depends on and uses laravel/ui package's auth scaffolding with Bootstrap for styling and design. But always check this project's `composer.json` and `package.json` for dependencies to make sure that all code you generate follows the proper version of dependencies. 
- Always use Bootstrap for styling and building user interfaces.
- Before generating code, make sure that same functionalities, features are not available from any of the dependencies/libraries/packages installed, If already available then use the same for implementing new features/functionality/writing new code. i.e. don't re-invent the wheel, use dependencies when can use them. See package.json and composer.json files for doing this.
- Always use Yajra's Laravel Datatables package installed in this project to return response for datatable endpoints when needed.
- Always use datatables npm module from datatables.net to create a dynamic list / table of data when needed. Always create a server side datatable, never create client side datatables when needed.
- This project has jquery, jquery.repeater, select2 and jquery-ui as dependencies so make use of them when needed.
- We can run this project locally using command `php artisan serve` (application server) or `composer dev` (application server at `http://localhost:8000`+ vite at `http://localhost:5173`). Usually I run this myself so whenever you think of running this application first make sure that I'm not already running it on the mentioned ports.