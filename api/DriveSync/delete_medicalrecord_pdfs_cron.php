<?php
 array_map('unlink', ( glob( "../mobileMedicalRecords/*" ) ? glob( "../mobileMedicalRecords/*" ) : array() ) );
?>