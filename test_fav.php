<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::first();
Auth::login($user);
$buku = App\Models\Buku::first();
$req = Illuminate\Http\Request::create('/favorit/toggle', 'POST', ['buku_id' => $buku->id]);
$res = app(App\Http\Controllers\FavoritController::class)->toggle($req);
echo $res->getContent();
