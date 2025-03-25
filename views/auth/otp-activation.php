<?php



?>


<img src="<?php echo $qrCodeUri ?>" alt="">

<x-input>
    <input slot="input" id="otp_input" type="text" placeholder="Entrez votre code d'authentification" maxlength="6">
</x-input>
<button type="submit" class="button">Vérifier</button>
