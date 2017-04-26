<?php 

//Configuracion de entorno
define('SERVER','http://pre.pagoefectivo.pe/');
//Aqui colocar la ip del server donde se van a colocar los archivos
//Si se estan realizando pruebas - colocar su IP LOCAL
define('IP_SERVER','199.19.116.102');

//Rutas de Webservices
$wsCrypta= SERVER.'PagoEfectivoWSCrypto/WSCrypto.asmx';
define('WSCRYPTA', $wsCrypta.'?wsdl');		
$wsCIP= SERVER.'PagoEfectivoWSGeneral/WSCIP.asmx';
define('WSCIP', $wsCIP.'?wsdl');
$wsGenPago = SERVER.'GenPago.aspx';
define('WSGENPAGO', $wsGenPago);
//Configuracion de cuenta
//Aqui iran las claves que les proporcionemos - estan son de prueba
define('CAPI','e077122e-bac6-42db-a993-5df6cfe2a36f');
define('CCLAVE','950baee9-104f-47a0-8210-5e8f2c572e4c');

//Mail de la persona a la que le llegara el mail en la prueba de generacion de cip
//Este mail es de prueba, al final en vez de esta constante - se reemplazará con el mail del cliente
define('EMAIL_CONTACTO','likerow@gmail.com');


//Este dato es unico por servicio - nosotros se lo proporcionaremos
define('MERCHAN_ID','SCB');
//Nombre del concepto de Pago que acompaña al numero de pedido en el banco 
define('COMERCIO_CONCEPTO_PAGO','PRUEBA');
//El dominio de pruebas o produccion al que solicitaron permisos por IP
define('DOMINIO_COMERCIO',  Cit_Server::getContent()->host);

//Colocar la url de notificacion, luego enviarnoslas para configurarlas
define('URL_NOTIFICACION',DOMINIO_COMERCIO.'urlnotificacion.php');

//ubicacion y nombre de los archivos a usar
define('PATH',dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('SECURITY_PATH',PATH  ."key");
//Estos archivos se los enviara PagoEfectivo
//nombre del archivo clave publica de PagoEfectivo
define('PUBLICKEY', "SPE_PublicKey.1pz");
//nombre del archivo clave privada del comercio
define('PRIVATEKEY', "SCB_PrivateKey.1pz");

define('MEDIO_PAGO','1');

?>