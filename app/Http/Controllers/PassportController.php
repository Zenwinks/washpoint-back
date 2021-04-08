<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PassportController extends Controller
{
  /**
   * Handles Registration Request
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request)
  {
    $this->validate($request, [
      'firstname' => 'required',
      'lastname' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required'
    ]);
    try {
      $user = new User();
      DB::transaction(function () use ($user, $request) {
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->roles = json_encode([]);
        $user->email = $request->email;
        $user->remember_token = base64_encode(Hash::make(uniqid()));
        $user->password = Hash::make($request->password);
        $user->save();
      });
      $user->notify(new AccountCreated($user->email, $user->getRememberToken()));
      Log::channel('info')->info('L\'utilisateur ' . $user->email . ' a finalisé son inscription');
      return response()->json('Utilisateur enregistré');
    } catch (\Exception $e) {
      Log::channel('errorUser')->error('Une erreur est survenue lors de l\'enregistrement d\'un utilisateur' . PHP_EOL . $e);
      return response()->json(['error' => 'Une erreur est survenue lors de l\'enregistrement de l\'utilisateur'], 500);
    }
  }

  public function validateRegistration(Request $request)
  {
    $this->validate($request, [
      'token' => 'required'
    ]);
    try {
      $user = User::where('remember_token', $request->token)->first();
      if ($user) {
        DB::transaction(function () use ($user, $request) {
          if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
          } else {
            Log::channel('info')->info("L'utilisateur " . $user->email . " a déjà validé son email !");
          }
        });
        return response()->json(['user' => $user]);
      } else {
        Log::channel('errorUser')->error('Utilisateur non trouvé avec le token donnée : ' . $request->token . '.');
        return response()->json(['error' => "Une erreur est survenue lors de la validation de l\'enregistrement de l\'utilisateur"], 500);
      }
    } catch (\Exception $e) {
      Log::channel('errorUser')->error('Une erreur est survenue lors de la validation de l\'enregistrement d\'un utilisateur' . PHP_EOL . $e);
      return response()->json(['error' => "Une erreur est survenue lors de la validation de l\'enregistrement de l\'utilisateur"], 500);
    }
  }

  /**
   * Handles Login Request
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {
    try {
      $credentials = [
        'email' => $request->email,
        'password' => $request->password
      ];
      if (auth()->attempt($credentials)) {
        //check if auth as role admin si c'est le cas filer le scope
        if (in_array('ROLE_ADMIN', json_decode(auth()->user()->roles))) {
          $token = auth()->user()->createToken($request->email)->accessToken;
        } else {
          $token = auth()->user()->createToken($request->email)->accessToken;
        }
        $user = User::where('id', auth()->user()->id)->first();
        return response()->json(['accessToken' => $token, 'user' => $user], 200, ['Content-Type: application/json']);
      } else {
        Log::channel('errorAccess')->error("Tentative d'accès infructueux (" . $request->email . ")" . PHP_EOL);
        return response()->json(['error' => 'Identifiants inconnus'], 401);
      }
    } catch (\Exception $e) {
      Log::channel('errorAccess')->error('Erreur lors du login' . PHP_EOL . $e);
      return response()->json(['error' => 'Erreur'], 500);
    }
  }

  public function logout()
  {
    try {
      auth()->user()->tokens->each(function ($token, $key) {
        $token->delete();
      });
    } catch (\Exception $e) {
      Log::channel('errorPassport')->error('La déconnexion de l\'utilisateur ' . auth()->user()->id . ' a échoué.' . PHP_EOL . $e);
    }
  }

  /**
   * Returns Authenticated User Details
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function details()
  {
    return response()->json(['user' => auth()->user()], 200);
  }
}
