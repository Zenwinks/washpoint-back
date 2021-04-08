<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
  public function updateUser(Request $request)
  {
    $this->validate($request, [
      'id' => 'required',
      'firstname' => 'required',
      'lastname' => 'required',
      'email' => 'required|email'
    ]);
    try {
      //Test unicité email
      $userMail = User::where('email', $request->email)->first();
      if ($userMail && $userMail->id !== $request->id) {
        Log::channel('errorUser')->error('Cette adresse email est déjà utilisée !');
        return response()->json(['error' => 'Cette adresse email est déjà utilisée !'], 409);
      } else {
        //MAJ du user
        $user = User::find($request->id);
        DB::transaction(function () use ($user, $request) {
          $user->firstname = $request->firstname;
          $user->lastname = $request->lastname;
          $user->email = $request->email;
          $user->save();
        });
        Log::channel('info')->info('Utilisateur ' . $user->id . ' mis à jour avec succès.');
        return response()->json(['user' => $user]);
      }
    } catch (\Exception $e) {
      Log::channel('errorUser')->error('Une erreur est survenue pendant la modification d\'un utilisateur' . PHP_EOL . $e);
      return response()->json(['error' => 'Une erreur est survenue pendant la modification d\'un utilisateur'], 500);
    }
  }

  public function updatePassword(Request $request)
  {
    $this->validate($request, [
      'id' => 'required',
      'actualPwd' => 'required',
      'newPwd' => 'required'
    ]);
    try {
      $sql = DB::table('users')->where('id', '=', $request->id)->first();
      if (Hash::check($request->actualPwd, $sql->password)) {
        $user = User::find($request->id);
        DB::transaction(function () use ($user, $request) {
          $user->password = Hash::make($request->newPwd);
          $user->save();
        });
        Log::channel('info')->info('Le mot de passe a bien été changé.');
        return response()->json('Le mot de passe a bien été changé.');
      } else {
        Log::channel('errorUser')->error('Le mot de passe donné est incorrect');
        return response()->json(['error' => 'Le mot de passe donné est incorrect'], 500);
      }
    } catch (\Exception $e) {
      Log::channel('errorUser')->error('Une erreur est survenue à la modification du mot de passe de l\'utilisateur ' . $request->id . PHP_EOL . $e);
      return response()->json(['error' => 'Une erreur est survenue à la modification du mot de passe'], 500);
    }
  }
}
