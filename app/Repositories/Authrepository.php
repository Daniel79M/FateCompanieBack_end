<?php

namespace App\Repositories;

use App\Interfaces\AuthInterface;
use App\Mail\OtpCodeEmail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Authrepository implements AuthInterface
{
    /**
     * Create a new class instance.
     */
    
    public function register(array $data)
    {
        $user = User::create($data);

    

        $otp_code = [
            'email' => $data['email'],
            'code' => rand(111111, 999999),
        ];

        OtpCode::where('email', $data['email'])->delete();
        OtpCode::create($otp_code);
        Mail::to($data['email'])->send(new OtpCodeEmail(
            $data['name'],
            $data['email'],
            $otp_code['code']
        ));

        return $user;
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user)
            return false;

        if (!Hash::check($data['password'], $user->password))
            return false;

        $user->tokens()->delete();
        $user->token = $user->createToken($user->id)->plainTextToken;

        return $user;
    }

    public function checkOtpCode($data)
    {
        $otp_code = OtpCode::where('email', $data['email'])->first();

        if (!$otp_code)
            return false;

        if (Hash::check($data['code'], $otp_code['code'])) {

            $user = User::where('email', $data['email'])->first();
            $user->update(['is_confirmed' => true]);

            $otp_code->delete();

            $user->token = $user->createToken($user->id)->plainTextToken;
            return $user;

        }

        return false;
    }
}
