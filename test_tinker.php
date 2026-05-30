<?php
$user = App\Models\User::where("level", "admin")->first();
$token = $user->createToken("auth_token")->plainTextToken;
$request = Illuminate\Http\Request::create("/api/santri", "GET");
$request->headers->set("Authorization", "Bearer " . $token);
$request->headers->set("Accept", "application/json");
$kernel = app()->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);
if ($response->getStatusCode() >= 500) {
    if (isset($response->exception)) {
        echo "ERROR: " . $response->exception->getMessage() . "\nFILE: " . $response->exception->getFile() . "\nLINE: " . $response->exception->getLine();
    } else {
        echo "500 Error: " . $response->getContent();
    }
} else {
    echo "Status: " . $response->getStatusCode();
}
