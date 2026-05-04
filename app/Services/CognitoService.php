<?php

namespace App\Services;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

class CognitoService
{
    private $client;

    public function __construct()
    {
        $this->client = new CognitoIdentityProviderClient([
            'region' => env('AWS_COGNITO_REGION'),
            'version' => 'latest',
            'credentials' => false,
        ]);
    }

    public function createUser($email, $password)
    {
        $result = $this->client->signUp([
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'Username' => $email,
            'Password' => $password,
            'UserAttributes' => [
                [
                    'Name' => 'email',
                    'Value' => $email,
                ],
            ],
            // 'SecretHash' => $this->secretHash($email),
        ]);

        return $result;
    }

    public function confirmSignup($email, $confirmationCode)
    {
        $result = $this->client->confirmSignUp([
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'Username' => $email,
            'ConfirmationCode' => $confirmationCode,
        ]);
    }

    public function resendConfirmationCode($email)
    {
        try {
            $this->client->resendConfirmationCode([
                'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
                'Username' => $email,
            ]);
        } catch (CognitoIdentityProviderException $error) {
            return response()->json($error->getAwsErrorCode());
        }
    }

    public function login($email, $password)
    {
        return $this->client->initiateAuth([
            'AuthFlow' => 'USER_PASSWORD_AUTH',
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'AuthParameters' => [
                'USERNAME' => $email,
                'PASSWORD' => $password,
                // 'SECRET_HASH' => $this->secretHash($email),
            ],
        ]);
    }

    public function refresh($refreshToken, $userName)
    {
        return $this->client->initiateAuth([
            'AuthFlow' => 'REFRESH_TOKEN_AUTH',
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'AuthParameters' => [
                'REFRESH_TOKEN' => $refreshToken,
                // 'SECRET_HASH' => $this->secretHash($userName),
            ],
        ]);
    }

    public function resetPassword($email)
    {
        return $this->client->forgotPassword([
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'Username' => $email,
            // 'SecretHash' => $this->secretHash($email),
        ]);
    }

    public function confirmResetPassword($email, $confirmationCode, $newPassword)
    {
        return $this->client->confirmForgotPassword([
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'Username' => $email,
            'ConfirmationCode' => $confirmationCode,
            'Password' => $newPassword,
            // 'SecretHash' => $this->secretHash($email),
        ]);
    }

    public function changePassword($current_password, $new_password, $access_token)
    {
        $result = $this->client->changePassword([
            'AccessToken' => $access_token,
            'PreviousPassword' => $current_password,
            'ProposedPassword' => $new_password,
        ]);

        return $result;
    }

    public function forgotPassword ($email)
    {
        $result = $this->client->forgotPassword([
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'Username' => $email,
        ]);

        return $result;
    }

    public function confirmForgotPassword($userName, $password, $confirmationCode)
    {
        $result = $this->client->confirmForgotPassword([
            'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
            'ConfirmationCode' => $confirmationCode,
            'Username' => $userName,
            'Password' => $password,
        ]);

        return $result;
    }

    public function disableUser($username)
    {
        $results = $this->client->adminDisableProviderForUser(
            [
                'UserPoolId' => env('AWS_COGNITO_USER_POOL_ID'),
                'Username' => $username,
            ]
        );

        return $results;
    }

    public function logout()
    {
        return response()->json(['success' => true])
            ->cookie('accessToken', '', -1)
            ->cookie('refreshToken', '', -1);
    }

    private function secretHash($username)
    {
        return base64_encode(
            hash_hmac(
                'sha256',
                $username . env('AWS_COGNITO_CLIENT_ID'),
                env('AWS_COGNITO_CLIENT_SECRET'),
                true
            )
        );
    }

    // public function forceChangePassword($username, $newPassword, $sessionId)
    // {
    //     try {
    //         $result = $this->client->respondToAuthChallenge([
    //             'ChallengeName' => 'NEW_PASSWORD_REQUIRED',
    //             'ClientId' => env('AWS_COGNITO_CLIENT_ID'),
    //             'Session' => $sessionId,
    //             'ChallengeResponses' => [
    //                 'USERNAME' => $username,
    //                 'NEW_PASSWORD' => $newPassword,
    //             ],
    //         ]);

    //         return response()->json(['message' => 'Password updated successfully'], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }
}
