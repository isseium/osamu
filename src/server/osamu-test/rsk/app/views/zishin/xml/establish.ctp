<?php header("Content-Type: text/xml"); ?>

<respons>
    <?php echo $xml->serialize($recipe); ?>
</respons>