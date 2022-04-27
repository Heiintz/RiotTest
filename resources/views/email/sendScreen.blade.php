<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    Bonjour,
    <br><br>
    Vous trouverez ci-joint les informations liées à la commande ainsi qu'une capture d'écran en pièce jointe.
    <br><br>
    <table>
        <thead>
            <tr>
                <td>Référence de la commande</td>
                <td>OC</td>
                <td>OI</td>
            </tr>
        </thead>
        <tbody>
            <td>{!!$reference!!}</td>
            <td>{!!$oc!!}</td>
            <td>{!!$oi!!}</td>
        </tbody>
    </table>
    <br><br>
    Cordialement,
    <br>
    IFTools
</body>
</html>
<style type="text/css" rel="stylesheet" media="all">
    td {
        border: 1px black solid;
        padding:8px;
    }
</style>
