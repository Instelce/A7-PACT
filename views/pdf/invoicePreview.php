<?php
/** @var $pk int */
/** @var $invoice \app\models\payment\Invoice */
/** @var $offer \app\models\offer\Offer */
/** @var $user \app\models\account\UserAccount */
/** @var $professional \app\models\user\professional\ProfessionalUser */
/** @var $professionalAddress \app\models\Address */
/** @var $subscription  \app\models\offer\Subscription */
/** @var $type \app\models\offer\OfferType */


use app\core\Application;
use app\core\Utils;

$url = $_ENV['DOMAIN'] . '/offres/' . $offer->id;

$offerPrice = 30 * $type->price;

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

        /* Totals */

        .totals {
            margin-top: 20px;
        }

        .totals-table {
            width: 50%;
            border-spacing: 0;
            margin-left: 22rem;
        }

        .totals-table td,
        .totals-table th,
        .totals-table{
            border: none;
        }

        .totals-table td {
            padding: 5px;
            text-align: right;
        }

        .totals-table .total {
            font-weight: bold;
            color: #d032e7;
        }

        /* Footer */

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

        .payment-terms {
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            margin-top: 40px;
        }

        .payment-terms h3 {
            color: #d032e7;
            font-size: 18px;
            padding-bottom: 10px;
        }

        .payment-terms p {
            margin: 5px 0;
            font-size: 14px;
        }

        .contact {
            /* background-color: #333;*/
            position: absolute;
            bottom: 0;
            right: 28%;
            color: #333;
            text-align: center;
            padding: 10px 10px;
            font-size: 12px;
            margin-top: 50px;
        }

        .contact p {
            padding: 5px;
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

    <div>
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
                <td>Offre "<?php echo $type->type ?>" / jour</td>
                <td>Nombre de jours</td>
                <td><?php echo $type->price ?> €</td>
                <td><?php echo $offerPrice ?> €</td>
            </tr>
            <?php if ($subscription) {
                $optionPrice = $subscription->duration * $subscription->price();

                $sousTotal = $offerPrice;
                if($subscription){
                    $sousTotal = $offerPrice + $optionPrice;
                }?>
                <tr>
                    <td>Option "<?php echo $subscription->option()->french() ?>" / semaine</td>
                    <td><?php echo $subscription->duration ?></td>
                    <td><?php echo $subscription->price() ?></td>
                    <td><?php echo $optionPrice ?> €</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Sous-total</td>
                    <td><?php echo $sousTotal ?> €</td>
                </tr>
                <tr>
                    <td>Total TVA 20%</td>
                    <td><?php echo $sousTotal * 0.2 ?> €</td>
                </tr>
                <tr>
                    <td class="total">TOTAL TTC</td>
                    <td class="total"><?php echo $sousTotal + $sousTotal*0.2 ?> €</td>
                </tr>
            </table>
        </div>
    </div>




    <footer>
        <div class="payment-terms">
            <h3>Conditions et modalités de paiement</h3>
            <p>Le paiement est dû à <?php echo $professional->denomination ?> dans 30 jours à compter de la date de facture.</p>
        </div>

        <div class="contact">
            <p>TEL : 02 96 01 10 51 | FAX : 00212535-00-00-00</p>
            <p>IBAN : FR15 1265 9574 | BIC : 0123456789</p>
            <p>Merci de votre confiance.</p>
        </div>
    </footer>
</body>
</html>