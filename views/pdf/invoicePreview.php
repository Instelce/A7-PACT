<?php
/** @var $pk int */
/** @var $invoice \app\models\payment\Invoice */
/** @var $offer \app\models\offer\Offer */
/** @var $user \app\models\account\UserAccount */
/** @var $professional \app\models\user\professional\ProfessionalUser */
/** @var $professionalAddress \app\models\Address */

use app\core\Application;
use app\core\Utils;

$url = $_ENV['DOMAIN'] . '/offres/' . $offer->id;

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <title>Facture <?php echo $pk ?> - <?php echo Utils::monthConversion($invoice->service_date) ?> - <?php echo $offer->title ?></title>

    <style>
        /* Général */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            color: #333;
        }

        header {
            margin-top: 5rem;
            padding-bottom: 2rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            font-size: 4rem;
            line-height: 0;
            font-weight: 400;
            letter-spacing: -1px;
        }

        header img {
            position: absolute;
            right: 0;
            top: 2.5rem;
        }

        .badge {
            margin-right: .5rem;
            padding: .5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: .6rem;
            text-align: left;
            line-height: 100%;
        }

        td[colspan="2"] {
            border: none;
            background: none;
        }

        th {
            color: #d032e7;
            border: 1px solid #e4b8e9;
            background-color: #faebfd;
            font-weight: bold;
            text-transform: uppercase;
            font-size: .8rem;
        }

        .sous-total {
            color: #d032e7;
            background-color: #faebfd;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .title {
            text-align: center;
            font-weight: 500;
            margin-top: 4rem;
            margin-bottom: 2rem;
        }

        .address {
            line-height: 30%;
        }

        .address-receiver {
            text-align: right;
            position: absolute;
            top: 13rem;
            right: 0;
        }

        a {
            text-decoration: underline;
            color: inherit;
        }

        footer {
            line-height: 30%;
        }

        footer .left {
            position: absolute;
            bottom: 0;
            left: 0;
        }

        footer .right {
            position: absolute;
            bottom: 0;
            right: 0;
            text-align: right;
        }
    </style>
</head>
<body>

    <header>
        <h1>FACTURE</h1>

        <span class="badge">N° <?php echo $pk ?></span>
        <span class="badge">Lannion</span>
        <span class="badge"><?php echo date('d/m/Y', strtotime($invoice->issue_date)) ?></span>
        <span class="badge"><?php echo $offer->title ?></span>

        <img src="data:image/svg+xml;base64,<?php echo base64_encode(file_get_contents(Application::assets('/images/logoPro.svg'))) ?>" alt="Logo">
    </header>

    <div>
        <div class="address">
            <h3>SARL PACT</h3>
            <p>25, Rue de la Boutade</p>
            <p>22300, Lannion</p>
            <p>02 96 01 10 51</p>
        </div>

        <div class="address address-receiver">
            <h3>FACTURÉ À</h3>
            <p><?php echo $professional->denomination ?></p>
            <p><?php echo $professionalAddress->number . ' ' . $professionalAddress->street . ' , ' . $professionalAddress->postal_code . ' ' . $professionalAddress->city; ?></p>
            <p>SIREN : <?php echo $professional->siren ?></p>
            <p>Tél : <?php echo $professional->phone ?></p>
        </div>
    </div>

    <h2 class="title">
        Facturation de l'offre <a href="<?php echo $url ?>"><?php echo $offer->title ?></a> pour le mois de <?php echo Utils::monthConversion($invoice->service_date) ?>
    </h2>

    <table>
        <thead>
            <tr>
                <th>Prestations</th>
                <th>Qté</th>
                <th>Prix HT</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Offre Standard / jour</td>
                <td>30</td>
                <td>1.67€</td>
                <td>50.1€</td>
            </tr>
            <tr>
                <td>Option "En relief" / semaine</td>
                <td>1</td>
                <td>8.34€</td>
                <td>8.34€</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td>Sous-total</td>
                <td>58.44€</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td>Total TVA 20%</td>
                <td>11.688€</td>
            </tr>
            <tr class="sous-total">
                <td colspan="2"></td>
                <td>Prix TTC</td>
                <td>70.128€</td>
            </tr>
        </tbody>
    </table>

    <footer>
        <div class="left">
            <strong>Payement à l'ordre de de <?php echo $professional->denomination ?></strong>
            <p>N° de compte</p>
        </div>

        <div class="right">
            <strong>Conditions de paiement</strong>
            <p>Paiement sous 30 jours</p>
        </div>
    </footer>
</body>
</html>