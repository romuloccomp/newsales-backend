<?php

namespace App\Http\Middleware;
use App\Services\CognitoService;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

use Closure;
use Illuminate\Http\Request;

class CognitoMiddleware
{
  public function handle($request, Closure $next)
    {
        $token = $request->cookie('accessToken');

        if (!$token) {
            return response()->json(['error' => '(401) Unauthorized'], 401);
        }

        $jwksUrl = sprintf(
            'https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json',
            env('AWS_COGNITO_REGION'),
            env('AWS_COGNITO_USER_POOL_ID')
        );

        $jwks = json_decode(file_get_contents($jwksUrl), true);
        $keys = JWK::parseKeySet($jwks);

        try {
            $decoded = JWT::decode($token, $keys);

            // salvar usuário no request para uso no refresh token
            $request->attributes->add(['user' => $decoded]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        return $next($request);
    }
    public function refresh(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->refresh($request->refresh_token, $request->cookie('userName'));

            return response()->json([
                'idToken' => $result['AuthenticationResult']['IdToken'],
                'accessToken' => $result['AuthenticationResult']['AccessToken']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Refresh token inválido'
            ], 401);
        }
    }
}
