<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\Company;

class OtpController extends Controller
{
      public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $otpCode = rand(100000, 999999);

        Otp::create([
            'email' => $request->email,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        Mail::raw("Your OTP code is: $otpCode", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Your OTP Code');
        });

        return response()->json(['message' => 'OTP sent successfully']);
    }

     public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp_code' => 'required|string'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->is_verified = true;
            $user->save();
        } else {
            $company = Company::where('email', $request->email)->first();
            if ($company) {
                $company->is_verified = true;
                $company->save();
            } else {
                return response()->json(['message' => 'No user or company found with this email'], 404);
            }
        }

        $otp->delete();

        return response()->json(['message' => 'OTP verified successfully, account activated']);
    }

}
