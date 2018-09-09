<?php

namespace LaraPay\Http\Controllers;

use Illuminate\Http\Request;
use LaraPay\Card;
use Openpay;

class PaymentsController extends Controller
{

	public function __construct()
	{
		$this->id = env('OPEN_PAY_ID');
		$this->secret = env('OPEN_PAY_SECRET');
		$this->middleware('auth');
	}

	public function index(Request $request)
	{
		try {

			Openpay::setSandboxMode(true);
			$openpay = Openpay::getInstance($this->id, $this->secret);

			$cliente = array(
				'name' => $request->get('nombre'),
				'last_name' => $request->get('apellidos'),
				'phone_number' => $request->get('telefono'),
				'email' => $request->get('correo'),
			);
			$pago = (float)preg_replace('/[^0-9]+/', '',$request->get('amount')) ;
			$datos = array(
				'method' => 'card',
				'source_id' => $request->get('token_id'),
				'amount' => $pago,
				'description' => $request->get('description'),
				'device_session_id' => $request->get('deviceIdHiddenFieldName'),
				'customer' => $cliente,
			);

			if ($openpay->charges->create($datos)) {
				return view('home')->with('success','El cobro ha sido correctamente procesado.');
			}
			else {
				return view('home')->with('error','A ocurrido un erorr, no se ha podido realizar el cobro.');
			}
		
			
		} catch (OpenpayApiTransactionError $e) {
			error_log('ERROR on the transaction: ' . $e->getMessage() . 
				' [error code: ' . $e->getErrorCode() . 
				', error category: ' . $e->getCategory() . 
				', HTTP code: '. $e->getHttpCode() . 
				', request ID: ' . $e->getRequestId() . ']', 0);

		} catch (OpenpayApiRequestError $e) {
			error_log('ERROR on the request: ' . $e->getMessage(), 0);

		} catch (OpenpayApiConnectionError $e) {
			error_log('ERROR while connecting to the API: ' . $e->getMessage(), 0);

		} catch (OpenpayApiAuthError $e) {
			error_log('ERROR on the authentication: ' . $e->getMessage(), 0);

		} catch (OpenpayApiError $e) {
			error_log('ERROR on the API: ' . $e->getMessage(), 0);

		} catch (Exception $e) {
			error_log('Error on the script: ' . $e->getMessage(), 0);
		}
	}

}
