<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class MagicLinkController extends Controller
{
    public function showMagicLinkForm()
    {
        return view('auth.magic-link');
    }

    public function sendMagicLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        $token = Hash::make(Str::random(60));

        $expires = Carbon::now()->addMinutes(30);

        $user->update([
            'magic_link_token' => $token,
            'magic_link_expires_at' => $expires,
        ]);

        $url = route('magic.login', ['token' => $token, 'email' => $user->email]);

        Mail::to($user->email)->send(new \App\Mail\MagicLinkMail($url));

        return back()->with('status', 'Magic link sent! Please check your email and close this window.');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('magic_link_token', $request->token)
            ->where('magic_link_expires_at', '>', Carbon::now())
            ->first();

        if ($user) {
            auth()->login($user);
            $user->update(['magic_link_token' => null, 'magic_link_expires_at' => null]);
            return redirect('/projects');
        }

        return redirect('/login')->withErrors(['email' => 'Invalid or expired magic link.']);
    }
}
