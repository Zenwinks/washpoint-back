<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <title>Compte créé sur Washpoint</title>
</head>
<style>
  img {
    height: 100px;
    width: 160px;
    margin-left: 38%;
  }

  body {
    font-family: Georgia, serif;
    font-size: 24px;
    height: 100%;
    width: 100%;
    color: black;
    margin-left: 10%;
    margin-right: 10%;
  }

  .main {
    width: 80%;
    padding: 5px;
  }

  .greeting, .signature {
    background-color: rgba(0, 151, 230, 0.8);
    padding: 5px;
    color: white;
  }

  .content {
    padding: 5px;
  }

  .message, .thanks, .endGreeting {
    margin-left: 15px
  }

  .signature {
    width: 100%;
    text-align: end;
  }

  a {
    width: 100%;
    margin: auto;
    text-decoration: unset;
  }

  .action {
    width: 200px;
    margin: auto;
    cursor: pointer;
    background-color: rgba(0, 151, 230, 0.8);
    color: white;
    padding: 20px;
    text-align: center;
  }
</style>
<body>
<div class="header">
  <img src="{{asset('logo.png')}}" alt="logo" class="logo">
</div>
<div class="main">
  <p class="greeting">Bonjour,</p>
  <div class="content">
    <p class="message">Vous venez de créer un compte sur l'application Washpoint et nous vous en remercions. Activez
      votre compte dès maintenant afin de pouvoir bénéficier pleinement des fonctionnalités proposées.</p>

    <a href="{{env('APP_FRONT_URL') . 'register/' . $token}}">
      <div class="action">
        <span>J'active mon compte</span>
      </div>
    </a>

    <p class="message">Si vous pensez que cet email ne vous est pas destiné, merci de l'ignorer.</p>
    <br>
    <p class="endGreeting">Cordialement,</p>
  </div>
  <p class="signature">{{$signature}}</p>
</div>
</body>
</html>
