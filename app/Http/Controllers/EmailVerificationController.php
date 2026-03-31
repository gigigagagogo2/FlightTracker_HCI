<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Mail\VerifyEmailMail;
use App\Mail\WelcomeMail;

class EmailVerificationController extends Controller
{
    // Pagina "controlla la tua email"
    public function notice()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect('/');
        }
        return view('auth.verify-email');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            abort(403);
        }

        if (!$request->hasValidSignature()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Il link è scaduto. Richiedine uno nuovo.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            // Invia email di benvenuto dopo la verifica
            Mail::to($user->email)->send(new WelcomeMail($user));
        }

        return redirect('/')->with('status', 'Email verificata. Benvenuto su FlightTracker!');
    }

    // Reinvia email di verifica
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/');
        }

        $user = $request->user();
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new VerifyEmailMail($verifyUrl));

        return back()->with('status', 'Email di verifica inviata.');
    }

    // Invia al momento della registrazione
    public static function sendVerificationEmail(User $user): void
    {
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new VerifyEmailMail($verifyUrl));
    }
}
