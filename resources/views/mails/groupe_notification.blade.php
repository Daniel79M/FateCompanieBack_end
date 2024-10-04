<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Confirmation d'email</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="text-align: center; padding: 20px;">
        <h1>Nouveau Groupe Créé : {{ $group->groupe_name }}</h1>
        <p>Bonjour {{ $member['name'] ?? 'Membre' }},</p>
        <p>Vous avez été ajouté au groupe "{{ $group->groupe_name }}" récemment créé.</p>
        <p>Actualité du groupe : {{ $group->groupe_actu }}</p>
        <p><a href="http://127.0.0.1:8000/api/FATE.v1.0.0/register">Inscrivrez-vous</a></p>
    </div>
</body>

</html>
