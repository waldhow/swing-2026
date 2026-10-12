<?php

/**
 * Felmeddelanden för de publika formulären. Används både av servern
 * (controllers, SwingRules) och av valideringen i webbläsaren (data-msg-*),
 * så att texterna alltid är desamma.
 */
return [
    'pickPot'      => 'Välj vilken pott du vill beställa.',
    'pickAmount'   => 'Välj ett belopp mellan {min} och {max}.',
    'acceptTerms'  => 'Du behöver godkänna köpvillkoren.',
    'pnrFormat'    => 'Ange personnummer som ÅÅÅÅMMDD-NNNN.',
    'pnrChecksum'  => 'Personnumret verkar inte stämma — kontrollera siffrorna.',
    'phone'        => 'Ange ett giltigt telefonnummer.',
    'participants' => 'Ange antal personer som ett tal mellan 1 och {max}.',
];
