<?php 

namespace App\Helpers;
use DB;
use Auth;
use Session;
use Ixudra\Curl\Facades\Curl;


class ClearTaxApi{
  public static function GenerateIrn($data){
          
$response = Curl::to('https://einvoicing.internal.cleartax.co/v2/eInvoice/generate')
            ->withData($data)
            ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
            ->withHeader('x-cleartax-product: B2B')
		        ->withHeader('Content-Type: application/json')
		        ->withHeader('gstin: 05AAFCD5862R012')
            ->put();
	        
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
    $response = Curl::to('https://einvoicing.internal.cleartax.co/v2/eInvoice/cancel')
            ->withData('[
                {
                  "irn": "'.$IRN.'",
                  "CnlRsn": "1",
                  "CnlRem": "Wrong"
                }
                ]')
            ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
            ->withHeader('x-cleartax-product: EInvoice')
		        ->withHeader('Content-Type: application/json')
		        ->withHeader('gstin: 05AAFCD5862R012')
            ->put();
	        
      return $response;

  }
  

  public static function PrintIrn($IRN){
    $response = Curl::to('https://einvoicing.internal.cleartax.co/v2/eInvoice/download?irns='.$IRN.'&template=6e351b87-35b4-48a5-bc5f-d085685410f7')
            //->withData($data)
            ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
            ->withHeader('x-cleartax-product: EInvoice')
		        ->withHeader('Content-Type: application/json')
		        ->withHeader('gstin: 05AAFCD5862R012')
            ->get();
	        
      return $response; 
  }
  

  public static function SendInvoice($data){    
$response = Curl::to('https://einvoicing.internal.cleartax.co/v0/communication/send')
            ->withData('{
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
    }')
            ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
            ->withHeader('x-cleartax-product: EInvoice')
		        ->withHeader('Content-Type: application/json')
		        ->withHeader('gstin: 05AAFCD5862R012')
            ->post();
	        
      return $response;
    
  }
  


  //===========================================================EWAYBILL SECTION STARTS HERE ==========================================================

  public static function GenerateEwayBill($data){

    $response = Curl::to('https://einvoicing.internal.cleartax.co/v2/eInvoice/ewaybill')
    ->withData($data)
    ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
    ->withHeader('x-cleartax-product: EInvoice')
    ->withHeader('Content-Type: application/json')
    ->withHeader('gstin: 05AAFCD5862R012')
    ->post();
  
    return $response;

  }


  
  public static function CancelEway($EWAY_NO)
  {
    $response = Curl::to('https://einvoicing.internal.cleartax.co/v2/eInvoice/ewaybill/cancel')
            ->withData('{
                "ewbNo": '.$EWAY_NO.',
                "cancelRsnCode": "DATA_ENTRY_MISTAKE",
                "cancelRmrk" : "DATA_ENTRY_MISTAKE"
            }')
            ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
            ->withHeader('x-cleartax-product: EInvoice')
		        ->withHeader('Content-Type: application/json')
		        ->withHeader('gstin: 05AAFCD5862R012')
            ->post();
	        
      return $response;

  }



  public static function PrintEway($EWAY_NO){
    $response = Curl::to('https://einvoicing.internal.cleartax.co/v2/eInvoice/ewaybill/print?ewb_numbers='.$EWAY_NO.'&print_type=BASIC&format=PDF')
    ->withData('{
        "ewb_numbers": [
          '.$EWAY_NO.'
        ],
        "print_type": "DETAILED"
      }')
      
    ->withHeader('x-cleartax-auth-token: 1.9745c92c-c89d-4e21-bfda-03d6896b4549_932895328bf5b8b57a9a510c461c21c5b6ec2eecc4b22f9ed12f9d39060ea087')
    ->withHeader('x-cleartax-product: EInvoice')
    ->withHeader('Content-Type: application/json')
    ->withHeader('gstin: 05AAFCD5862R012')
    ->post();
    //dd($response); 
  
return $response;
    
  }
  

//===================================================EWAYBILL SECTION ENDS HERE =======================================================

}