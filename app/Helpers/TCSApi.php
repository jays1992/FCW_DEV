<?php 
namespace App\Helpers;
use DB;
use Auth;
use Session;

class TCSApi{

  public static function GenerateIrn($data){
 
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://staging.tcsgsp.in/Tax-Tool-Core/services/auth/einvapi/geneinvandewaybill',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>$data,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer 86413f83-92dc-468b-a05a-d2929e4923ec',
        'clientCode: EINV01',
        'gstin: 29AACPC6144K000',
        'Cookie: JSESSIONID=LE-TyoA_1mvCF72y9uUmzq0oevn3JN2lrda1tfpS.g186070w'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response;
    
  }



  public static function GetInvoice($IRN){

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://einvoicing.internal.cleartax.co/v2/eInvoice/get?irn='.$IRN,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087',
        'x-cleartax-product: EInvoice',
        'gstin: 05AAFCD5862R012'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response; 
  }

  public static function CancelIRN($IRN){


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://staging.tcsgsp.in/Tax-Tool-Core/services/auth/einvapi/cancel',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
                    "irn":"'.$IRN.'",
                    "cnlResCd":"2",
                    "cnlRem":"entry mistake"
                    }',
  CURLOPT_HTTPHEADER => array(
    'gstin: 29AACPC6144K000',
    'Content-Type: application/json',
    'Authorization: Bearer 86413f83-92dc-468b-a05a-d2929e4923ec',
    'clientCode: EINV01'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;
 

  }

  public static function PrintIrn($data){

     $curl = curl_init();
     //return $data["SINO"]; 
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://staging.tcsgsp.in/Tax-Tool-Core/services/auth/einvapi/generatePdfForEinv',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{"irn": "'.$data["IRN_NO"].'","docNo": "'.$data["SINO"].'","docType": "INV"
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer 86413f83-92dc-468b-a05a-d2929e4923ec',
        'clientCode: EINV01',
        'gstin: 29AACPC6144K000'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);   
    
    return $response; 
  }

  public static function SendInvoice($data){
    
    $curl = curl_init();    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://einvoicing.internal.cleartax.co/v0/communication/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "attachment_details": {
        "invoice_details": {
          "invoice_id": "'.$data["DOC_NO"].'",
          "invoice_date": "'.$data["DOC_DT"].'",
          "invoice_type": "INV"
        }
      },
      "communication_details": {
        "template": {
          "template_type": "INVOICE_GENERATED"
        },
        "contacts": [
          {
            "recipients": [
              {
                "name": "'.$data["BUYER_NAME"].'",
                "email": "'.$data["EMAIL"].'",
                "email_recipient_type": "TO"
              }
            ]
          }
        ]
      }
    }',
      CURLOPT_HTTPHEADER => array(
        'x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087',
        'gstin: 05AAFCD5862R012',
        'user-agent: Pothera ERP',
        'Content-Type: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response;
    
  }

}