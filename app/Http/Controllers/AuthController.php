<?php

namespace App\Http\Controllers;

use App\Services\CognitoService;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function createAccount(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->createUser($request->email, $request->password);

            return response()->json(['result' => $result]);
        } catch (CognitoIdentityProviderException $error) {
            return response()->json($error->getAwsErrorCode());
        }
    }

    public function confirmAccount(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->confirmSignup($request->email, $request->confirmation_code);

            return response(['message' => 'E-mail confirmado com sucesso!', 'code' => "EmailConfirmed"], 200);
        } catch (CognitoIdentityProviderException $error) {
            return response()->json(['message' => $error->getAwsErrorMessage(), 'error' => $error->getAwsErrorCode()], 401);
        }
    }

    public function resendConfirmationCode(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->resendConfirmationCode($request->email);

            return response()->json($result);
        } catch (CognitoIdentityProviderException $error) {
            return response()->json($error->getAwsErrorCode());
        }
    }

    public function login(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->login(
                $request->username,
                $request->password
            );

            $minutes = $request->remember ? 60 * 24 * 30 : 60;

            return response()->json(['success' => true])
                ->cookie('accessToken', $result['AuthenticationResult']['AccessToken'], $minutes, null, null, true, true)
                ->cookie('idToken', $result['AuthenticationResult']['IdToken'], $minutes, null, null, true, true)
                ->cookie('refreshToken', $result['AuthenticationResult']['RefreshToken'], 60 * 24 * 30, null, null, true, true)
                ->cookie('userName', $request->username, $minutes, null, null, true, true);
        } catch (CognitoIdentityProviderException $error) {
            $errorCode = $error->getAwsErrorCode();

            switch ($errorCode) {
                case 'NotAuthorizedException':
                    return response()->json(['error' => 'Usuário ou senha incorretos.', 'error_code' => $errorCode], 401);

                case 'UserNotFoundException':
                    return response()->json(['error' => 'Usuário ou senha incorretos.', 'error_code' => $errorCode], 404);

                case 'UserNotConfirmedException':
                    return response()->json(['error' => 'Por favor, confirme seu e-mail.', 'error_code' => $errorCode], 403);

                case 'PasswordResetRequiredException':
                    return response()->json(['error' => 'Redefinição de senha necessária.', 'error_code' => $errorCode], 403);

                default:
                    // Log de debug
                    return response()->json(['error_message' => 'Erro na autenticação. Tente novamente.', 'code' => $errorCode,], 500);
            }
        }
    }

    public function changePassword(Request $request, CognitoService $cognito)
    {
        $result = $cognito->changePassword(
            $request->username,
            $request->current_password,
            $request->new_password,
            $request->cookie('accessToken')
        );

        try {
            $result = $cognito->changePassword(
                $request->current_password,
                $request->new_password,
                $request->cookie('accessToken')
            );

            return response()->json(['cognito' => $result]);
        } catch (CognitoIdentityProviderException $error) {
            $errorCode = $error->getAwsErrorCode();

            switch ($errorCode) {
                case 'NotAuthorizedException':
                    return response()->json(['error' => 'Usuário não autorizado.'], 401);
                case 'UserNotFoundException':
                    return response()->json(['error' => 'Usuário não encontrado.'], 404);
                case 'InvalidPasswordException':
                    return response()->json(['error' => 'Senha inválida.'], 400);
                default:
                    // Log geral para o depuração
                    return response()->json(['error' => 'Erro ao alterar senha. Tente novamente.'], 500);
            }
        }
    }

    public function forgotPassword(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->forgotPassword($request->email);
            return response()->json($result);
        } catch (CognitoIdentityProviderException $error) {
            $errorCode = $error->getAwsErrorCode();
            return response()->json($errorCode);
        }
    }

    public function confirmForgotPassword(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->confirmForgotPassword($request->email, $request->password, $request->confirmation_code);
            return response()->json($result);
        } catch (CognitoIdentityProviderException $error) {
            $errorCode = $error->getAwsErrorCode();
            return response()->json($errorCode);
        }
    }

    public function logout()
    {
        return response()->json(['message' => 'Logout'])
            ->cookie('accessToken', '', -1)
            ->cookie('refreshToken', '', -1)
            ->cookie('userName', '', -1);
    }

    public function refresh(Request $request, CognitoService $cognito)
    {
        try {
            $result = $cognito->refresh($request->cookie('refreshToken'), $request->cookie('userName'));

            return response()->json([
                'id_token' => $result['AuthenticationResult']['IdToken'],
                'access_token' => $result['AuthenticationResult']['AccessToken'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Refresh token inválido' . $e->getMessage(),
            ], 401);
        }
    }
}
